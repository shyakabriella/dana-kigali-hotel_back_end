<?php

namespace App\Http\Controllers\Api\Rooms;

use App\Http\Controllers\Api\BaseController;
use App\Models\Rooms\RoomsHero;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class RoomsHeroController extends BaseController
{
    // GET /api/dana/rooms/hero - Get all (Public)
    public function index()
    {
        $heroes = RoomsHero::orderBy('id', 'desc')->get();
        
        $data = $heroes->map(function ($hero) {
            return [
                'id' => $hero->id,
                'title' => $hero->title,
                'subtitle' => $hero->subtitle,
                'destination' => $hero->destination,
                'background_image' => $hero->background_image_url,
            ];
        });

        return $this->sendResponse($data, 'Rooms hero retrieved successfully');
    }

    // GET /api/dana/rooms/hero/{id} - Get single (Public)
    public function show($id)
    {
        $hero = RoomsHero::find($id);
        
        if (!$hero) {
            return $this->sendError('Rooms hero not found', [], 404);
        }

        return $this->sendResponse([
            'id' => $hero->id,
            'title' => $hero->title,
            'subtitle' => $hero->subtitle,
            'destination' => $hero->destination,
            'background_image' => $hero->background_image_url,
        ], 'Rooms hero retrieved successfully');
    }

    // POST /api/dana/rooms/hero - CREATE (Admin only)
    // Handles both URL and file upload for background_image
    public function store(Request $request)
    {
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

        $imagePath = null;

        // Handle image upload or URL
        if ($request->hasFile('background_image')) {
            $file = $request->file('background_image');
            $imagePath = $file->store('rooms-hero', 'public');
        } elseif ($request->has('background_image') && !empty($request->background_image)) {
            $imagePath = $request->background_image;
        }

        $hero = RoomsHero::create([
            'title' => $request->title ?? '— THE RIDGE COLLECTION',
            'subtitle' => $request->subtitle ?? 'Rooms & Suites',
            'destination' => $request->destination ?? 'Home/Rooms',
            'background_image' => $imagePath,
        ]);

        return $this->sendResponse([
            'id' => $hero->id,
            'title' => $hero->title,
            'subtitle' => $hero->subtitle,
            'destination' => $hero->destination,
            'background_image' => $hero->background_image_url,
        ], 'Rooms hero created successfully');
    }

    // PUT /api/dana/rooms/hero/{id} - UPDATE (Admin only)
    // Handles both URL and file upload for background_image
    public function update(Request $request, $id)
    {
        $hero = RoomsHero::find($id);
        
        if (!$hero) {
            return $this->sendError('Rooms hero not found', [], 404);
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

        // Handle image upload or URL
        if ($request->hasFile('background_image')) {
            if ($hero->background_image && !filter_var($hero->background_image, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($hero->background_image);
            }
            $file = $request->file('background_image');
            $data['background_image'] = $file->store('rooms-hero', 'public');
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
        ], 'Rooms hero updated successfully');
    }

    // DELETE /api/dana/rooms/hero/{id} - DELETE (Admin only)
    public function destroy($id)
    {
        $hero = RoomsHero::find($id);
        
        if (!$hero) {
            return $this->sendError('Rooms hero not found', [], 404);
        }

        if ($hero->background_image && !filter_var($hero->background_image, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($hero->background_image);
        }
        
        $hero->delete();

        return $this->sendResponse([], 'Rooms hero deleted successfully');
    }
}