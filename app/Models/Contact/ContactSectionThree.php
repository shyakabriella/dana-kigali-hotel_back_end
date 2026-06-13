<?php

namespace App\Models\Contact;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactSectionThree extends Model
{
    use HasFactory;

    protected $table = 'contact_section_three';

    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'button_one_text',
        'button_two_text',
    ];
}