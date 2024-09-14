<?php

namespace App\Traits;

use Carbon\Carbon;

trait VoteHelper
{
    // 即時用系統時間判斷投票活動的狀態
    protected function getVoteStatus($voteEvent)
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
        return $voteEvent;
    }

}
