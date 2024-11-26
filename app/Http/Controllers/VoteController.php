<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VoteEvent;
use App\Models\Candidate;
use App\Models\GenerateQrcode;
use App\Models\VoteRecord;
use App\Traits\VoteHelper;
use App\Constants\VoteStatus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use chillerlan\QRCode\{QRCode, QROptions};
use Dompdf\Dompdf;
use Dompdf\Options;

class VoteController extends Controller
{
    use VoteHelper;

    private $voteEvent;
    private $generateQrcode;

    public function showVotes()
    {
        try
        {
            $votes = VoteEvent::orderBy('end_time', 'desc')->get();
            foreach ($votes as $key => $vote) {
                $this->voteEvent = $vote;
                $this->addVoteStatus($this->voteEvent);
                $this->addRemainDate($this->voteEvent);
                $votes[$key] = $this->voteEvent;
            }

            $response = [
                'votes' => $votes,
            ];

            return view('front.index', $response);
        }
        catch (\Exception $e) 
        {
            echo $e->getMessage();
            // Log::error(sprintf('[%s] %s (%s)', __METHOD__, $e->getMessage(), $e->getLine()));
            // return redirect()->route('index');
        }
    }

    //
    public function showVotePage($event_id, $qrcode_string) 
    {
        try
        {
            $result = $this->handleVoteEvent($event_id, $qrcode_string);

            if ($result['status'] === 'error') {
                throw new \Exception(json_encode($result));
            }
            elseif ($result['status'] === 'voted') {
                return $this->showVoteResult($event_id, $qrcode_string);
            }

            $candidates = Candidate::where('event_id', $event_id)
                                ->orderBy('number', 'asc')
                                ->get();

            $response = [
                'status' => 'ok',
                'vote_event' => $this->voteEvent,
                'candidates' => $candidates,
                'qrcode_string' => $qrcode_string,
            ];

            return view('front.vote', $response);
        }
        catch (\Exception $e) 
        {
            Log::error(sprintf('[%s] %s (%s)', __METHOD__, $e->getMessage(), $e->getLine()));
            return redirect()->route('index');
        }
    }

    public function showSingleVote($event_id)
    {
        try
        {
            $this->voteEvent = VoteEvent::find($event_id);
            $this->addVoteStatus($this->voteEvent);

            $candidates = Candidate::where('event_id', $event_id)
                                ->orderBy('number', 'asc')
                                ->get();

            $response = [
                'status' => 'ok',
                'vote_event' => $this->voteEvent,
                'candidates' => $candidates,
            ];

            return view('front.vote', $response);
        }
        catch (\Exception $e) 
        {
            Log::error(sprintf('[%s] %s (%s)', __METHOD__, $e->getMessage(), $e->getLine()));
            return redirect()->route('index');
        }
    }

    public function doVote(Request $request)
    {
        try
        {
            // 檢查投票數
            $validated = $request->validate([
                'event_id' => 'required|integer',
                'candidates' => 'required|array|min:1|max:3',
                'qrcode_string' => 'required|string|max:255'
            ]);

            // 檢查狀態
            $result = $this->handleVoteEvent($validated['event_id'], $validated['qrcode_string']);

            if ($result['status'] === 'error') {
                return view('front.vote', $result);
            } 
            elseif ($result['status'] === 'voted') {
                return response()->json(['status' => 'voted'], 400);
            }

            // 寫入投票紀錄
            DB::transaction(function () use ($validated) {
                foreach ($validated['candidates'] as $key => $cand_id) {
                    VoteRecord::create([
                        'event_id' => $validated['event_id'],
                        'cand_id' => $cand_id,
                        'code_id' => $this->generateQrcode->code_id
                    ]);
                }

                GenerateQrcode::where('code_id', $this->generateQrcode->code_id)->update(['has_been_voted' => 1]);
            });

            return response()->json(['message' => '投票活動新增成功'], 200);
        }
        catch (\Exception $e) 
        {
            Log::error(sprintf('[%s] %s (%s)', __METHOD__, $e->getMessage(), $e->getLine()));
            return redirect()->route('index');
        }
    }

