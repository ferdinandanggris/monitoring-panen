<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    use HasFactory;

    protected $table = 'machine'; // Nama table, karena defaultnya "mesins"

    protected $fillable = [
        'name',
        'notes',
        'current_driver_id',
    ];

    public function sessions()
    {
        return $this->hasMany(Session::class, 'machine_id');
    }
}
