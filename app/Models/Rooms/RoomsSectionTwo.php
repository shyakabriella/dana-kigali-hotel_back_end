<?php

namespace App\Models\Rooms;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomsSectionTwo extends Model
{
    use HasFactory;

    protected $table = 'rooms_section_two';

    protected $fillable = [
        'title',
        'subtitle',
        'button_text',
    ];
}