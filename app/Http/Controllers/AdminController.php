<?php

namespace App\Http\Controllers;

use App\Models\VoteEvent;
use App\Models\Candidate;
use App\Models\GenerateQrcode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use chillerlan\QRCode\{QRCode, QROptions};

class AdminController extends Controller
{
    public function adminPage(Request $request) 
    {
        try
        {
            $voteEvents = VoteEvent::orderBy('created_at', 'desc')->get()->map(function ($event) {
                $now = Carbon::now();
                $start = Carbon::parse($event->start_time);
                $end = Carbon::parse($event->end_time);

                if ($now->between($start, $end)) {
                    $event->status = 1; # 進行中
                } elseif ($now->lessThan($start)) {
                    $event->status = 2; # 未開始
                } else {
                    $event->status = 3; # 已結束
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
    public function createVote(Request $request) 
    {
        try
        {
            $validated = $request->validate([
                'vote_name' => 'required|string|max:255',
                'start' => 'required|date_format:Y-m-d H:i:s',
                'end' => 'required|date_format:Y-m-d H:i:s|after:start',
                'candidates' => 'required|array',
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
        if ($voteEvent) {
            $now = Carbon::now();
            $start = Carbon::parse($voteEvent->start_time);
            $end = Carbon::parse($voteEvent->end_time);

            // 判斷狀態
            if ($now->between($start, $end)) {
                $voteEvent->status = 1; // 進行中
            } elseif ($now->lessThan($start)) {
                $voteEvent->status = 2; // 未開始
            } else {
                $voteEvent->status = 3; // 已結束
            }
        }

        if (!$voteEvent) {
            return response()->json(['error' => 'Vote Event not found'], 404);
        }

        $candidates = Candidate::where('event_id', $event_id)->get();

        $qrcodes = GenerateQrcode::where('event_id', $event_id)->get()->map(function ($qrcode, $event_id) {
            $url = 'http://140.115.2.129/vote' . '/' . $event_id . '/' . $qrcode->qrcode_string;
            $qrcode->qrcode_url = (new QRCode)->render($url);
            return $qrcode;
        }, $event_id);

        $response = [];
        $response = [
            'vote_event' => $voteEvent,
            'candidates' => $candidates,
            'qrcodes' => $qrcodes,
        ];

        return view('admin.vote', $response);
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
            ->header('Content-Disposition', 'attachment; filename="qrcode.pdf"');
    }

    private function getQrcodeInfo($event_id)
    {
        $result = GenerateQrcode::where('event_id', $event_id)->get()->map(function ($qrcode, $event_id) {
            $url = 'http://140.115.2.129/vote' . '/' . $event_id . '/' . $qrcode->qrcode_string;
            $qrcode->qrcode_url = (new QRCode)->render($url);
            return $qrcode;
        }, $event_id);

        return $result;
    }

}
