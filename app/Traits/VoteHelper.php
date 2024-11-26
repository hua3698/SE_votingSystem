<?php

namespace App\Traits;

use Carbon\Carbon;

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

    protected function addRemainDate(&$voteEvent)
    {
        if($voteEvent) {
            $startDate = Carbon::parse($voteEvent->start);
            $endDate = Carbon::parse($voteEvent->end);

            $voteEvent->remain_date = $endDate->greaterThanOrEqualTo(now()) ? $endDate->diffInDays(now()) : 0;
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
