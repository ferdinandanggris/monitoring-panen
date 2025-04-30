<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    use HasFactory;

    protected $table = 'session'; // Nama table, karena defaultnya "sessions"

    protected $fillable = [
        'date',
        'longitude',
        'latitude',
        'total_area',
        'total_distance',
        'last_sequence_session_detail',
        'last_calculate_at',
        'average_speed',
        'machine_id',
        'driver_id',
        'start_time',
        'end_time',
        'last_update_at'
    ];

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function details()
    {
        return $this->hasMany(SessionDetail::class);
    }
}
