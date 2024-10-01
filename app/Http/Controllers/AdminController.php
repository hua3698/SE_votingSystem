<?php

namespace App\Http\Controllers;

use App\Models\VoteEvent;
use App\Models\Candidate;
use App\Models\GenerateQrcode;
use App\Models\VoteRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use chillerlan\QRCode\{QRCode, QROptions};
use App\Traits\VoteHelper;

class AdminController extends Controller
{
    use VoteHelper;

    public function adminPage(Request $request) 
    {
        try
        {
            $voteEvents = VoteEvent::orderBy('created_at', 'desc')->get()->map(function ($event) {
                $now = Carbon::now();
                $start = Carbon::parse($event->start_time);
                $end = Carbon::parse($event->end_time);

                if ($now->lessThan($start)) {
                    $event->status = 0; // 未開始
                } elseif ($now->between($start, $end)) {
                    $event->status = 1; // 進行中
                } else {
                    $event->status = 2; // 已結束
                }
                return $event;
            });

            $count = VoteEvent::count();

            $response = [];
            $response['vote_event'] = $voteEvents;
            $response['total'] = $count;

            return view('admin.index', $response);
        }
        catch (\Exception $e) 
        {
            echo $e;
        }
    }

    // 新增投票活動
    public function createVoteEvent(Request $request) 
    {
        try
        {
            $validated = $request->validate([
                'vote_name' => 'required|string|max:255',
                'start' => 'required|date_format:Y-m-d H:i:s',
                'end' => 'required|date_format:Y-m-d H:i:s|after:start',
                'candidates' => 'required|array',
                'candidates.*.number' => 'required|string|min:1|max:10',
                'candidates.*.name' => 'required|string|max:255', 
                'candidates.*.school' => 'required|string|max:255', 
                'qrcode_count' => 'required|integer|min:1',
                'manual_control' => 'required|integer',  // 是否手動控制投票活動
                'max_vote' => 'integer|min:1|max:10', // 每張qrcode最多可以投幾票，目前1~10
                'max_winner' => 'integer|min:1|max:10' // 共有幾位得名者，目前1~10
            ]);

            DB::transaction(function () use ($validated) {
                $voteEvent = new VoteEvent();
                $voteEvent->event_name = $validated['vote_name'];
                $voteEvent->start_time = $validated['start'];
                $voteEvent->end_time = $validated['end'];
                $voteEvent->max_vote_count = $validated['max_vote'];
                $voteEvent->number_of_qrcodes = $validated['qrcode_count'];
                $voteEvent->number_of_candidates = count($validated['candidates']);
                $voteEvent->number_of_winners = $validated['max_winner'];
                $voteEvent->manual_control = $validated['manual_control'];
                $voteEvent->save();

                foreach ($validated['candidates'] as $key => $cand) {
                    $candidate = new Candidate();
                    $candidate->event_id = $voteEvent->event_id;
                    $candidate->number = $cand['number'];
                    $candidate->name = $cand['name'];
                    $candidate->school = $cand['school'];
                    $candidate->save();
                }

                for ($i = 0; $i < $validated['qrcode_count']; $i++) {
                    $generateQrcode = new GenerateQrcode();
                    $generateQrcode->event_id = $voteEvent->event_id;
                    $generateQrcode->qrcode_string = md5($voteEvent->event_id . '_' . ($i + 1));
                    $generateQrcode->save();
                }
            });

            return response()->json(['message' => '投票活動新增成功'], 200);
        }
        catch (\Exception $e) 
        {
            Log::error(sprintf('[%s] %s', __METHOD__, $e->getMessage()));
            return response()->json(['error' => '建立投票活動時發生錯誤。', 'details' => $e->getMessage()], 500);
        }
    }

    // 取得單一投票活動詳細內容
    public function getVoteEvent($event_id)
    {
        $voteEvent = VoteEvent::find($event_id);
        $voteEvent = $this->getVoteStatus($voteEvent);

        if (!$voteEvent) {
            return response()->json(['error' => 'Vote Event not found'], 404);
        }

        $candidates = Candidate::where('event_id', $event_id)->get();
        $qrcodes = $this->getQrcodeInfo($event_id);

        $response = [];
        $response = [
            'vote_event' => $voteEvent,
            'candidates' => $candidates,
            'qrcodes' => $qrcodes,
        ];

        return view('admin.vote', $response);
    }

    public function voteEventEditPage($event_id)
    {
        try {
            $voteEvent = VoteEvent::find($event_id);
            $voteEvent = $this->getVoteStatus($voteEvent);

            //檢查當前狀態是否可以編輯 (only allow 尚未開始階段)
            $this->validEditPermission($voteEvent);

            $candidates = Candidate::where('event_id', $event_id)->get();

            $response = [];
            $response = [
                'vote_event' => $voteEvent,
                'candidates' => $candidates,
            ];
            return view('admin.voteedit', $response);
        }
        catch (\Exception $e) 
        {
            Log::error(sprintf('[%s] %s', __METHOD__, $e->getMessage()));
            return view('hello');
        }
    }

    public function editVoteEvent($event_id)
    {
        return view('hello');
    }

