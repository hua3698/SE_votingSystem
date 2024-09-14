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

class VoteController extends Controller
{
    use VoteHelper;

    private $voteEvent;
    private $generateQrcode;

    //
    public function showVotePage($event_id, $qrcode_string) 
    {
        try
        {
            $result = $this->handleVoteEvent($event_id, $qrcode_string);

            if ($result['status'] === 'error') {
                return view('front.vote', $result);
            }

            $candidates = Candidate::where('event_id', $event_id)->get();


            $response = [];
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
            Log::error(sprintf('[%s] %s', __METHOD__, $e->getMessage()));

            // $response = [];
            // $response['status'] = 'no';
            // $response['error_msg'] = $e->getMessage();
            // return view('front.vote', $response);
            return view('hello');
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
            Log::error(sprintf('[%s] %s', __METHOD__, $e->getMessage()));
            return view('hello');
        }
    }

    private function handleVoteEvent($event_id, $qrcode_string)
    {
        $this->voteEvent = VoteEvent::find($event_id);
        $this->voteEvent = $this->getVoteStatus($this->voteEvent); // VoteHelper trait
        $isOpen = $this->checkVoteisOpen();

        // 檢查 vote_event 是否存在
        if (!$this->voteEvent) {
            return [
                'status' => 'error',
                'error_msg' => '請聯絡管理員'
            ];
        }

        // 檢查是否開放投票
        if (!$isOpen) {
            return [
                'status' => 'error',
                'error_msg' => '目前不在開放投票的時間內'
            ];
        }

        // 檢查 QR code 是否存在、是否已經投過票
        $this->generateQrcode = GenerateQrcode::where('qrcode_string', $qrcode_string)->first();
        if (!$this->generateQrcode) {
            return [
                'status' => 'error',
                'error_msg' => 'QR code not found'
            ];
        } elseif ($this->generateQrcode->has_been_voted === 1) {
            return [
                'status' => 'error',
                'error_msg' => '已經投過票囉'
            ];
        }

        return ['status' => 'success'];
    }

    private function checkVoteisOpen()
    {
        if($this->voteEvent->manual_control === VoteStatus::MANUAL_CONTROL_ENABLED &&
            $this->voteEvent->vote_is_ongoing === VoteStatus::VOTE_IS_ONGOING) 
        {
            // 手動開啟投票 且 投票進行中
        }
        else if($this->voteEvent->manual_control === VoteStatus::MANUAL_CONTROL_DISABLED &&
            $this->voteEvent->status === VoteStatus::TIME_IN_THE_PROGRESS) 
        {
            // 當下處於投票時間內
        }
        else {
            return false;
        }
        return true;
    }
}