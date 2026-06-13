<?php

namespace App\Models\About;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class AboutSectionOne extends Model
{
    use HasFactory;

    protected $table = 'about_section_one';

    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'right_image',
        'card_title',
        'stats',
    ];

    protected $casts = [
        'stats' => 'array',
    ];

    public function getRightImageUrlAttribute()
    {
        if ($this->right_image && !filter_var($this->right_image, FILTER_VALIDATE_URL)) {
            return Storage::url($this->right_image);
        }
        return $this->right_image;
    }

    public function setStatsAttribute($value)
    {
        $this->attributes['stats'] = json_encode($value);
    }

    public function getStatsAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }
}