    public function generatePDF($event_id)
    {
        $voteEvent = VoteEvent::find($event_id);
        $candidates = Candidate::where('event_id', $event_id)->get();
        $qrcodes = $this->getQrcodeInfo($event_id);

        // 初始化 DOMPDF
        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans'); // 使用默認字型作為備用字型
        $options->setIsHtml5ParserEnabled(true);
        $options->setIsFontSubsettingEnabled(true);

        $dompdf = new Dompdf($options);

        // 載入 HTML 模板，並傳遞數據
        $html = view('pdf.voteticket', compact('voteEvent', 'candidates', 'qrcodes'))->render();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // 輸出 PDF
        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $voteEvent->event_name . '_qrcode.pdf"');
    }

    public function testPDF($event_id)
    {
        $voteEvent = VoteEvent::find($event_id);
        $candidates = Candidate::where('event_id', $event_id)->get();
        $qrcodes = $this->getQrcodeInfo($event_id);

        $response = [];
        $response = [
            'voteEvent' => $voteEvent,
            'candidates' => $candidates,
            'qrcodes' => $qrcodes,
        ];

        return view('pdf.voteticket', $response);
    }

    private function getQrcodeInfo($event_id)
    {
        $qrcodes = GenerateQrcode::where('event_id', $event_id)->get();
        foreach ($qrcodes as $key => $qrcode) {
            $url = config('app.url') . 'vote/' . $event_id . '/' . $qrcode->qrcode_string; 
            $images = (new QRCode)->render($url);
            $qrcodes[$key]->qrcode_url = $images;
        }

        return $qrcodes;
    }

    public function activateVoteEvent(Request $request)
    {
        try {
            $validated = $request->validate([
                'event_id' => 'required|integer',
            ]);

            $voteEvent = VoteEvent::find($validated['event_id']);

            if ($voteEvent) {
                $this->validActivatePermission($voteEvent);

                $voteEvent->vote_is_ongoing = 1;
                $voteEvent->save();
                return response()->json(['message' => '投票活動已啟用'], 200);
            }

            return response()->json(['message' => '投票活動不存在'], 404);
        }
        catch (\Exception $e) 
        {
            Log::error(sprintf('[%s] %s', __METHOD__, $e->getMessage()));
            return view('hello');
        }
    }

    // 停用投票活動
    public function deactivateVoteEvent(Request $request)
    {
        try {
            $validated = $request->validate([
                'event_id' => 'required|integer',
            ]);

            $voteEvent = VoteEvent::find($validated['event_id']);

            if ($voteEvent) {
                $this->validDeactivatePermission($voteEvent);

                $voteEvent->vote_is_ongoing = 2;
                $voteEvent->save();
                return response()->json(['message' => '投票活動已停用'], 200);
            }

            return response()->json(['message' => '投票活動不存在'], 404);
        }
        catch (\Exception $e) 
        {
            Log::error(sprintf('[%s] %s', __METHOD__, $e->getMessage()));
            return view('hello');
        }
    }

    public function checkVoteSituation($event_id)
    {
        $voteEvent = VoteEvent::find($event_id);
        $voteEvent = $this->getVoteStatus($voteEvent);
        $voted_qrcodes = GenerateQrcode::getVotedQrcodes($event_id);

        $response = [];
        $response = [
            'vote_event' => $voteEvent,
            'qrcodes' => $voted_qrcodes,
            'system_time' => date('Y-m-d H:i:s', time())
        ];

        return view('admin.votecheck', $response);
    }

    // ajax每20秒取資料
    public function postCheckVoteSituation($event_id)
    {
        $voted_qrcodes = GenerateQrcode::getVotedQrcodes($event_id);

        $result = [];
        $result = [
            'qrcodes' => $voted_qrcodes,
            'system_time' => date('Y-m-d H:i:s', time())
        ];

        return response()->json($result);
    }

    public function getVoteResult($event_id)
    {
        try {
            $voteEvent = VoteEvent::find($event_id);
            $this->validGetResultPermission($voteEvent);

            $rank = Candidate::getCandidateRanking($event_id);
            $voteRecord = $this->getVoteRecord($event_id);

            $response = [];
            $response = [
                'vote_event' => $voteEvent,
                'rank' => $rank,
                'records' => $voteRecord
            ];
            return view('admin.voteresult', $response);
        }
        catch (\Exception $e) 
        {
            Log::error(sprintf('[%s] %s', __METHOD__, $e->getMessage()));
            return view('hello');
        }
    }

    private function getVoteRecord($event_id)
    {
        $results = VoteRecord::getVoteDetails($event_id);

        $groupedResults = [];

        foreach ($results as $row) {
            if (!isset($groupedResults[$row->code_id])) {
                $groupedResults[$row->code_id] = [
                    'code_id' => $row->code_id,
                    'qrcode_string' => $row->qrcode_string,
                    'updated_at' => $row->updated_at,
                    'vote' => [],
                ];
            }

            $groupedResults[$row->code_id]['vote'][] = [
                'number' => $row->number,
                'name' => $row->name,
                'school' => $row->school
            ];
        }

        // 按 number 排序
        foreach ($groupedResults as &$group) {
            usort($group['vote'], function ($a, $b) {
                return $a['number'] <=> $b['number'];
            });
        }

        $groupedResults = array_values($groupedResults);
        return $groupedResults;
    }

    public function exportDetail($event_id)
    {
        $voteRecord = $this->getVoteRecord($event_id);

        $response = [];
        $response['records'] = $voteRecord;
        return view('pdf.votedetail', $response);
    }

}
