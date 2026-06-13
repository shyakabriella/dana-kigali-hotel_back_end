<?php

namespace App\Http\Controllers\Api\HomePages;

use App\Http\Controllers\Api\BaseController;
use App\Models\HomePages\SectionEight;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SectionEightController extends BaseController
{
    // GET /api/dana/section-eight - Get all sections (Public)
    public function index()
    {
        $sections = SectionEight::orderBy('id', 'desc')->get();
        
        $data = $sections->map(function ($section) {
            return [
                'id' => $section->id,
                'title' => $section->title,
                'description' => $section->description,
                'button_text' => $section->button_text,
            ];
        });

        return $this->sendResponse($data, 'Sections retrieved successfully');
    }

    // GET /api/dana/section-eight/{id} - Get single section (Public)
    public function show($id)
    {
        $section = SectionEight::find($id);
        
        if (!$section) {
            return $this->sendError('Section not found', [], 404);
        }

        return $this->sendResponse([
            'id' => $section->id,
            'title' => $section->title,
            'description' => $section->description,
            'button_text' => $section->button_text,
        ], 'Section retrieved successfully');
    }

    // POST /api/dana/section-eight - CREATE (Admin only)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'button_text' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $section = SectionEight::create([
            'title' => $request->title ?? '— MEETINGS & EVENTS',
            'description' => $request->description ?? 'A warm, exquisite, and elevated space for occasions of every scale.',
            'button_text' => $request->button_text ?? 'Plan Your Event',
        ]);

        return $this->sendResponse([
            'id' => $section->id,
            'title' => $section->title,
            'description' => $section->description,
            'button_text' => $section->button_text,
        ], 'Section created successfully', 201);
    }

    // PUT /api/dana/section-eight/{id} - UPDATE (Admin only)
    public function update(Request $request, $id)
    {
        $section = SectionEight::find($id);
        
        if (!$section) {
            return $this->sendError('Section not found', [], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'button_text' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $data = [];
        
        if ($request->has('title')) {
            $data['title'] = $request->title;
        }
        if ($request->has('description')) {
            $data['description'] = $request->description;
        }
        if ($request->has('button_text')) {
            $data['button_text'] = $request->button_text;
        }

        $section->update($data);

        return $this->sendResponse([
            'id' => $section->id,
            'title' => $section->title,
            'description' => $section->description,
            'button_text' => $section->button_text,
        ], 'Section updated successfully');
    }

    // DELETE /api/dana/section-eight/{id} - DELETE (Admin only)
    public function destroy($id)
    {
        $section = SectionEight::find($id);
        
        if (!$section) {
            return $this->sendError('Section not found', [], 404);
        }
        
        $section->delete();

        return $this->sendResponse([], 'Section deleted successfully');
    }
}