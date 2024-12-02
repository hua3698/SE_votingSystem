<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VoteEvent extends Model
{
    use HasFactory;

    protected $table = 'vote_events';

    protected $primaryKey = 'event_id';

    protected $fillable = [
        'event_name',
        'start_date',
        'end_date',
        'number_of_candidates',
    ];

    public $timestamps = true;

    public function generateQrcodes()
    {
        return $this->hasMany(GenerateQrcode::class, 'event_id', 'event_id');
    }

    public function candidates()
    {
        return $this->hasMany(Candidate::class, 'event_id', 'event_id');
    }
}
