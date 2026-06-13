<?php

namespace App\Models\About;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class AboutHero extends Model
{
    use HasFactory;

    protected $table = 'about_hero';

    protected $fillable = [
        'title',
        'subtitle',
        'destination',
        'background_image',
    ];

    public function getBackgroundImageUrlAttribute()
    {
        if ($this->background_image && !filter_var($this->background_image, FILTER_VALIDATE_URL)) {
            return Storage::url($this->background_image);
        }
        return $this->background_image;
    }
}