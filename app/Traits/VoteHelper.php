<?php

namespace App\Traits;

use Carbon\Carbon;
use App\Models\VoteRecord;

trait VoteHelper
{
    // 即時用系統時間判斷投票活動的狀態
    protected function addVoteStatus(&$voteEvent)
    {
        if ($voteEvent) {
            $now = Carbon::now();
            $start = Carbon::parse($voteEvent->start_time);
            $end = Carbon::parse($voteEvent->end_time);

            // 判斷狀態
            if ($now->lessThan($start)) {
                $voteEvent->status = 0; // 未開始
            } elseif ($now->between($start, $end)) {
                $voteEvent->status = 1; // 進行中
            } else {
                $voteEvent->status = 2; // 已結束
            }
        }
    }

    protected function addRemainDay(&$voteEvent)
    {
        if($voteEvent) {
            $today = Carbon::today();
            $end_date = date("Y-m-d", strtotime($voteEvent->end_time));
            $diff = $today->diffInDays($end_date, false);
            $diff = $diff > 0 ? $diff : 0;
            $voteEvent->remain_date = $diff;
        }
    }

    protected function addTotalParticipants(&$voteEvent)
    {
        if($voteEvent) {
            $voteEvent->total_participants = VoteRecord::countTotalParticipants($voteEvent->event_id);
        }
    }

    //檢查當前狀態是否可以編輯 (only allow 尚未開始階段)
    protected function validEditPermission(&$voteEvent)
    {
        if($voteEvent->status == 0) {
        }
        else {
            throw new \Exception();
        }
    }

    protected function validDeletePermission(&$voteEvent)
    {
        $this->addVoteStatus($voteEvent);

        // 結束投票後才可以刪除
        if($voteEvent->status == 2) {
        }
        else {
            throw new \Exception();
        }
    }

    protected function validActivatePermission(&$voteEvent)
    {
        $this->addVoteStatus($voteEvent);

        // 尚未開始才可開啟投票
        if($voteEvent->status == 0) {
        }
        else {
            throw new \Exception();
        }
    }

    protected function validDeactivatePermission(&$voteEvent)
    {
        $this->addVoteStatus($voteEvent);

        // 進行中才可以結束投票
        if($voteEvent->status == 1) {
        }
        else {
            throw new \Exception();
        }
    }

    protected function validGetResultPermission(&$voteEvent)
    {
        $this->addVoteStatus($voteEvent);

        // 結束投票後才可以查看排名結果
        if($voteEvent->status == 2) {
        }
        else {
            throw new \Exception();
        }
    }
}
