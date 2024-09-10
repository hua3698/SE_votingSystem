<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    use HasFactory;
    
    protected $table = 'candidates';

    protected $primaryKey = 'cand_id';

    protected $fillable = [
        'event_id',
        'name',
        'vote_count',
        'notes',
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
}
