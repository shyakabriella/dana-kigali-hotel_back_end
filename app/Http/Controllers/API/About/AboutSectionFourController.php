<?php

namespace App\Http\Controllers\Api\About;

use App\Http\Controllers\Api\BaseController;
use App\Models\About\AboutSectionFour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AboutSectionFourController extends BaseController
{
    // GET /api/dana/about/section-four - Get all (Public)
    public function index()
    {
        $sections = AboutSectionFour::orderBy('id', 'desc')->get();
        
        $data = $sections->map(function ($section) {
            return [
                'id' => $section->id,
                'title' => $section->title,
                'subtitle' => $section->subtitle,
                'team_members' => $section->team_members,
            ];
        });

        return $this->sendResponse($data, 'About section four retrieved successfully');
    }

    // GET /api/dana/about/section-four/{id} - Get single (Public)
    public function show($id)
    {
        $section = AboutSectionFour::find($id);
        
        if (!$section) {
            return $this->sendError('About section not found', [], 404);
        }

        return $this->sendResponse([
            'id' => $section->id,
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'team_members' => $section->team_members,
        ], 'About section retrieved successfully');
    }

    // POST /api/dana/about/section-four - CREATE (Admin only)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'team_members' => 'required|array|min:1',
            'team_members.*.name' => 'required|string',
            'team_members.*.position' => 'required|string',
            'team_members.*.image' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $section = AboutSectionFour::first();

        $data = [
            'title' => $request->title ?? ($section->title ?? '— OUR FAMILY'),
            'subtitle' => $request->subtitle ?? ($section->subtitle ?? 'A team that welcomes you home.'),
            'team_members' => $request->team_members,
        ];

        if ($section) {
            $section->update($data);
            $message = 'About section four updated successfully';
        } else {
            $section = AboutSectionFour::create($data);
            $message = 'About section four created successfully';
        }

        // Refresh to get updated data
        $section = AboutSectionFour::find($section->id);

        return $this->sendResponse([
            'id' => $section->id,
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'team_members' => $section->team_members,
        ], $message);
    }

    // POST /api/dana/about/section-four/upload-member-image - Upload team member image (Admin only)
    public function uploadMemberImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'member_index' => 'required|integer',
            'section_id' => 'required|integer|exists:about_section_four,id',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $section = AboutSectionFour::find($request->section_id);
        $teamMembers = $section->team_members;
        $memberIndex = $request->member_index;

        if (!isset($teamMembers[$memberIndex])) {
            return $this->sendError('Team member not found', [], 404);
        }

        // Delete old image if exists and is local file
        if (isset($teamMembers[$memberIndex]['image']) && $teamMembers[$memberIndex]['image'] && !filter_var($teamMembers[$memberIndex]['image'], FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($teamMembers[$memberIndex]['image']);
        }

        // Store new image
        $file = $request->file('image');
        $path = $file->store('about-section-four', 'public');

        // Update team member image
        $teamMembers[$memberIndex]['image'] = $path;
        $section->team_members = $teamMembers;
        $section->save();

        // Get updated section
        $updatedSection = AboutSectionFour::find($request->section_id);

        return $this->sendResponse([
            'member_index' => $memberIndex,
            'team_members' => $updatedSection->team_members,
        ], 'Team member image uploaded successfully');
    }

    // PUT /api/dana/about/section-four/{id} - UPDATE (Admin only)
    public function update(Request $request, $id)
    {
        $section = AboutSectionFour::find($id);
        
        if (!$section) {
            return $this->sendError('About section not found', [], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'team_members' => 'nullable|array',
            'team_members.*.name' => 'required_with:team_members|string',
            'team_members.*.position' => 'required_with:team_members|string',
            'team_members.*.image' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $data = [];
        
        if ($request->has('title')) {
            $data['title'] = $request->title;
        }
        if ($request->has('subtitle')) {
            $data['subtitle'] = $request->subtitle;
        }
        if ($request->has('team_members')) {
            $data['team_members'] = $request->team_members;
        }

        $section->update($data);

        // Refresh to get updated data
        $updatedSection = AboutSectionFour::find($id);

        return $this->sendResponse([
            'id' => $updatedSection->id,
            'title' => $updatedSection->title,
            'subtitle' => $updatedSection->subtitle,
            'team_members' => $updatedSection->team_members,
        ], 'About section four updated successfully');
    }

    // DELETE /api/dana/about/section-four/{id} - DELETE (Admin only)
    public function destroy($id)
    {
        $section = AboutSectionFour::find($id);
        
        if (!$section) {
            return $this->sendError('About section not found', [], 404);
        }

        // Delete all team member images from storage
        $teamMembers = $section->team_members;
        if (is_array($teamMembers)) {
            foreach ($teamMembers as $member) {
                if (isset($member['image']) && $member['image'] && !filter_var($member['image'], FILTER_VALIDATE_URL)) {
                    Storage::disk('public')->delete($member['image']);
                }
            }
        }
        
        $section->delete();

        return $this->sendResponse([], 'About section four deleted successfully');
    }

    // DELETE /api/dana/about/section-four/{id}/member/{index}/image - Delete specific team member image
    public function deleteMemberImage($id, $index)
    {
        $section = AboutSectionFour::find($id);
        
        if (!$section) {
            return $this->sendError('About section not found', [], 404);
        }

        $teamMembers = $section->team_members;
        
        if (!isset($teamMembers[$index])) {
            return $this->sendError('Team member not found', [], 404);
        }

        // Delete image from storage
        if (isset($teamMembers[$index]['image']) && $teamMembers[$index]['image'] && !filter_var($teamMembers[$index]['image'], FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($teamMembers[$index]['image']);
        }

        // Remove image field or set to null
        $teamMembers[$index]['image'] = null;
        $section->team_members = $teamMembers;
        $section->save();

        return $this->sendResponse([], 'Team member image deleted successfully');
    }
}