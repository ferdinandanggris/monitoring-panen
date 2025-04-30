<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionDetail extends Model
{
    use HasFactory;

    protected $table = 'session_detail'; // Nama table, karena defaultnya "session_details"

    protected $fillable = [
        'recorded_at',
        'sequence',
        'latitude',
        'longitude',
        'speed',
        'session_id'
    ];

    public function session()
    {
        return $this->belongsTo(Session::class);
    }
}
