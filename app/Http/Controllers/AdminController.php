<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VoteEvent;
use App\Models\Candidate;
use App\Models\GenerateQrcode;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
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

        DB::beginTransaction();

        try
        {
            $voteEvent = new VoteEvent();
            $voteEvent->event_name = $validated['vote_name'];
            $voteEvent->start_time = $validated['start'];
            $voteEvent->end_time = $validated['end'];
            $voteEvent->max_vote_count = $validated['max_vote'];
            $voteEvent->number_of_qrcodes = $validated['qrcode_count'];
            $voteEvent->number_of_candidates = count($candidates);
            $voteEvent->number_of_winners = $validated['max_winner'];
            $voteEvent->manual_control = $boolManualControl;
            $voteEvent->save();

            foreach ($validated['candidates'] as $key => $cand) {
                $candidate = new Candidate();
                $candidate->event_id = $voteEvent->event_id;
                $candidate->candidates_name = $cand->name;
                $candidate->candidates_school = $cand->school;
                $candidate->save();
            }

            for ($i = 0; $i < $validated['qrcode_count']; $i++) {
                $generateQrcode = new GenerateQrcode();
                $generateQrcode->event_id = $voteEvent->event_id;
                $generateQrcode->qrcode_string = md5($voteEvent->event_id . '_' . ($i + 1));
                $generateQrcode->save();
            }

            DB::commit();

            return response()->json(['message' => '投票活動新增成功'], 200);
        }
        catch (\Exception $e) 
        {
            // echo $e;
            DB::rollBack();
            return response()->json(['error' => '建立投票活動時發生錯誤。', 'details' => $e->getMessage()], 500);
        }
    }

    // 取得單一投票活動詳細內容
    public function getVoteEvent($event_id)
    {
        $voteEvent = VoteEvent::find($event_id);

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

}
