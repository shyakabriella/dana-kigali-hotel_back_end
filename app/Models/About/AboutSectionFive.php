<?php

namespace App\Models\About;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class AboutSectionFive extends Model
{
    use HasFactory;

    protected $table = 'about_section_five';

    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'left_image',
        'button_text',
        'secondary_text',
    ];

    public function getLeftImageUrlAttribute()
    {
        if ($this->left_image && !filter_var($this->left_image, FILTER_VALIDATE_URL)) {
            return Storage::url($this->left_image);
        }
        return $this->left_image;
    }
}