    private function handleVoteEvent($event_id, $qrcode_string)
    {
        $this->voteEvent = VoteEvent::find($event_id);

        $this->addVoteStatus($this->voteEvent);
        $isOpen = $this->checkVoteisOpen();

        // 檢查 vote_event 是否存在
        if (!$this->voteEvent) {
            return [
                'status' => 'error',
                'error_msg' => '請聯絡管理員'
            ];
        }

        // 檢查 QR code 是否存在、是否已經投過票
        $this->generateQrcode = GenerateQrcode::where('qrcode_string', $qrcode_string)->first();

        if (!$this->generateQrcode) {
            return [
                'status' => 'error',
                'error_msg' => 'QR code not found'
            ];
        } elseif ($this->generateQrcode['has_been_voted'] === 1) {
            return [
                'status' => 'voted',
                'error_msg' => '已經投過票囉'
            ];
        }

        // 檢查是否開放投票
        if (!$isOpen) {
            return [
                'status' => 'error',
                'error_msg' => '目前不在開放投票的時間內'
            ];
        }

        return ['status' => 'success'];
    }

    private function checkVoteisOpen()
    {
        if($this->voteEvent->status == VoteStatus::TIME_IN_THE_PROGRESS) {
            // 當下處於投票時間內
        }
        else {
            return false;
        }
        return true;
    }

    private function getVoteRecords($qrcode_id)
    {
        return VoteRecord::leftJoin('candidates', 'vote_records.cand_id', '=', 'candidates.cand_id')
                ->where('vote_records.code_id', $qrcode_id)
                ->select('vote_records.code_id', 'candidates.number as cand_number', 'candidates.name as cand_name', 'vote_records.updated_at as vote_time')
                ->get();
    }

    public function showVoteResult($event_id, $qrcode_string)
    {
        try
        {
            $eventName = VoteEvent::where('event_id', $event_id)->value('event_name');
            $qrcode = GenerateQrcode::where('qrcode_string', $qrcode_string)->first();

            if($qrcode['has_been_voted'] === 1) {
                $records = $this->getVoteRecords($qrcode->code_id);
                $response = [
                    'status' => 'ok',
                    'event_name' => $eventName,
                    'records' => $records,
                    'qrcode_string' => $qrcode_string
                ];
                return view('front.result', $response);
            }
            else {
                return view('front.result', ['status' => 'error']);
            }
        }
        catch (\Exception $e) 
        {
            Log::error(sprintf('[%s] %s (%s)', __METHOD__, $e->getMessage(), $e->getLine()));
            return redirect()->route('index');
        }
    }

