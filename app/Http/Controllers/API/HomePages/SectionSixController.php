<?php

namespace App\Http\Controllers\Api\HomePages;

use App\Http\Controllers\Api\BaseController;
use App\Models\HomePages\SectionSix;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SectionSixController extends BaseController
{
    public function index()
    {
        $sections = SectionSix::orderBy('id', 'desc')->get();
        
        $data = $sections->map(function ($section) {
            return [
                'id' => $section->id,
                'title' => $section->title,
                'subtitle' => $section->subtitle,
                'gallery' => $section->gallery,
            ];
        });

        return $this->sendResponse($data, 'Sections retrieved successfully');
    }

    public function show($id)
    {
        $section = SectionSix::find($id);
        
        if (!$section) {
            return $this->sendError('Section not found', [], 404);
        }

        return $this->sendResponse([
            'id' => $section->id,
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'gallery' => $section->gallery,
        ], 'Section retrieved successfully');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'gallery' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $section = SectionSix::create([
            'title' => $request->title ?? '— MOMENTS',
            'subtitle' => $request->subtitle ?? 'A glimpse of life on the ridge.',
            'gallery' => $request->gallery ?? [],
        ]);

        return $this->sendResponse([
            'id' => $section->id,
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'gallery' => $section->gallery,
        ], 'Section created successfully', 201);
    }

    public function uploadImages(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'images' => 'required|array',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'section_id' => 'required|integer|exists:section_sixes,id',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $section = SectionSix::find($request->section_id);
        $existingGallery = $section->gallery_paths;
        $uploadedImages = [];

        foreach ($request->file('images') as $image) {
            $path = $image->store('section-six/gallery', 'public');
            $uploadedImages[] = $path;
        }

        $allImages = array_merge($existingGallery, $uploadedImages);
        $section->update(['gallery' => $allImages]);
        $updatedSection = SectionSix::find($request->section_id);

        return $this->sendResponse([
            'added_images' => count($uploadedImages),
            'total_images' => count($allImages),
            'gallery' => $updatedSection->gallery,
        ], 'Images uploaded successfully');
    }

    public function addImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'section_id' => 'required|integer|exists:section_sixes,id',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $section = SectionSix::find($request->section_id);
        $existingGallery = $section->gallery_paths;
        
        $path = $request->file('image')->store('section-six/gallery', 'public');
        $existingGallery[] = $path;
        $section->update(['gallery' => $existingGallery]);

        $updatedSection = SectionSix::find($request->section_id);

        return $this->sendResponse([
            'added_image' => Storage::url($path),
            'total_images' => count($existingGallery),
            'gallery' => $updatedSection->gallery,
        ], 'Image added successfully');
    }

    // ✅ NEW: Replace image at specific index
    public function replaceImage(Request $request, $id, $index)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $section = SectionSix::find($id);

        if (!$section) {
            return $this->sendError('Section not found', [], 404);
        }

        $galleryPaths = $section->gallery_paths;
        $index = (int) $index;

        // Delete the old image file if it exists at that index
        if (isset($galleryPaths[$index])) {
            Storage::disk('public')->delete($galleryPaths[$index]);
        }

        // Store the new image at the same index
        $newPath = $request->file('image')->store('section-six/gallery', 'public');
        $galleryPaths[$index] = $newPath;

        $section->update(['gallery' => $galleryPaths]);

        $updatedSection = SectionSix::find($id);

        return $this->sendResponse([
            'replaced_image' => Storage::url($newPath),
            'index' => $index,
            'gallery' => $updatedSection->gallery,
        ], 'Image replaced successfully');
    }

    public function update(Request $request, $id)
    {
        $section = SectionSix::find($id);
        
        if (!$section) {
            return $this->sendError('Section not found', [], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'gallery' => 'nullable|array',
            'gallery.*' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $data = [];
        
        if ($request->has('title')) $data['title'] = $request->title;
        if ($request->has('subtitle')) $data['subtitle'] = $request->subtitle;
        if ($request->has('gallery')) $data['gallery'] = $request->gallery;

        $section->update($data);

        return $this->sendResponse([
            'id' => $section->id,
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'gallery' => $section->gallery,
        ], 'Section updated successfully');
    }

    public function deleteImage($id, $index)
    {
        $section = SectionSix::find($id);
        
        if (!$section) {
            return $this->sendError('Section not found', [], 404);
        }

        $galleryPaths = $section->gallery_paths;
        
        if (!isset($galleryPaths[$index])) {
            return $this->sendError('Image not found', [], 404);
        }

        Storage::disk('public')->delete($galleryPaths[$index]);
        array_splice($galleryPaths, $index, 1);
        $section->update(['gallery' => $galleryPaths]);

        return $this->sendResponse([], 'Image deleted successfully');
    }

    public function deleteAllImages($id)
    {
        $section = SectionSix::find($id);
        
        if (!$section) {
            return $this->sendError('Section not found', [], 404);
        }

        foreach ($section->gallery_paths as $image) {
            Storage::disk('public')->delete($image);
        }
        
        $section->update(['gallery' => []]);

        return $this->sendResponse([], 'All images deleted successfully');
    }

    public function destroy($id)
    {
        $section = SectionSix::find($id);
        
        if (!$section) {
            return $this->sendError('Section not found', [], 404);
        }

        foreach ($section->gallery_paths as $image) {
            Storage::disk('public')->delete($image);
        }
        
        $section->delete();

        return $this->sendResponse([], 'Section deleted successfully');
    }
}