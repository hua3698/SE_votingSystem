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

    public static function getVoteDetails($event_id)
    {
        return DB::table('vote_records as vr')
            ->join('generate_qrcodes as gq', 'vr.code_id', '=', 'gq.code_id')
            ->join('candidates as cand', 'vr.cand_id', '=', 'cand.cand_id')
            ->where('vr.event_id', $event_id)
            ->select('vr.code_id', 'gq.qrcode_string', 'cand.number', 'cand.name', 'cand.school', 'vr.updated_at')
            ->orderBy('vr.updated_at', 'desc')
            ->get();
    }
}
