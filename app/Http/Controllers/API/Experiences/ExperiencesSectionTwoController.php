<?php

namespace App\Http\Controllers\Api\Experiences;

use App\Http\Controllers\Api\BaseController;
use App\Models\Experiences\ExperiencesSectionTwo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExperiencesSectionTwoController extends BaseController
{
    // GET /api/dana/experiences/section-two - Get all (Public)
    public function index()
    {
        $sections = ExperiencesSectionTwo::orderBy('id', 'desc')->get();
        
        $data = $sections->map(function ($section) {
            return [
                'id' => $section->id,
                'title' => $section->title,
                'subtitle' => $section->subtitle,
                'description' => $section->description,
                'button_one_text' => $section->button_one_text,
                'button_two_text' => $section->button_two_text,
            ];
        });

        return $this->sendResponse($data, 'Experiences section two retrieved successfully');
    }

    // GET /api/dana/experiences/section-two/{id} - Get single (Public)
    public function show($id)
    {
        $section = ExperiencesSectionTwo::find($id);
        
        if (!$section) {
            return $this->sendError('Experiences section not found', [], 404);
        }

        return $this->sendResponse([
            'id' => $section->id,
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'description' => $section->description,
            'button_one_text' => $section->button_one_text,
            'button_two_text' => $section->button_two_text,
        ], 'Experiences section retrieved successfully');
    }

    // POST /api/dana/experiences/section-two - CREATE (Admin only)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'button_one_text' => 'nullable|string|max:100',
            'button_two_text' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $section = ExperiencesSectionTwo::first();

        $data = [
            'title' => $request->title ?? ($section->title ?? '— BEGIN YOUR STORY'),
            'subtitle' => $request->subtitle ?? ($section->subtitle ?? 'Reserve your first experience.'),
            'description' => $request->description ?? ($section->description ?? 'Many experiences are exclusive to guests. Book a room and unlock the full ridge.'),
            'button_one_text' => $request->button_one_text ?? ($section->button_one_text ?? 'Reserve a Stay'),
            'button_two_text' => $request->button_two_text ?? ($section->button_two_text ?? 'View Rooms'),
        ];

        if ($section) {
            $section->update($data);
            $message = 'Experiences section two updated successfully';
        } else {
            $section = ExperiencesSectionTwo::create($data);
            $message = 'Experiences section two created successfully';
        }

        return $this->sendResponse([
            'id' => $section->id,
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'description' => $section->description,
            'button_one_text' => $section->button_one_text,
            'button_two_text' => $section->button_two_text,
        ], $message);
    }

    // PUT /api/dana/experiences/section-two/{id} - UPDATE (Admin only)
    public function update(Request $request, $id)
    {
        $section = ExperiencesSectionTwo::find($id);
        
        if (!$section) {
            return $this->sendError('Experiences section not found', [], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'button_one_text' => 'nullable|string|max:100',
            'button_two_text' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $data = [];
        
        if ($request->has('title')) $data['title'] = $request->title;
        if ($request->has('subtitle')) $data['subtitle'] = $request->subtitle;
        if ($request->has('description')) $data['description'] = $request->description;
        if ($request->has('button_one_text')) $data['button_one_text'] = $request->button_one_text;
        if ($request->has('button_two_text')) $data['button_two_text'] = $request->button_two_text;

        $section->update($data);

        return $this->sendResponse([
            'id' => $section->id,
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'description' => $section->description,
            'button_one_text' => $section->button_one_text,
            'button_two_text' => $section->button_two_text,
        ], 'Experiences section two updated successfully');
    }

    // DELETE /api/dana/experiences/section-two/{id} - DELETE (Admin only)
    public function destroy($id)
    {
        $section = ExperiencesSectionTwo::find($id);
        
        if (!$section) {
            return $this->sendError('Experiences section not found', [], 404);
        }
        
        $section->delete();

        return $this->sendResponse([], 'Experiences section two deleted successfully');
    }
}