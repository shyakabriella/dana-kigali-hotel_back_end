<?php

namespace App\Models\HomePages;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SectionEight extends Model
{
    use HasFactory;

    protected $table = 'section_eights';

    protected $fillable = [
        'title',
        'description',
        'button_text',
    ];
}