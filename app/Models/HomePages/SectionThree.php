<?php

namespace App\Models\HomePages;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SectionThree extends Model
{
    use HasFactory;

    protected $table = 'section_threes';

    protected $fillable = [
        'title',
        'subtitle',
        'cards',
    ];

    protected $casts = [
        'cards' => 'array',
    ];

    public function setCardsAttribute($value)
    {
        $this->attributes['cards'] = json_encode($value);
    }

    public function getCardsAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }
}