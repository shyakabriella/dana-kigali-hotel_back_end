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

    // ✅ Consistent: always use 'image' key
    public function getRoomsAttribute($value)
    {
        $rooms = json_decode($value, true) ?? [];

        return array_map(function ($room) {
            $imagePath = $room['image'] ?? null;

            if ($imagePath && !filter_var($imagePath, FILTER_VALIDATE_URL)) {
                $room['image_url'] = Storage::url($imagePath);
            } else {
                $room['image_url'] = $imagePath;
            }

            return $room;
        }, $rooms);
    }

    public function setRoomsAttribute($value)
    {
        $this->attributes['rooms'] = json_encode($value);
    }
}