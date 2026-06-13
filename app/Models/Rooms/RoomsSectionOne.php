<?php

namespace App\Models\Rooms;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class RoomsSectionOne extends Model
{
    use HasFactory;

    protected $table = 'rooms_section_one';

    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'rooms',
    ];

    protected $casts = [
        'rooms' => 'array',
    ];

    public function setRoomsAttribute($value)
    {
        $this->attributes['rooms'] = json_encode($value);
    }

    public function getRoomsAttribute($value)
    {
        $rooms = json_decode($value, true) ?? [];
        
        return array_map(function ($room) {
            // Handle main image
            if (isset($room['main_image']) && $room['main_image'] && !filter_var($room['main_image'], FILTER_VALIDATE_URL)) {
                $room['main_image_url'] = Storage::url($room['main_image']);
                $room['main_image_path'] = $room['main_image'];
            } else {
                $room['main_image_url'] = $room['main_image'] ?? null;
                $room['main_image_path'] = null;
            }
            
            // Handle gallery images
            if (isset($room['gallery']) && is_array($room['gallery'])) {
                $room['gallery_urls'] = array_map(function ($image) {
                    if ($image && !filter_var($image, FILTER_VALIDATE_URL)) {
                        return Storage::url($image);
                    }
                    return $image;
                }, $room['gallery']);
                $room['gallery_paths'] = $room['gallery'];
            } else {
                $room['gallery_urls'] = [];
                $room['gallery_paths'] = [];
            }
            
            return $room;
        }, $rooms);
    }
}