<?php

namespace App\Models\HomePages;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SectionSeven extends Model
{
    use HasFactory;

    protected $table = 'section_sevens';

    protected $fillable = [
        'title',
        'subtitle',
        'testimonials',
    ];

    protected $casts = [
        'testimonials' => 'array',
    ];

    public function setTestimonialsAttribute($value)
    {
        $this->attributes['testimonials'] = json_encode($value);
    }

    public function getTestimonialsAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }
}