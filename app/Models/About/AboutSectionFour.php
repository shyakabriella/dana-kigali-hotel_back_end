<?php

namespace App\Models\About;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class AboutSectionFour extends Model
{
    use HasFactory;

    protected $table = 'about_section_four';

    protected $fillable = [
        'title',
        'subtitle',
        'team_members',
    ];

    protected $casts = [
        'team_members' => 'array',
    ];

    public function setTeamMembersAttribute($value)
    {
        $this->attributes['team_members'] = json_encode($value);
    }

    public function getTeamMembersAttribute($value)
    {
        $members = json_decode($value, true) ?? [];
        
        return array_map(function ($member) {
            if (isset($member['image']) && $member['image'] && !filter_var($member['image'], FILTER_VALIDATE_URL)) {
                $member['image_url'] = Storage::url($member['image']);
                $member['image_path'] = $member['image'];
            } else {
                $member['image_url'] = $member['image'] ?? null;
                $member['image_path'] = null;
            }
            return $member;
        }, $members);
    }
}