<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Candidate extends Model
{
    use HasFactory;
    
    protected $table = 'candidates';

    protected $primaryKey = 'cand_id';

    protected $fillable = [
        'event_id',
        'name',
        'votes_count',
        'notes',
        'specialty',
        'manifesto',
        'achievements'
    ];

    public $timestamps = true;

    public function voteEvent()
    {
        return $this->belongsTo(VoteEvent::class, 'event_id', 'event_id');
    }

    public function voteRecords()
    {
        return $this->hasMany(VoteRecord::class, 'cand_id', 'cand_id');
    }

    public static function getCandidateRanking($event_id)
    {
        $query = "
            SELECT cand.cand_id, cand.name, cand.school, 
                    COUNT(*) AS total, 
                    RANK() OVER (ORDER BY COUNT(*) DESC) AS 'rank'
            FROM candidates cand
            LEFT JOIN vote_records vr ON vr.cand_id = cand.cand_id 
            WHERE vr.event_id = ?
            GROUP BY cand_id
        ";

        return DB::select($query, [$event_id]);
    }
}
