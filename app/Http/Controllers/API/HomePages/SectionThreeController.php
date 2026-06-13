<?php

namespace App\Http\Controllers\Api\HomePages;

use App\Http\Controllers\Api\BaseController;
use App\Models\HomePages\SectionThree;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SectionThreeController extends BaseController
{
    // GET /api/dana/section-three - Get all sections (Public)
    public function index()
    {
        $sections = SectionThree::orderBy('id', 'desc')->get();
        
        $data = $sections->map(function ($section) {
            return [
                'id' => $section->id,
                'title' => $section->title,
                'subtitle' => $section->subtitle,
                'cards' => $section->cards,
            ];
        });

        return $this->sendResponse($data, 'Sections retrieved successfully');
    }

    // GET /api/dana/section-three/{id} - Get single section (Public)
    public function show($id)
    {
        $section = SectionThree::find($id);
        
        if (!$section) {
            return $this->sendError('Section not found', [], 404);
        }

        return $this->sendResponse([
            'id' => $section->id,
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'cards' => $section->cards,
        ], 'Section retrieved successfully');
    }

    // POST /api/dana/section-three - CREATE (Admin only)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'cards' => 'required|array|min:1',
            'cards.*.title' => 'required|string',
            'cards.*.description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $section = SectionThree::create([
            'title' => $request->title ?? '— SIGNATURE EXPERIENCES',
            'subtitle' => $request->subtitle ?? 'Days that linger in memory.',
            'cards' => $request->cards,
        ]);

        return $this->sendResponse([
            'id' => $section->id,
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'cards' => $section->cards,
        ], 'Section created successfully', 201);
    }

    // PUT /api/dana/section-three/{id} - UPDATE (Admin only)
    public function update(Request $request, $id)
    {
        $section = SectionThree::find($id);
        
        if (!$section) {
            return $this->sendError('Section not found', [], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'cards' => 'nullable|array',
            'cards.*.title' => 'required_with:cards|string',
            'cards.*.description' => 'required_with:cards|string',
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
        if ($request->has('cards')) {
            $data['cards'] = $request->cards;
        }

        $section->update($data);

        return $this->sendResponse([
            'id' => $section->id,
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'cards' => $section->cards,
        ], 'Section updated successfully');
    }

    // DELETE /api/dana/section-three/{id} - DELETE (Admin only)
    public function destroy($id)
    {
        $section = SectionThree::find($id);
        
        if (!$section) {
            return $this->sendError('Section not found', [], 404);
        }
        
        $section->delete();

        return $this->sendResponse([], 'Section deleted successfully');
    }
}