<?php

namespace App\Models\HomePages;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SectionSix extends Model
{
    use HasFactory;

    protected $table = 'section_sixes';

    protected $fillable = [
        'title',
        'subtitle',
        'gallery',
    ];

    protected $casts = [
        'gallery' => 'array',
    ];

    public function setGalleryAttribute($value)
    {
        $this->attributes['gallery'] = json_encode($value);
    }

    public function getGalleryAttribute($value)
    {
        $gallery = json_decode($value, true) ?? [];
        
        return array_map(function ($image) {
            if ($image && !filter_var($image, FILTER_VALIDATE_URL)) {
                return Storage::url($image);
            }
            return $image;
        }, $gallery);
    }

    // Get original paths for deletion
    public function getGalleryPathsAttribute()
    {
        return json_decode($this->attributes['gallery'] ?? '[]', true) ?? [];
    }
}