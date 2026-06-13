<?php

namespace App\Http\Controllers\Api\HomePages;

use App\Http\Controllers\Api\BaseController;
use App\Models\HomePages\SectionSeven;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SectionSevenController extends BaseController
{
    // GET /api/dana/section-seven - Get all sections (Public)
    public function index()
    {
        $sections = SectionSeven::orderBy('id', 'desc')->get();
        
        $data = $sections->map(function ($section) {
            return [
                'id' => $section->id,
                'title' => $section->title,
                'subtitle' => $section->subtitle,
                'testimonials' => $section->testimonials,
            ];
        });

        return $this->sendResponse($data, 'Sections retrieved successfully');
    }

    // GET /api/dana/section-seven/{id} - Get single section (Public)
    public function show($id)
    {
        $section = SectionSeven::find($id);
        
        if (!$section) {
            return $this->sendError('Section not found', [], 404);
        }

        return $this->sendResponse([
            'id' => $section->id,
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'testimonials' => $section->testimonials,
        ], 'Section retrieved successfully');
    }

    // POST /api/dana/section-seven - CREATE (Admin only)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'testimonials' => 'required|array|min:1',
            'testimonials.*.text' => 'required|string',
            'testimonials.*.name' => 'required|string',
            'testimonials.*.location' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $section = SectionSeven::create([
            'title' => $request->title ?? '— GUEST WORDS',
            'subtitle' => $request->subtitle ?? 'Quiet praise, gratefully received.',
            'testimonials' => $request->testimonials,
        ]);

        return $this->sendResponse([
            'id' => $section->id,
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'testimonials' => $section->testimonials,
        ], 'Section created successfully', 201);
    }

    // PUT /api/dana/section-seven/{id} - UPDATE (Admin only)
    public function update(Request $request, $id)
    {
        $section = SectionSeven::find($id);
        
        if (!$section) {
            return $this->sendError('Section not found', [], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'testimonials' => 'nullable|array',
            'testimonials.*.text' => 'required_with:testimonials|string',
            'testimonials.*.name' => 'required_with:testimonials|string',
            'testimonials.*.location' => 'required_with:testimonials|string',
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
        if ($request->has('testimonials')) {
            $data['testimonials'] = $request->testimonials;
        }

        $section->update($data);

        return $this->sendResponse([
            'id' => $section->id,
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'testimonials' => $section->testimonials,
        ], 'Section updated successfully');
    }

    // DELETE /api/dana/section-seven/{id} - DELETE (Admin only)
    public function destroy($id)
    {
        $section = SectionSeven::find($id);
        
        if (!$section) {
            return $this->sendError('Section not found', [], 404);
        }
        
        $section->delete();

        return $this->sendResponse([], 'Section deleted successfully');
    }

    // DELETE /api/dana/section-seven/{id}/testimonial/{index} - Delete single testimonial
    public function deleteTestimonial($id, $index)
    {
        $section = SectionSeven::find($id);
        
        if (!$section) {
            return $this->sendError('Section not found', [], 404);
        }

        $testimonials = $section->testimonials;
        
        if (!isset($testimonials[$index])) {
            return $this->sendError('Testimonial not found', [], 404);
        }
        
        array_splice($testimonials, $index, 1);
        $section->update(['testimonials' => $testimonials]);

        return $this->sendResponse([], 'Testimonial deleted successfully');
    }
}