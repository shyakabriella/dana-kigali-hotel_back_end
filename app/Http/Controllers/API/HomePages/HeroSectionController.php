<?php

namespace App\Http\Controllers\Api\HomePages;

use App\Http\Controllers\Api\BaseController;
use App\Models\HomePages\HeroSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class HeroSectionController extends BaseController
{
    /**
     * GET /api/dana/hero - Get all hero sections (Public)
     */
    public function index()
    {
        $heroes = HeroSection::orderBy('id', 'desc')->get();
        
        $data = $heroes->map(function ($hero) {
            return [
                'id' => $hero->id,
                'title' => $hero->title,
                'subtitle' => $hero->subtitle,
                'description' => $hero->description,
                'button_text' => $hero->button_text,
                'secondary_text' => $hero->secondary_text,
                'background_image' => $hero->background_image_url,
            ];
        });

        return $this->sendResponse($data, 'Hero sections retrieved successfully');
    }

    /**
     * GET /api/dana/hero/{id} - Get single hero section (Admin)
     */
    public function show($id)
    {
        $hero = HeroSection::find($id);
        
        if (!$hero) {
            return $this->sendError('Hero section not found', [], 404);
        }

        return $this->sendResponse([
            'id' => $hero->id,
            'title' => $hero->title,
            'subtitle' => $hero->subtitle,
            'description' => $hero->description,
            'button_text' => $hero->button_text,
            'secondary_text' => $hero->secondary_text,
            'background_image' => $hero->background_image_url,
        ], 'Hero section retrieved successfully');
    }

    /**
     * POST /api/dana/hero - Create new hero section (Admin)
     */
    public function store(Request $request)
    {
        $rules = [
            'title' => 'required|string|max:255',
            'subtitle' => 'required|string|max:255',
            'description' => 'required|string',
            'button_text' => 'required|string|max:100',
            'secondary_text' => 'required|string|max:100',
        ];

        if ($request->hasFile('background_image')) {
            $rules['background_image'] = 'required|image|mimes:jpeg,png,jpg,webp|max:5120';
        } elseif ($request->has('background_image')) {
            $rules['background_image'] = 'nullable|string|max:1000';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $imagePath = null;

        if ($request->hasFile('background_image')) {
            $file = $request->file('background_image');
            $imagePath = $file->store('hero', 'public');
        } elseif ($request->has('background_image') && !empty($request->background_image)) {
            $imagePath = $request->background_image;
        }

        $hero = HeroSection::create([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'description' => $request->description,
            'button_text' => $request->button_text,
            'secondary_text' => $request->secondary_text,
            'background_image' => $imagePath,
        ]);

        return $this->sendResponse([
            'id' => $hero->id,
            'title' => $hero->title,
            'subtitle' => $hero->subtitle,
            'description' => $hero->description,
            'button_text' => $hero->button_text,
            'secondary_text' => $hero->secondary_text,
            'background_image' => $hero->background_image_url,
        ], 'Hero section created successfully', 201);
    }

    /**
     * PUT /api/dana/hero/{id} - Update hero section (Admin)
     */
    public function update(Request $request, $id)
    {
        $hero = HeroSection::find($id);
        
        if (!$hero) {
            return $this->sendError('Hero section not found', [], 404);
        }

        $rules = [
            'title' => 'sometimes|required|string|max:255',
            'subtitle' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'button_text' => 'sometimes|required|string|max:100',
            'secondary_text' => 'sometimes|required|string|max:100',
        ];

        if ($request->hasFile('background_image')) {
            $rules['background_image'] = 'sometimes|required|image|mimes:jpeg,png,jpg,webp|max:5120';
        } elseif ($request->has('background_image')) {
            $rules['background_image'] = 'nullable|string|max:1000';
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

        if ($request->hasFile('background_image')) {
            if ($hero->background_image && !filter_var($hero->background_image, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($hero->background_image);
            }
            $file = $request->file('background_image');
            $data['background_image'] = $file->store('hero', 'public');
        } elseif ($request->has('background_image')) {
            $data['background_image'] = $request->background_image;
        }

        $hero->update($data);

        return $this->sendResponse([
            'id' => $hero->id,
            'title' => $hero->title,
            'subtitle' => $hero->subtitle,
            'description' => $hero->description,
            'button_text' => $hero->button_text,
            'secondary_text' => $hero->secondary_text,
            'background_image' => $hero->background_image_url,
        ], 'Hero section updated successfully');
    }

    /**
     * DELETE /api/dana/hero/{id} - Delete hero section (Admin)
     */
    public function destroy($id)
    {
        $hero = HeroSection::find($id);
        
        if (!$hero) {
            return $this->sendError('Hero section not found', [], 404);
        }

        if ($hero->background_image && !filter_var($hero->background_image, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($hero->background_image);
        }
        
        $hero->delete();

        return $this->sendResponse([], 'Hero section deleted successfully');
    }
}