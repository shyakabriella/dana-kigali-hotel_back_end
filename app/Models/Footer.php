<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Footer extends Model
{
    use HasFactory;

    protected $table = 'footer';

    protected $fillable = [
        'hotel_name',
        'description',
        'address',
        'phone',
        'email',
        'newsletter_placeholder',
        'newsletter_button',
        'social_links',
        'copyright_text',
    ];

    protected $casts = [
        'social_links' => 'array',
    ];

    public function setSocialLinksAttribute($value)
    {
        $this->attributes['social_links'] = json_encode($value);
    }

    public function getSocialLinksAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }
}