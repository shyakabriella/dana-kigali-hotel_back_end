<?php

namespace App\Models\About;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AboutSectionThree extends Model
{
    use HasFactory;

    protected $table = 'about_section_three';

    protected $fillable = [
        'title',
        'subtitle',
        'timeline',
    ];

    protected $casts = [
        'timeline' => 'array',
    ];

    public function setTimelineAttribute($value)
    {
        $this->attributes['timeline'] = json_encode($value);
    }

    public function getTimelineAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }
}