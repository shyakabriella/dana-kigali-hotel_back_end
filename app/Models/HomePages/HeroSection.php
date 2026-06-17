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
        if (!$this->background_image) {
            return null;
        }
        
        // If it's already a full URL, return as is
        if (filter_var($this->background_image, FILTER_VALIDATE_URL)) {
            return $this->background_image;
        }
        
        // Get the full URL using app URL
        $appUrl = config('app.url');
        $url = Storage::url($this->background_image);
        
        // If Storage::url returns relative path, prepend app URL
        if (str_starts_with($url, '/')) {
            return rtrim($appUrl, '/') . $url;
        }
        
        return $url;
    }
}