<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
