<?php

namespace App\Models\Experiences;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ExperiencesHero extends Model
{
    use HasFactory;

    protected $table = 'experiences_hero';

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