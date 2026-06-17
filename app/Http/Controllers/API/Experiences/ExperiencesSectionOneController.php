<?php

namespace App\Http\Controllers\Api\Experiences;

use App\Http\Controllers\Api\BaseController;
use App\Models\Experiences\ExperiencesSectionOne;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ExperiencesSectionOneController extends BaseController
{
    // Helper function to process experiences with image URLs
    private function processExperiences($experiences)
    {
        if (empty($experiences)) {
            return [];
        }
        
        return array_map(function ($experience) {
            if (isset($experience['image']) && $experience['image'] && !filter_var($experience['image'], FILTER_VALIDATE_URL)) {
                $experience['image_url'] = Storage::url($experience['image']);
            } else {
                $experience['image_url'] = $experience['image'] ?? null;
            }
            return $experience;
        }, $experiences);
    }

    // GET /api/dana/experiences/section-one - Get all (Public)
    public function index()
    {
        $sections = ExperiencesSectionOne::orderBy('id', 'desc')->get();
        
        $data = $sections->map(function ($section) {
            $processedExperiences = $this->processExperiences($section->experiences ?? []);
            
            return [
                'id' => $section->id,
                'title' => $section->title,
                'subtitle' => $section->subtitle,
                'description' => $section->description,
                'experiences' => $processedExperiences,
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

        $processedExperiences = $this->processExperiences($section->experiences ?? []);

        return $this->sendResponse([
            'id' => $section->id,
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'description' => $section->description,
            'experiences' => $processedExperiences,
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

        $processedExperiences = $this->processExperiences($section->experiences ?? []);

        return $this->sendResponse([
            'id' => $section->id,
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'description' => $section->description,
            'experiences' => $processedExperiences,
        ], 'Experiences section created successfully', 201);
    }

    // POST /api/dana/experiences/section-one/upload-image - Upload experience image (Admin only)
    public function uploadImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'experience_index' => 'required|integer',
            'section_id' => 'required|integer|exists:experiences_section_one,id',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $section = ExperiencesSectionOne::find($request->section_id);
        $experiences = $section->experiences ?? [];
        $experienceIndex = (int) $request->experience_index;

        if (!isset($experiences[$experienceIndex])) {
            return $this->sendError('Experience not found', [], 404);
        }

        // Delete old image if exists and is local file
        if (isset($experiences[$experienceIndex]['image']) && $experiences[$experienceIndex]['image'] && !filter_var($experiences[$experienceIndex]['image'], FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($experiences[$experienceIndex]['image']);
        }

        // Store new image
        $file = $request->file('image');
        $path = $file->store('experiences-section-one', 'public');

        // Update ONLY the specific experience's image
        $experiences[$experienceIndex]['image'] = $path;
        
        // Save the entire experiences array back to the section
        $section->experiences = $experiences;
        $section->save();

        // Get updated section and process experiences
        $updatedSection = ExperiencesSectionOne::find($request->section_id);
        $processedExperiences = $this->processExperiences($updatedSection->experiences ?? []);

        return $this->sendResponse([
            'experience_index' => $experienceIndex,
            'experiences' => $processedExperiences,
        ], 'Experience image uploaded successfully');
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
        if ($request->has('experiences')) {
            $existingExperiences = $section->experiences ?? [];
            $newExperiences = $request->experiences;
            
            // Preserve existing image paths if not provided in update
            foreach ($newExperiences as $key => $exp) {
                if (!isset($exp['image']) && isset($existingExperiences[$key]['image'])) {
                    $newExperiences[$key]['image'] = $existingExperiences[$key]['image'];
                }
            }
            $data['experiences'] = $newExperiences;
        }

        $section->update($data);

        $processedExperiences = $this->processExperiences($section->experiences ?? []);

        return $this->sendResponse([
            'id' => $section->id,
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'description' => $section->description,
            'experiences' => $processedExperiences,
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