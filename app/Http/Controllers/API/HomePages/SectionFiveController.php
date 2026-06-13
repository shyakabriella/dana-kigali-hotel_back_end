<?php

namespace App\Http\Controllers\Api\HomePages;

use App\Http\Controllers\Api\BaseController;
use App\Models\HomePages\SectionFive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SectionFiveController extends BaseController
{
    // GET /api/dana/section-five - Get all sections (Public)
    public function index()
    {
        $sections = SectionFive::orderBy('id', 'desc')->get();
        
        $data = $sections->map(function ($section) {
            return [
                'id' => $section->id,
                'title' => $section->title,
                'subtitle' => $section->subtitle,
                'description' => $section->description,
                'left_image' => $section->left_image_url,
                'items' => $section->items,
                'button_text' => $section->button_text,
            ];
        });

        return $this->sendResponse($data, 'Sections retrieved successfully');
    }

    // GET /api/dana/section-five/{id} - Get single section (Public)
    public function show($id)
    {
        $section = SectionFive::find($id);
        
        if (!$section) {
            return $this->sendError('Section not found', [], 404);
        }

        return $this->sendResponse([
            'id' => $section->id,
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'description' => $section->description,
            'left_image' => $section->left_image_url,
            'items' => $section->items,
            'button_text' => $section->button_text,
        ], 'Section retrieved successfully');
    }

    // POST /api/dana/section-five - CREATE (Admin only)
    public function store(Request $request)
    {
        $rules = [
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*' => 'required|string',
            'button_text' => 'nullable|string|max:100',
        ];

        // Check if file upload or URL
        if ($request->hasFile('left_image')) {
            $rules['left_image'] = 'required|image|mimes:jpeg,png,jpg,webp|max:5120';
        } else {
            $rules['left_image'] = 'nullable|string|max:1000';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('left_image')) {
            $file = $request->file('left_image');
            $imagePath = $file->store('section-five', 'public');
        } elseif ($request->has('left_image') && !empty($request->left_image)) {
            $imagePath = $request->left_image;
        }

        $section = SectionFive::create([
            'title' => $request->title ?? '— RELAXING MOMENTS',
            'subtitle' => $request->subtitle ?? 'Spa & Thermal Center.',
            'description' => $request->description ?? 'A subterranean retreat of stone, candlelight and water — designed for stillness.',
            'left_image' => $imagePath,
            'items' => $request->items,
            'button_text' => $request->button_text ?? 'reserve treatment',
        ]);

        return $this->sendResponse([
            'id' => $section->id,
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'description' => $section->description,
            'left_image' => $section->left_image_url,
            'items' => $section->items,
            'button_text' => $section->button_text,
        ], 'Section created successfully', 201);
    }

    // POST /api/dana/section-five/upload-image - Upload left image (Admin only)
    public function uploadImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'section_id' => 'required|integer|exists:section_fives,id',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $section = SectionFive::find($request->section_id);

        // Delete old image if exists and is local file
        if ($section->left_image && !filter_var($section->left_image, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($section->left_image);
        }

        // Store new image
        $file = $request->file('image');
        $path = $file->store('section-five', 'public');

        // Update section
        $section->update(['left_image' => $path]);

        return $this->sendResponse([
            'path' => $path,
            'url' => Storage::url($path),
        ], 'Image uploaded successfully');
    }

    // PUT /api/dana/section-five/{id} - UPDATE (Admin only)
    public function update(Request $request, $id)
    {
        $section = SectionFive::find($id);
        
        if (!$section) {
            return $this->sendError('Section not found', [], 404);
        }

        $rules = [
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'items' => 'nullable|array',
            'items.*' => 'nullable|string',
            'button_text' => 'nullable|string|max:100',
        ];

        if ($request->hasFile('left_image')) {
            $rules['left_image'] = 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120';
        } else {
            $rules['left_image'] = 'nullable|string|max:1000';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $data = [];
        
        if ($request->has('title')) $data['title'] = $request->title;
        if ($request->has('subtitle')) $data['subtitle'] = $request->subtitle;
        if ($request->has('description')) $data['description'] = $request->description;
        if ($request->has('items')) $data['items'] = $request->items;
        if ($request->has('button_text')) $data['button_text'] = $request->button_text;

        // Handle image upload
        if ($request->hasFile('left_image')) {
            if ($section->left_image && !filter_var($section->left_image, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($section->left_image);
            }
            $file = $request->file('left_image');
            $data['left_image'] = $file->store('section-five', 'public');
        } elseif ($request->has('left_image')) {
            $data['left_image'] = $request->left_image;
        }

        $section->update($data);

        return $this->sendResponse([
            'id' => $section->id,
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'description' => $section->description,
            'left_image' => $section->left_image_url,
            'items' => $section->items,
            'button_text' => $section->button_text,
        ], 'Section updated successfully');
    }

    // DELETE /api/dana/section-five/{id} - DELETE (Admin only)
    public function destroy($id)
    {
        $section = SectionFive::find($id);
        
        if (!$section) {
            return $this->sendError('Section not found', [], 404);
        }

        // Delete image from storage if exists and is local file
        if ($section->left_image && !filter_var($section->left_image, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($section->left_image);
        }
        
        $section->delete();

        return $this->sendResponse([], 'Section deleted successfully');
    }

    // DELETE /api/dana/section-five/{id}/image - Delete left image only
    public function deleteImage($id)
    {
        $section = SectionFive::find($id);
        
        if (!$section) {
            return $this->sendError('Section not found', [], 404);
        }

        if ($section->left_image && !filter_var($section->left_image, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($section->left_image);
        }
        
        $section->update(['left_image' => null]);

        return $this->sendResponse([], 'Image deleted successfully');
    }
}