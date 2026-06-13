<?php

namespace App\Models\Contact;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactSectionOne extends Model
{
    use HasFactory;

    protected $table = 'contact_section_one';

    protected $fillable = [
        'title',
        'subtitle',
        'description',
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