    public function showAllCandidate($event_id)
    {
        // $eventName = VoteEvent::where('event_id', $event_id)->value('event_name');

        //$response = [];
        $candidates = Candidate::where('event_id', $event_id)->get();
        return view('front.candidate', ['candidates' => $candidates]);
    }

    
    // 新增投票活動
    public function createVoteEvent(Request $request) 
    {
        try
        {
            $validated = $request->validate([
                'vote_name' => 'required|string|max:255',
                'start' => '',
                'end' => '',
                'candidates' => 'required|array',
                'candidates.*.number' => 'required|string|min:1|max:10',
                'candidates.*.name' => 'required|string|max:255', 
                'qrcode_count' => 'required|integer|min:1',
                'max_vote' => 'integer|min:1|max:10', // 每張qrcode最多可以投幾票，目前1~10
                'max_winner' => 'integer|min:1|max:10' // 共有幾位得名者，目前1~10
            ]);

            DB::transaction(function () use ($validated) {
                $voteEvent = new VoteEvent();
                $voteEvent->event_name = $validated['vote_name'];
                $voteEvent->max_vote_count = $validated['max_vote'];
                $voteEvent->number_of_qrcodes = $validated['qrcode_count'];
                $voteEvent->number_of_candidates = count($validated['candidates']);
                $voteEvent->number_of_winners = $validated['max_winner'];
                $voteEvent->start_time = $validated['start'];
                $voteEvent->end_time = $validated['end'];
                $voteEvent->save();

                foreach ($validated['candidates'] as $key => $cand) {
                    $candidate = new Candidate();
                    $candidate->event_id = $voteEvent->event_id;
                    $candidate->number = $cand['number'];
                    $candidate->name = $cand['name'];
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
            Log::error(sprintf('[%s] %s (%s)', __METHOD__, $e->getMessage(), $e->getLine()));
            return response()->json(['error' => '建立投票活動時發生錯誤。', 'details' => $e->getMessage()], 500);
        }
    }

    // 取得單一投票活動詳細內容
    public function getVoteEvent($event_id)
    {
        $voteEvent = VoteEvent::find($event_id);
        $this->addVoteStatus($voteEvent);

        if (!$voteEvent) {
            return response()->json(['error' => 'Vote Event not found'], 404);
        }

        $candidates = Candidate::where('event_id', $event_id)
                                ->orderBy('number', 'asc')
                                ->get();

        $qrcodes = $this->getQrcodeInfo($event_id);

        $response = [];
        $response = [
            'vote_event' => $voteEvent,
            'candidates' => $candidates,
            'qrcodes' => $qrcodes,
        ];

        return view('admin.vote.list', $response);
    }

    public function voteEventEditPage($event_id)
    {
        try {
            $voteEvent = VoteEvent::find($event_id);
            $this->addVoteStatus($voteEvent);

            //檢查當前狀態是否可以編輯 (only allow 尚未開始階段)
            $this->validEditPermission($voteEvent);

            $candidates = Candidate::where('event_id', $event_id)
                                ->orderBy('number', 'asc')
                                ->get();

            $response = [];
            $response = [
                'vote_event' => $voteEvent,
                'candidates' => $candidates,
            ];
            return view('admin.vote.edit', $response);
        }
        catch (\Exception $e) 
        {
            Log::error(sprintf('[%s] %s (%s)', __METHOD__, $e->getMessage(), $e->getLine()));
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
        $candidates = Candidate::where('event_id', $event_id)
                                ->orderBy('number', 'asc')
                                ->get();
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
        $candidates = Candidate::where('event_id', $event_id)
                                ->orderBy('number', 'asc')
                                ->get();
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
                // 檢查是否可以啟動投票
                $this->validActivatePermission($voteEvent);

                VoteEvent::where('event_id', $validated['event_id'])
                            ->update([
                                'vote_is_ongoing' => 1,
                                'start_time' => date('Y-m-d H:i:s', time()),
                            ]);

                return response()->json(['message' => '投票活動已啟用'], 200);
            }

            return response()->json(['message' => '投票活動不存在'], 404);
        }
        catch (\Exception $e) 
        {
            Log::error(sprintf('[%s] %s (%s)', __METHOD__, $e->getMessage(), $e->getLine()));
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

                VoteEvent::where('event_id', $validated['event_id'])
                            ->update([
                                'vote_is_ongoing' => 2,
                                'end_time' => date('Y-m-d H:i:s', time()),
                            ]);

                return response()->json(['message' => '投票活動已停用'], 200);
            }

            return response()->json(['message' => '投票活動不存在'], 404);
        }
        catch (\Exception $e) 
        {
            Log::error(sprintf('[%s] %s (%s)', __METHOD__, $e->getMessage(), $e->getLine()));
            return view('hello');
        }
    }

    public function deleteVoteEvent(Request $request)
    {
        try {
            $validated = $request->validate([
                'event_id' => 'required|integer',
            ]);

            $voteEvent = VoteEvent::find($validated['event_id']);

            if ($voteEvent) {
                // 檢查是否可以刪除投票
                $this->validDeletePermission($voteEvent);

                VoteEvent::where('event_id', $validated['event_id'])
                            ->update(['is_delete' => 1]);

                return response()->json(['message' => '投票活動已刪除'], 200);
            }

            return response()->json(['message' => '投票活動不存在'], 404);
        }
        catch (\Exception $e) 
        {
            Log::error(sprintf('[%s] %s (%s)', __METHOD__, $e->getMessage(), $e->getLine()));
            return view('hello');
        }
    }

    public function checkVoteSituation($event_id)
    {
        $voteEvent = VoteEvent::find($event_id);
        $this->addVoteStatus($voteEvent);
        $voted_qrcodes = GenerateQrcode::getVotedQrcodes($event_id);

        $response = [];
        $response = [
            'vote_event' => $voteEvent,
            'qrcodes' => $voted_qrcodes,
            'system_time' => date('Y-m-d H:i:s', time())
        ];

        return view('admin.vote.check', $response);
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
            return view('admin.vote.result', $response);
        }
        catch (\Exception $e) 
        {
            Log::error(sprintf('[%s] %s (%s)', __METHOD__, $e->getMessage(), $e->getLine()));
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
        $eventName = VoteEvent::where('event_id', $event_id)->value('event_name');
        $voteRecord = $this->getVoteRecord($event_id);

        $response = [];
        $response = [
            'records' => $voteRecord,
            'event_name' => $eventName 
        ];
        return view('pdf.votedetail', $response);
    }

}