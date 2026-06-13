<?php

namespace App\Models\HomePages;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SectionOne extends Model
{
    use HasFactory;

    protected $table = 'section_ones';

    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'left_image',
        'card1_title',
        'card1_description',
        'card2_title',
        'card2_description',
        'bottom_card_text',
    ];

    public function getLeftImageUrlAttribute()
    {
        if ($this->left_image && !filter_var($this->left_image, FILTER_VALIDATE_URL)) {
            return Storage::url($this->left_image);
        }
        return $this->left_image;
    }
}