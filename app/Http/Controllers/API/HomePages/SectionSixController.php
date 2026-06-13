<?php

namespace App\Http\Controllers\Api\HomePages;

use App\Http\Controllers\Api\BaseController;
use App\Models\HomePages\SectionSix;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SectionSixController extends BaseController
{
    // GET /api/dana/section-six - Get all sections (Public)
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

    // GET /api/dana/section-six/{id} - Get single section (Public)
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

    // POST /api/dana/section-six - CREATE (Admin only)
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

    // POST /api/dana/section-six/upload-images - Upload multiple images (Admin only)
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

        // Merge existing with new images
        $allImages = array_merge($existingGallery, $uploadedImages);
        
        // Update gallery with paths (not URLs)
        $section->update(['gallery' => $allImages]);

        // Get updated section with URLs
        $updatedSection = SectionSix::find($request->section_id);

        return $this->sendResponse([
            'added_images' => count($uploadedImages),
            'total_images' => count($allImages),
            'gallery' => $updatedSection->gallery,
        ], 'Images uploaded successfully');
    }

    // POST /api/dana/section-six/add-image - Add single image (Admin only)
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
        
        // Store new image
        $file = $request->file('image');
        $path = $file->store('section-six/gallery', 'public');
        
        // Add to gallery
        $existingGallery[] = $path;
        $section->update(['gallery' => $existingGallery]);

        // Get updated section with URLs
        $updatedSection = SectionSix::find($request->section_id);

        return $this->sendResponse([
            'added_image' => Storage::url($path),
            'total_images' => count($existingGallery),
            'gallery' => $updatedSection->gallery,
        ], 'Image added successfully');
    }

    // PUT /api/dana/section-six/{id} - UPDATE (Admin only)
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
        
        if ($request->has('title')) {
            $data['title'] = $request->title;
        }
        if ($request->has('subtitle')) {
            $data['subtitle'] = $request->subtitle;
        }
        if ($request->has('gallery')) {
            $data['gallery'] = $request->gallery;
        }

        $section->update($data);

        return $this->sendResponse([
            'id' => $section->id,
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'gallery' => $section->gallery,
        ], 'Section updated successfully');
    }

    // DELETE /api/dana/section-six/{id}/image/{index} - Delete specific image (Admin only)
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

        // Delete file from storage
        Storage::disk('public')->delete($galleryPaths[$index]);
        
        // Remove from array
        array_splice($galleryPaths, $index, 1);
        
        // Update gallery
        $section->update(['gallery' => $galleryPaths]);

        return $this->sendResponse([], 'Image deleted successfully');
    }

    // DELETE /api/dana/section-six/{id}/images - Delete all images (Admin only)
    public function deleteAllImages($id)
    {
        $section = SectionSix::find($id);
        
        if (!$section) {
            return $this->sendError('Section not found', [], 404);
        }

        $galleryPaths = $section->gallery_paths;
        
        // Delete all files from storage
        foreach ($galleryPaths as $image) {
            Storage::disk('public')->delete($image);
        }
        
        // Clear gallery
        $section->update(['gallery' => []]);

        return $this->sendResponse([], 'All images deleted successfully');
    }

    // DELETE /api/dana/section-six/{id} - DELETE section (Admin only)
    public function destroy($id)
    {
        $section = SectionSix::find($id);
        
        if (!$section) {
            return $this->sendError('Section not found', [], 404);
        }

        // Delete all images from storage
        $galleryPaths = $section->gallery_paths;
        foreach ($galleryPaths as $image) {
            Storage::disk('public')->delete($image);
        }
        
        $section->delete();

        return $this->sendResponse([], 'Section deleted successfully');
    }
}