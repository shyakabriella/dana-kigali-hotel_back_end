<?php

namespace App\Http\Controllers\Api\Contact;

use App\Http\Controllers\Api\BaseController;
use App\Models\Contact\ContactHero;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ContactHeroController extends BaseController
{
    // GET /api/dana/contact/hero - Get all (Public)
    public function index()
    {
        $heroes = ContactHero::orderBy('id', 'desc')->get();
        
        $data = $heroes->map(function ($hero) {
            return [
                'id' => $hero->id,
                'title' => $hero->title,
                'subtitle' => $hero->subtitle,
                'destination' => $hero->destination,
                'background_image' => $hero->background_image_url,
            ];
        });

        return $this->sendResponse($data, 'Contact hero retrieved successfully');
    }

    // GET /api/dana/contact/hero/{id} - Get single (Public)
    public function show($id)
    {
        $hero = ContactHero::find($id);
        
        if (!$hero) {
            return $this->sendError('Contact hero not found', [], 404);
        }

        return $this->sendResponse([
            'id' => $hero->id,
            'title' => $hero->title,
            'subtitle' => $hero->subtitle,
            'destination' => $hero->destination,
            'background_image' => $hero->background_image_url,
        ], 'Contact hero retrieved successfully');
    }

    // POST /api/dana/contact/hero - CREATE (Admin only)
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
            $imagePath = $file->store('contact-hero', 'public');
        } elseif ($request->has('background_image') && !empty($request->background_image)) {
            $imagePath = $request->background_image;
        }

        $hero = ContactHero::create([
            'title' => $request->title ?? '— GET IN TOUCH',
            'subtitle' => $request->subtitle ?? 'Contact Us',
            'destination' => $request->destination ?? 'Home/Contact',
            'background_image' => $imagePath,
        ]);

        return $this->sendResponse([
            'id' => $hero->id,
            'title' => $hero->title,
            'subtitle' => $hero->subtitle,
            'destination' => $hero->destination,
            'background_image' => $hero->background_image_url,
        ], 'Contact hero created successfully', 201);
    }

    // PUT /api/dana/contact/hero/{id} - UPDATE (Admin only)
    public function update(Request $request, $id)
    {
        $hero = ContactHero::find($id);
        
        if (!$hero) {
            return $this->sendError('Contact hero not found', [], 404);
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
            $data['background_image'] = $file->store('contact-hero', 'public');
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
        ], 'Contact hero updated successfully');
    }

    // DELETE /api/dana/contact/hero/{id} - DELETE (Admin only)
    public function destroy($id)
    {
        $hero = ContactHero::find($id);
        
        if (!$hero) {
            return $this->sendError('Contact hero not found', [], 404);
        }

        if ($hero->background_image && !filter_var($hero->background_image, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($hero->background_image);
        }
        
        $hero->delete();

        return $this->sendResponse([], 'Contact hero deleted successfully');
    }
}