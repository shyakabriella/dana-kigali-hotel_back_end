<?php

namespace App\Http\Controllers\Api\About;

use App\Http\Controllers\Api\BaseController;
use App\Models\About\AboutSectionFive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AboutSectionFiveController extends BaseController
{
    // GET /api/dana/about/section-five - Get all (Public)
    public function index()
    {
        $sections = AboutSectionFive::orderBy('id', 'desc')->get();
        
        $data = $sections->map(function ($section) {
            return [
                'id' => $section->id,
                'title' => $section->title,
                'subtitle' => $section->subtitle,
                'description' => $section->description,
                'left_image' => $section->left_image_url,
                'button_text' => $section->button_text,
                'secondary_text' => $section->secondary_text,
            ];
        });

        return $this->sendResponse($data, 'About section five retrieved successfully');
    }

    // GET /api/dana/about/section-five/{id} - Get single (Public)
    public function show($id)
    {
        $section = AboutSectionFive::find($id);
        
        if (!$section) {
            return $this->sendError('About section not found', [], 404);
        }

        return $this->sendResponse([
            'id' => $section->id,
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'description' => $section->description,
            'left_image' => $section->left_image_url,
            'button_text' => $section->button_text,
            'secondary_text' => $section->secondary_text,
        ], 'About section retrieved successfully');
    }

    // POST /api/dana/about/section-five - CREATE (Admin only)
    public function store(Request $request)
    {
        $rules = [
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'button_text' => 'nullable|string|max:100',
            'secondary_text' => 'nullable|string|max:100',
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

        $section = AboutSectionFive::first();
        $imagePath = null;

        if ($request->hasFile('left_image')) {
            if ($section && $section->left_image && !filter_var($section->left_image, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($section->left_image);
            }
            $file = $request->file('left_image');
            $imagePath = $file->store('about-section-five', 'public');
        } elseif ($request->has('left_image') && !empty($request->left_image)) {
            $imagePath = $request->left_image;
        } elseif ($section && $section->left_image) {
            $imagePath = $section->left_image;
        }

        $data = [
            'title' => $request->title ?? ($section->title ?? '— COME STAY'),
            'subtitle' => $request->subtitle ?? ($section->subtitle ?? 'A warm welcome is waiting.'),
            'description' => $request->description ?? ($section->description ?? 'Reserve a room and experience the true meaning of home in the heart of Kigali.'),
            'left_image' => $imagePath,
            'button_text' => $request->button_text ?? ($section->button_text ?? 'Back to Home'),
            'secondary_text' => $request->secondary_text ?? ($section->secondary_text ?? 'Reserve a Stay'),
        ];

        if ($section) {
            $section->update($data);
            $message = 'About section five updated successfully';
        } else {
            $section = AboutSectionFive::create($data);
            $message = 'About section five created successfully';
        }

        return $this->sendResponse([
            'id' => $section->id,
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'description' => $section->description,
            'left_image' => $section->left_image_url,
            'button_text' => $section->button_text,
            'secondary_text' => $section->secondary_text,
        ], $message);
    }

    // POST /api/dana/about/section-five/upload-image - Upload left image (Admin only)
    public function uploadImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'section_id' => 'required|integer|exists:about_section_five,id',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $section = AboutSectionFive::find($request->section_id);

        // Delete old image if exists and is local file
        if ($section->left_image && !filter_var($section->left_image, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($section->left_image);
        }

        // Store new image
        $file = $request->file('image');
        $path = $file->store('about-section-five', 'public');

        // Update section
        $section->update(['left_image' => $path]);

        $updatedSection = AboutSectionFive::find($request->section_id);

        return $this->sendResponse([
            'path' => $path,
            'url' => Storage::url($path),
            'left_image' => $updatedSection->left_image_url,
        ], 'Image uploaded successfully');
    }

    // PUT /api/dana/about/section-five/{id} - UPDATE (Admin only)
    public function update(Request $request, $id)
    {
        $section = AboutSectionFive::find($id);
        
        if (!$section) {
            return $this->sendError('About section not found', [], 404);
        }

        $rules = [
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'button_text' => 'nullable|string|max:100',
            'secondary_text' => 'nullable|string|max:100',
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
        if ($request->has('button_text')) $data['button_text'] = $request->button_text;
        if ($request->has('secondary_text')) $data['secondary_text'] = $request->secondary_text;

        if ($request->hasFile('left_image')) {
            if ($section->left_image && !filter_var($section->left_image, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($section->left_image);
            }
            $file = $request->file('left_image');
            $data['left_image'] = $file->store('about-section-five', 'public');
        } elseif ($request->has('left_image')) {
            $data['left_image'] = $request->left_image;
        }

        $section->update($data);

        $updatedSection = AboutSectionFive::find($id);

        return $this->sendResponse([
            'id' => $updatedSection->id,
            'title' => $updatedSection->title,
            'subtitle' => $updatedSection->subtitle,
            'description' => $updatedSection->description,
            'left_image' => $updatedSection->left_image_url,
            'button_text' => $updatedSection->button_text,
            'secondary_text' => $updatedSection->secondary_text,
        ], 'About section five updated successfully');
    }

    // DELETE /api/dana/about/section-five/{id} - DELETE (Admin only)
    public function destroy($id)
    {
        $section = AboutSectionFive::find($id);
        
        if (!$section) {
            return $this->sendError('About section not found', [], 404);
        }

        if ($section->left_image && !filter_var($section->left_image, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($section->left_image);
        }
        
        $section->delete();

        return $this->sendResponse([], 'About section five deleted successfully');
    }

    // DELETE /api/dana/about/section-five/{id}/image - Delete left image only
    public function deleteImage($id)
    {
        $section = AboutSectionFive::find($id);
        
        if (!$section) {
            return $this->sendError('About section not found', [], 404);
        }

        if ($section->left_image && !filter_var($section->left_image, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($section->left_image);
        }
        
        $section->update(['left_image' => null]);

        return $this->sendResponse([], 'Image deleted successfully');
    }
}