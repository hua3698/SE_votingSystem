<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class GenerateQrcode extends Model
{
    use HasFactory;

    protected $table = 'generate_qrcodes';

    protected $primaryKey = 'code_id';

    protected $fillable = [
        'event_id',
        'qrcode_string',
        'has_been_voted',
    ];

    public $timestamps = true;

    public function voteEvent()
    {
        return $this->belongsTo(VoteEvent::class, 'event_id', 'event_id');
    }

    public function voteRecords()
    {
        return $this->hasMany(VoteRecord::class, 'code_id', 'code_id');
    }

    public static function getVotedQrcodes($event_id)
    {
        return self::where('generate_qrcodes.event_id', $event_id)
            ->where('has_been_voted', 1)
            ->leftJoin('vote_records', 'generate_qrcodes.code_id', '=', 'vote_records.code_id')
            ->select('generate_qrcodes.*', DB::raw('COUNT(vote_records.code_id) as total_votes'))
            ->groupBy('generate_qrcodes.code_id')
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    
}
