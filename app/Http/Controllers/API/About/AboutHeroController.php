<?php

namespace App\Http\Controllers\Api\About;

use App\Http\Controllers\Api\BaseController;
use App\Models\About\AboutHero;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AboutHeroController extends BaseController
{
    // GET /api/dana/about/hero - Get all (Public)
    public function index()
    {
        $heroes = AboutHero::orderBy('id', 'desc')->get();
        
        $data = $heroes->map(function ($hero) {
            return [
                'id' => $hero->id,
                'title' => $hero->title,
                'subtitle' => $hero->subtitle,
                'destination' => $hero->destination,
                'background_image' => $hero->background_image_url,
            ];
        });

        return $this->sendResponse($data, 'About hero retrieved successfully');
    }

    // GET /api/dana/about/hero/{id} - Get single (Public)
    public function show($id)
    {
        $hero = AboutHero::find($id);
        
        if (!$hero) {
            return $this->sendError('About hero not found', [], 404);
        }

        return $this->sendResponse([
            'id' => $hero->id,
            'title' => $hero->title,
            'subtitle' => $hero->subtitle,
            'destination' => $hero->destination,
            'background_image' => $hero->background_image_url,
        ], 'About hero retrieved successfully');
    }

    // POST /api/dana/about/hero - CREATE (Admin only)
    // Handles both URL and file upload for background_image
    public function store(Request $request)
    {
        $rules = [
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'destination' => 'nullable|string|max:255',
        ];

        // Check if file upload or URL
        if ($request->hasFile('background_image')) {
            $rules['background_image'] = 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120';
        } else {
            $rules['background_image'] = 'nullable|string|max:1000';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $hero = AboutHero::first();
        $imagePath = null;

        // Handle image upload or URL
        if ($request->hasFile('background_image')) {
            if ($hero && $hero->background_image && !filter_var($hero->background_image, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($hero->background_image);
            }
            $file = $request->file('background_image');
            $imagePath = $file->store('about-hero', 'public');
        } elseif ($request->has('background_image') && !empty($request->background_image)) {
            $imagePath = $request->background_image;
        } elseif ($hero && $hero->background_image) {
            $imagePath = $hero->background_image;
        }

        $data = [
            'title' => $request->title ?? ($hero->title ?? '— OUR STORY'),
            'subtitle' => $request->subtitle ?? ($hero->subtitle ?? 'About DANA KIGALI HOTEL'),
            'destination' => $request->destination ?? ($hero->destination ?? 'Home/About'),
            'background_image' => $imagePath,
        ];

        if ($hero) {
            $hero->update($data);
            $message = 'About hero updated successfully';
        } else {
            $hero = AboutHero::create($data);
            $message = 'About hero created successfully';
        }

        return $this->sendResponse([
            'id' => $hero->id,
            'title' => $hero->title,
            'subtitle' => $hero->subtitle,
            'destination' => $hero->destination,
            'background_image' => $hero->background_image_url,
        ], $message);
    }

    // PUT /api/dana/about/hero/{id} - UPDATE (Admin only)
    public function update(Request $request, $id)
    {
        $hero = AboutHero::find($id);
        
        if (!$hero) {
            return $this->sendError('About hero not found', [], 404);
        }

        $rules = [
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'destination' => 'nullable|string|max:255',
        ];

        if ($request->hasFile('background_image')) {
            $rules['background_image'] = 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120';
        } else {
            $rules['background_image'] = 'nullable|string|max:1000';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $data = [];
        
        if ($request->has('title')) $data['title'] = $request->title;
        if ($request->has('subtitle')) $data['subtitle'] = $request->subtitle;
        if ($request->has('destination')) $data['destination'] = $request->destination;

        // Handle image upload
        if ($request->hasFile('background_image')) {
            if ($hero->background_image && !filter_var($hero->background_image, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($hero->background_image);
            }
            $file = $request->file('background_image');
            $data['background_image'] = $file->store('about-hero', 'public');
        } elseif ($request->has('background_image')) {
            $data['background_image'] = $request->background_image;
        }

        $hero->update($data);

        return $this->sendResponse([
            'id' => $hero->id,
            'title' => $hero->title,
            'subtitle' => $hero->subtitle,
            'destination' => $hero->destination,
            'background_image' => $hero->background_image_url,
        ], 'About hero updated successfully');
    }

    // DELETE /api/dana/about/hero/{id} - DELETE (Admin only)
    public function destroy($id)
    {
        $hero = AboutHero::find($id);
        
        if (!$hero) {
            return $this->sendError('About hero not found', [], 404);
        }

        if ($hero->background_image && !filter_var($hero->background_image, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($hero->background_image);
        }
        
        $hero->delete();

        return $this->sendResponse([], 'About hero deleted successfully');
    }
}