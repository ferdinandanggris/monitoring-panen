<?php

namespace App\Models;

use Filament\Panel\Concerns\HasFont;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;
    protected $table = 'driver'; // Nama table, karena defaultnya "drivers"

    protected $fillable = [
        'name',
        'notes'
    ];

    public function sessions()
    {
        return $this->hasMany(Session::class);
    }

    public function machines()
    {
        return $this->hasMany(Machine::class);
    }
}
