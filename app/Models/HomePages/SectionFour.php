<?php

namespace App\Models\HomePages;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SectionFour extends Model
{
    use HasFactory;

    protected $table = 'section_fours';

    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'amenities',
    ];

    protected $casts = [
        'amenities' => 'array',
    ];

    public function setAmenitiesAttribute($value)
    {
        $this->attributes['amenities'] = json_encode($value);
    }

    public function getAmenitiesAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }
}