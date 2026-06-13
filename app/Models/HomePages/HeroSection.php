<?php

namespace App\Models\HomePages;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class HeroSection extends Model
{
    use HasFactory;

    protected $table = 'hero_sections';

    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'button_text',
        'secondary_text',
        'background_image',
    ];

    public function getBackgroundImageUrlAttribute()
    {
        if ($this->background_image) {
            return Storage::url($this->background_image);
        }
        return null;
    }
}