<?php

namespace App\Models\Experiences;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExperiencesSectionTwo extends Model
{
    use HasFactory;

    protected $table = 'experiences_section_two';

    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'button_one_text',
        'button_two_text',
    ];
}