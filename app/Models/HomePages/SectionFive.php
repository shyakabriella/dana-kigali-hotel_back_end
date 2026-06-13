<?php

namespace App\Models\HomePages;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SectionFive extends Model
{
    use HasFactory;

    protected $table = 'section_fives';

    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'left_image',
        'items',
        'button_text',
    ];

    protected $casts = [
        'items' => 'array',
    ];

    public function getLeftImageUrlAttribute()
    {
        if ($this->left_image && !filter_var($this->left_image, FILTER_VALIDATE_URL)) {
            return Storage::url($this->left_image);
        }
        return $this->left_image;
    }

    public function setItemsAttribute($value)
    {
        $this->attributes['items'] = json_encode($value);
    }

    public function getItemsAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }
}