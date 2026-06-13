<?php

namespace App\Models\Contact;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ContactSectionTwo extends Model
{
    use HasFactory;

    protected $table = 'contact_section_two';

    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'right_image',
        'image_caption',
        'image_address',
        'opening_hours_title',
        'opening_hours_subtitle',
        'opening_hours',
    ];

    protected $casts = [
        'opening_hours' => 'array',
    ];

    public function getRightImageUrlAttribute()
    {
        if ($this->right_image && !filter_var($this->right_image, FILTER_VALIDATE_URL)) {
            return Storage::url($this->right_image);
        }
        return $this->right_image;
    }

    public function setOpeningHoursAttribute($value)
    {
        $this->attributes['opening_hours'] = json_encode($value);
    }

    public function getOpeningHoursAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }
}