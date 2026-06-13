<?php

namespace App\Models\Experiences;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ExperiencesSectionOne extends Model
{
    use HasFactory;

    protected $table = 'experiences_section_one';

    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'experiences',
    ];

    protected $casts = [
        'experiences' => 'array',
    ];

    public function setExperiencesAttribute($value)
    {
        $this->attributes['experiences'] = json_encode($value);
    }

    public function getExperiencesAttribute($value)
    {
        $experiences = json_decode($value, true) ?? [];
        
        return array_map(function ($experience) {
            // Handle image
            if (isset($experience['image']) && $experience['image'] && !filter_var($experience['image'], FILTER_VALIDATE_URL)) {
                $experience['image_url'] = Storage::url($experience['image']);
                $experience['image_path'] = $experience['image'];
            } else {
                $experience['image_url'] = $experience['image'] ?? null;
                $experience['image_path'] = null;
            }
            
            return $experience;
        }, $experiences);
    }
}