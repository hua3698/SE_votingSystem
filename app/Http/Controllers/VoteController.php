<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VoteEvent;
use App\Models\Candidate;
use App\Models\User;
use App\Models\VoteRecord;
use App\Traits\VoteHelper;
use App\Constants\VoteStatus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Dompdf\Dompdf;
use Dompdf\Options;

class VoteController extends Controller
{
    use VoteHelper;

    private $voteEvent;
    private $userID;

    public function showVotes()
    {
        try
        {
            $votes = VoteEvent::where('is_delete', 0)->orderBy('end_time', 'asc')->get();

            foreach ($votes as $key => $vote) {
                $this->voteEvent = $vote;
                $this->addVoteStatus($this->voteEvent);
                $this->addRemainDay($this->voteEvent);
                $this->addTotalParticipants($this->voteEvent);
                $votes[$key] = $this->voteEvent;
            }

            $response = [
                'votes' => $votes,
                // 'total' => $votes->total(),
                // 'count' => 3,
                // 'current_page' => $votes->currentPage(),
                // 'last_page' => $votes->lastPage(),
            ];

            return view('front.index', $response);
        }
        catch (\Exception $e) 
        {
            Log::error(sprintf('[%s] %s (%s)', __METHOD__, $e->getMessage(), $e->getLine()));
            return redirect()->route('index');
        }
    }

    public function searchVotesAPI(Request $request)
    {
        $validated = $request->validate([
            'keyword' => 'required|string|max:255',
        ]);

        $keyword = $validated['keyword'];
        $results = DB::table('vote_events')
                    ->select('event_id', 'event_name')
                    ->where('event_name', 'like', '%' . $keyword . '%')
                    ->get();

        // 回傳 JSON 結果
        return response()->json($results, 200);
    }

    public function showSingleVote($event_id)
    {
        try
        {
            $user_id = User::where('email', session('frontuser'))->value('user_id');
            $result = $this->handleVoteEvent($event_id, $user_id);

            if ($result['status'] === 'error') {
                throw new \Exception(json_encode($result));
            }
            elseif ($result['status'] === 'voted') {
                return $this->showVoteDetail($event_id);
            }
            else if($result['status'] == 'close') {
                return $this->showVoteResult($event_id);
            }

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
            dd($e->getMessage());
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
                'candidates' => 'required|array|min:1',
            ]);

            // 檢查狀態
            $user_id = User::where('email', session('frontuser'))->value('user_id');
            $result = $this->handleVoteEvent($validated['event_id'], $user_id);

            if ($result['status'] === 'error') {
                return view('front.vote', $result);
            } 
            elseif ($result['status'] === 'voted') {
                return response()->json(['status' => 'voted'], 400);
            }

            // 寫入投票紀錄
            DB::transaction(function () use ($validated, $user_id) {
                foreach ($validated['candidates'] as $key => $cand_id) {
                    VoteRecord::create([
                        'event_id' => $validated['event_id'],
                        'cand_id' => $cand_id,
                        'user_id' => $user_id
                    ]);
                }
            });

            return response()->json(['message' => '投票活動新增成功'], 200);
        }
        catch (\Exception $e) 
        {
            Log::error(sprintf('[%s] %s (%s)', __METHOD__, $e->getMessage(), $e->getLine()));
            return redirect()->route('index');
        }
    }

    private function handleVoteEvent($event_id, $user_id)
    {
        $this->voteEvent = VoteEvent::find($event_id);

        $this->addVoteStatus($this->voteEvent);

        // 檢查 vote_event 是否存在
        if (!$this->voteEvent) {
            return [
                'status' => 'error',
                'error_msg' => '請聯絡管理員'
            ];
        }

        $voted = $this->checkIsVoted($event_id, $user_id);

        if ($voted) {
            return [
                'status' => 'voted',
                'error_msg' => '已經投過票囉'
            ];
        }

        // 檢查是否開放投票
        if ($this->voteEvent->status == 2) {
            return [
                'status' => 'close',
                'error_msg' => '已結束'
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

    private function checkIsVoted($event_id, $user_id)
    {
        $vote = VoteRecord::where('user_id', $user_id)
        ->where('event_id', $event_id)
        ->get();

        return count($vote) > 0 ? true : false;
    }

    private function getVoteRecords($event_id, $user_id)
    {
        return VoteRecord::leftJoin('candidates', 'vote_records.cand_id', '=', 'candidates.cand_id')
                ->where('vote_records.user_id', $user_id)
                ->where('vote_records.event_id', $event_id)
                ->select('candidates.number as cand_number', 'candidates.name as cand_name', 'vote_records.updated_at as vote_time')
                ->get();
    }

    public function showVoteResult($event_id)
    {
        try
        {
            $voteEvent = VoteEvent::find($event_id);
            $this->validGetResultPermission($voteEvent);

            // if($voted) {
                $voteEvent = VoteEvent::find($event_id);
                $eventName = VoteEvent::where('event_id', $event_id)->value('event_name');
                $rank = Candidate::getCandidateRanking($event_id);

                $response = [
                    'status' => 'ok',
                    'event_name' => $eventName,
                    'vote_event' => $voteEvent,
                    'rank' => $rank,
                ];
                return view('front.result', $response);
            // }
            // else {
            //     return view('front.result', ['status' => 'error']);
            // }
        }
        catch (\Exception $e) 
        {
            Log::error(sprintf('[%s] %s (%s)', __METHOD__, $e->getMessage(), $e->getLine()));
            return redirect()->route('index');
        }
    }

    public function showVoteDetail($event_id)
    {
        try
        {
            $user_id = User::where('email', session('frontuser'))->value('user_id');
            $voted = $this->checkIsVoted($event_id, $user_id);

            if($voted) {
                $eventName = VoteEvent::where('event_id', $event_id)->value('event_name');
                $records = $this->getVoteRecords($event_id, $user_id);
                $response = [
                    'status' => 'ok',
                    'event_name' => $eventName,
                    'records' => $records,
                ];
                return view('front.detail', $response);
            }
            else {
                return redirect()->route('index');
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
                'max_vote' => 'integer|min:1|max:10', // 每張qrcode最多可以投幾票，目前1~10
                'max_winner' => 'integer|min:1|max:10' // 共有幾位得名者，目前1~10
            ]);

            DB::transaction(function () use ($validated) {
                $voteEvent = new VoteEvent();
                $voteEvent->event_name = $validated['vote_name'];
                $voteEvent->max_vote_count = $validated['max_vote'];
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

        $response = [];
        $response = [
            'vote_event' => $voteEvent,
            'candidates' => $candidates,
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
        $voted_users = VoteRecord::getVoteUsers($event_id);

        $response = [];
        $response = [
            'vote_event' => $voteEvent,
            'users' => $voted_users,
            'system_time' => date('Y-m-d H:i:s', time())
        ];

        return view('admin.vote.check', $response);
    }

    // ajax每20秒取資料
    public function postCheckVoteSituation($event_id)
    {
        $voted_users = VoteRecord::getVoteUsers($event_id);

        $result = [];
        $result = [
            'users' => $voted_users,
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
            if (!isset($groupedResults[$row->user_id])) {
                $groupedResults[$row->user_id] = [
                    'user_id' => $row->user_id,
                    'user_name' => $row->user_name,
                    'updated_at' => $row->updated_at,
                    'vote' => [],
                ];
            }

            $groupedResults[$row->user_id]['vote'][] = [
                'number' => $row->cand_number,
                'name' => $row->cand_name,
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
}