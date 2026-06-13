<?php

namespace App\Http\Controllers\Api\Experiences;

use App\Http\Controllers\Api\BaseController;
use App\Models\Experiences\ExperiencesSectionOne;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ExperiencesSectionOneController extends BaseController
{
    // GET /api/dana/experiences/section-one - Get all (Public)
    public function index()
    {
        $sections = ExperiencesSectionOne::orderBy('id', 'desc')->get();
        
        $data = $sections->map(function ($section) {
            return [
                'id' => $section->id,
                'title' => $section->title,
                'subtitle' => $section->subtitle,
                'description' => $section->description,
                'experiences' => $section->experiences,
            ];
        });

        return $this->sendResponse($data, 'Experiences section one retrieved successfully');
    }

    // GET /api/dana/experiences/section-one/{id} - Get single (Public)
    public function show($id)
    {
        $section = ExperiencesSectionOne::find($id);
        
        if (!$section) {
            return $this->sendError('Experiences section not found', [], 404);
        }

        return $this->sendResponse([
            'id' => $section->id,
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'description' => $section->description,
            'experiences' => $section->experiences,
        ], 'Experiences section retrieved successfully');
    }

    // POST /api/dana/experiences/section-one - CREATE (Admin only)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'experiences' => 'required|array|min:1',
            'experiences.*.category' => 'required|string',
            'experiences.*.title' => 'required|string',
            'experiences.*.description' => 'required|string',
            'experiences.*.details' => 'nullable|string',
            'experiences.*.image' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $section = ExperiencesSectionOne::create([
            'title' => $request->title ?? '— SIX WAYS TO REMEMBER',
            'subtitle' => $request->subtitle ?? 'Days that linger.',
            'description' => $request->description ?? 'At DANA KIGALI HOTEL, the landscape is not a backdrop — it is the main event. Each experience is designed to draw you deeper into the ridge, the forest, and the quiet rhythm of mountain life.',
            'experiences' => $request->experiences,
        ]);

        return $this->sendResponse([
            'id' => $section->id,
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'description' => $section->description,
            'experiences' => $section->experiences,
        ], 'Experiences section created successfully', 201);
    }

    // PUT /api/dana/experiences/section-one/{id} - UPDATE (Admin only)
    public function update(Request $request, $id)
    {
        $section = ExperiencesSectionOne::find($id);
        
        if (!$section) {
            return $this->sendError('Experiences section not found', [], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'experiences' => 'nullable|array',
            'experiences.*.category' => 'required_with:experiences|string',
            'experiences.*.title' => 'required_with:experiences|string',
            'experiences.*.description' => 'required_with:experiences|string',
            'experiences.*.details' => 'nullable|string',
            'experiences.*.image' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $data = [];
        
        if ($request->has('title')) $data['title'] = $request->title;
        if ($request->has('subtitle')) $data['subtitle'] = $request->subtitle;
        if ($request->has('description')) $data['description'] = $request->description;
        if ($request->has('experiences')) $data['experiences'] = $request->experiences;

        $section->update($data);

        return $this->sendResponse([
            'id' => $section->id,
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'description' => $section->description,
            'experiences' => $section->experiences,
        ], 'Experiences section updated successfully');
    }

    // DELETE /api/dana/experiences/section-one/{id} - DELETE (Admin only)
    public function destroy($id)
    {
        $section = ExperiencesSectionOne::find($id);
        
        if (!$section) {
            return $this->sendError('Experiences section not found', [], 404);
        }
        
        // Delete all images from storage
        $experiences = $section->experiences;
        if (is_array($experiences)) {
            foreach ($experiences as $experience) {
                if (isset($experience['image']) && $experience['image'] && !filter_var($experience['image'], FILTER_VALIDATE_URL)) {
                    Storage::disk('public')->delete($experience['image']);
                }
            }
        }
        
        $section->delete();

        return $this->sendResponse([], 'Experiences section deleted successfully');
    }
}