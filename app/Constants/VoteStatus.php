<?php

namespace App\Constants;

class VoteStatus
{
    const MANUAL_CONTROL_DISABLED = 0;
    const MANUAL_CONTROL_ENABLED = 1;

    // 當vote_events.manual_control = 1時才有效
    const VOTE_IS_NOT_STARTED = 0;
    const VOTE_IS_ONGOING = 1;
    const VOTE_IS_ENDED = 2;

    // 當vote_events.manual_control = 0時，用時間判斷
    const TIME_NOT_YET = 0;
    const TIME_IN_THE_PROGRESS = 1;
    const TIME_END = 2;
}