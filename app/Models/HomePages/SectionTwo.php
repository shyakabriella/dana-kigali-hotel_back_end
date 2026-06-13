<?php

namespace App\Models\HomePages;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SectionTwo extends Model
{
    use HasFactory;

    protected $table = 'section_twos';

    protected $fillable = [
        'title',
        'subtitle',
        'rooms',
    ];

    protected $casts = [
        'rooms' => 'array',
    ];

    // Get rooms with proper image URLs
    public function getRoomsAttribute($value)
    {
        $rooms = json_decode($value, true) ?? [];
        
        return array_map(function ($room) {
            if (isset($room['image']) && $room['image'] && !filter_var($room['image'], FILTER_VALIDATE_URL)) {
                $room['image_url'] = Storage::url($room['image']);
                $room['image_path'] = $room['image'];
            } elseif (isset($room['image']) && filter_var($room['image'], FILTER_VALIDATE_URL)) {
                $room['image_url'] = $room['image'];
                $room['image_path'] = null;
            } else {
                $room['image_url'] = null;
                $room['image_path'] = null;
            }
            return $room;
        }, $rooms);
    }

    public function setRoomsAttribute($value)
    {
        $this->attributes['rooms'] = json_encode($value);
    }
}