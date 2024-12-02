<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class VoteRecord extends Model
{
    use HasFactory;

    protected $table = 'vote_records';

    protected $primaryKey = 'record_id';
    
    protected $fillable = [
        'event_id',
        'cand_id',
        'user_id'
    ];

    public $timestamps = true;

    public function voteEvent()
    {
        return $this->belongsTo(VoteEvent::class, 'event_id', 'event_id');
    }

    public function candidate()
    {
        return $this->belongsTo(Candidate::class, 'cand_id', 'cand_id');
    }

    public static function getVoteUsers($event_id)
    {
        $sql = "
            SELECT u.user_id, u.name, a.vote_count, a.updated_at
            FROM (
                SELECT user_id, event_id, COUNT(*) AS vote_count, MAX(updated_at) AS updated_at
                FROM vote_records
                WHERE event_id = ?
                GROUP BY user_id, event_id
            ) a
            LEFT JOIN users u ON u.user_id = a.user_id
        ";

        return DB::select($sql, [$event_id]);
    }

    public static function getVoteDetails($event_id)
    {
        return DB::table('vote_records as vr')
            ->join('users as u', 'vr.user_id', '=', 'u.user_id')
            ->join('candidates as cand', 'vr.cand_id', '=', 'cand.cand_id')
            ->where('vr.event_id', $event_id)
            ->select('vr.user_id', 'u.name as user_name', 'cand.number as cand_number', 'cand.name as cand_name', 'vr.updated_at')
            ->orderBy('vr.updated_at', 'desc')
            ->get();
    }

    public static function countTotalParticipants($event_id)
    {
        return DB::table('vote_records')
            ->where('event_id', $event_id)
            ->distinct('user_id')
            ->count('user_id');
    }
}


// select u.user_id, u.name, a.vote_count, a.updated_at
// from (
//     select user_id, event_id, count(*) as vote_count, max(updated_at) as updated_at
//     from vote_records
//     where event_id = 5
//     group by user_id, event_id
// ) a
// left join users u on u.user_id = a.user_id;


// select vr.user_id, u.name, cand.number, cand.name, vr.updated_at
// from vote_records as vr
// left join users as u on vr.user_id = u.user_id
// left join candidates as cand on vr.cand_id = cand.cand_id
// where vr.event_id = 5
// order by vr.updated_at desc;


// select count(distinct user_id) AS total_users
// from vote_records
// where event_id = 5;