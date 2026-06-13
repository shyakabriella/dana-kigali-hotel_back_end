<?php

namespace App\Http\Controllers\Api\Experiences;

use App\Http\Controllers\Api\BaseController;
use App\Models\Experiences\ExperiencesHero;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ExperiencesHeroController extends BaseController
{
    // GET /api/dana/experiences/hero - Get all (Public)
    public function index()
    {
        $heroes = ExperiencesHero::orderBy('id', 'desc')->get();
        
        $data = $heroes->map(function ($hero) {
            return [
                'id' => $hero->id,
                'title' => $hero->title,
                'subtitle' => $hero->subtitle,
                'destination' => $hero->destination,
                'background_image' => $hero->background_image_url,
            ];
        });

        return $this->sendResponse($data, 'Experiences hero retrieved successfully');
    }

    // GET /api/dana/experiences/hero/{id} - Get single (Public)
    public function show($id)
    {
        $hero = ExperiencesHero::find($id);
        
        if (!$hero) {
            return $this->sendError('Experiences hero not found', [], 404);
        }

        return $this->sendResponse([
            'id' => $hero->id,
            'title' => $hero->title,
            'subtitle' => $hero->subtitle,
            'destination' => $hero->destination,
            'background_image' => $hero->background_image_url,
        ], 'Experiences hero retrieved successfully');
    }

    // POST /api/dana/experiences/hero - CREATE (Admin only)
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

        if ($request->hasFile('background_image')) {
            $file = $request->file('background_image');
            $imagePath = $file->store('experiences-hero', 'public');
        } elseif ($request->has('background_image') && !empty($request->background_image)) {
            $imagePath = $request->background_image;
        }

        $hero = ExperiencesHero::create([
            'title' => $request->title ?? '— SIGNATURE EXPERIENCES',
            'subtitle' => $request->subtitle ?? 'Experiences',
            'destination' => $request->destination ?? 'Home/Experiences',
            'background_image' => $imagePath,
        ]);

        return $this->sendResponse([
            'id' => $hero->id,
            'title' => $hero->title,
            'subtitle' => $hero->subtitle,
            'destination' => $hero->destination,
            'background_image' => $hero->background_image_url,
        ], 'Experiences hero created successfully', 201);
    }

    // PUT /api/dana/experiences/hero/{id} - UPDATE (Admin only)
    public function update(Request $request, $id)
    {
        $hero = ExperiencesHero::find($id);
        
        if (!$hero) {
            return $this->sendError('Experiences hero not found', [], 404);
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

        if ($request->hasFile('background_image')) {
            if ($hero->background_image && !filter_var($hero->background_image, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($hero->background_image);
            }
            $file = $request->file('background_image');
            $data['background_image'] = $file->store('experiences-hero', 'public');
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
        ], 'Experiences hero updated successfully');
    }

    // DELETE /api/dana/experiences/hero/{id} - DELETE (Admin only)
    public function destroy($id)
    {
        $hero = ExperiencesHero::find($id);
        
        if (!$hero) {
            return $this->sendError('Experiences hero not found', [], 404);
        }

        if ($hero->background_image && !filter_var($hero->background_image, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($hero->background_image);
        }
        
        $hero->delete();

        return $this->sendResponse([], 'Experiences hero deleted successfully');
    }
}