<?php

namespace App\Models\About;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AboutSectionTwo extends Model
{
    use HasFactory;

    protected $table = 'about_section_two';

    protected $fillable = [
        'title',
        'subtitle',
        'values',
    ];

    protected $casts = [
        'values' => 'array',
    ];

    public function setValuesAttribute($value)
    {
        $this->attributes['values'] = json_encode($value);
    }

    public function getValuesAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }
}