<?php

namespace App\Http\Controllers\Api\HomePages;

use App\Http\Controllers\Api\BaseController;
use App\Models\HomePages\SectionFour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SectionFourController extends BaseController
{
    // GET /api/dana/section-four - Get all sections (Public)
    public function index()
    {
        $sections = SectionFour::orderBy('id', 'desc')->get();
        
        $data = $sections->map(function ($section) {
            return [
                'id' => $section->id,
                'title' => $section->title,
                'subtitle' => $section->subtitle,
                'description' => $section->description,
                'amenities' => $section->amenities,
            ];
        });

        return $this->sendResponse($data, 'Sections retrieved successfully');
    }

    // GET /api/dana/section-four/{id} - Get single section (Public)
    public function show($id)
    {
        $section = SectionFour::find($id);
        
        if (!$section) {
            return $this->sendError('Section not found', [], 404);
        }

        return $this->sendResponse([
            'id' => $section->id,
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'description' => $section->description,
            'amenities' => $section->amenities,
        ], 'Section retrieved successfully');
    }

    // POST /api/dana/section-four - CREATE (Admin only)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'amenities' => 'required|array|min:1',
            'amenities.*' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $section = SectionFour::create([
            'title' => $request->title ?? '— HOTEL FACILITIES',
            'subtitle' => $request->subtitle ?? 'The finest amenities, considered for you.',
            'description' => $request->description ?? 'Everything that defines a perfect stay — quietly available, never imposed.',
            'amenities' => $request->amenities,
        ]);

        return $this->sendResponse([
            'id' => $section->id,
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'description' => $section->description,
            'amenities' => $section->amenities,
        ], 'Section created successfully', 201);
    }

    // PUT /api/dana/section-four/{id} - UPDATE (Admin only)
    public function update(Request $request, $id)
    {
        $section = SectionFour::find($id);
        
        if (!$section) {
            return $this->sendError('Section not found', [], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'amenities' => 'nullable|array',
            'amenities.*' => 'nullable|string',
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
        if ($request->has('description')) {
            $data['description'] = $request->description;
        }
        if ($request->has('amenities')) {
            $data['amenities'] = $request->amenities;
        }

        $section->update($data);

        return $this->sendResponse([
            'id' => $section->id,
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'description' => $section->description,
            'amenities' => $section->amenities,
        ], 'Section updated successfully');
    }

    // DELETE /api/dana/section-four/{id} - DELETE (Admin only)
    public function destroy($id)
    {
        $section = SectionFour::find($id);
        
        if (!$section) {
            return $this->sendError('Section not found', [], 404);
        }
        
        $section->delete();

        return $this->sendResponse([], 'Section deleted successfully');
    }
}