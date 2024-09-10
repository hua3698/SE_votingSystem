<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoteRecord extends Model
{
    use HasFactory;

    protected $table = 'vote_records';

    protected $primaryKey = 'record_id';
    
    protected $fillable = [
        'event_id',
        'cand_id',
        'code_id',
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

    public function generateQrcode()
    {
        return $this->belongsTo(GenerateQrcode::class, 'code_id', 'code_id');
    }
}
