<?php

namespace App\Http\Controllers\Api\About;

use App\Http\Controllers\Api\BaseController;
use App\Models\About\AboutSectionOne;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AboutSectionOneController extends BaseController
{
    // GET /api/dana/about/section-one - Get all (Public)
    public function index()
    {
        $sections = AboutSectionOne::orderBy('id', 'desc')->get();
        
        $data = $sections->map(function ($section) {
            return [
                'id' => $section->id,
                'title' => $section->title,
                'subtitle' => $section->subtitle,
                'description' => $section->description,
                'right_image' => $section->right_image_url,
                'card_title' => $section->card_title,
                'stats' => $section->stats,
            ];
        });

        return $this->sendResponse($data, 'About section one retrieved successfully');
    }

    // GET /api/dana/about/section-one/{id} - Get single (Public)
    public function show($id)
    {
        $section = AboutSectionOne::find($id);
        
        if (!$section) {
            return $this->sendError('About section not found', [], 404);
        }

        return $this->sendResponse([
            'id' => $section->id,
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'description' => $section->description,
            'right_image' => $section->right_image_url,
            'card_title' => $section->card_title,
            'stats' => $section->stats,
        ], 'About section retrieved successfully');
    }

    // POST /api/dana/about/section-one - CREATE (Admin only)
    public function store(Request $request)
    {
        $rules = [
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'required|string',
            'card_title' => 'nullable|string|max:255',
            'stats' => 'nullable|array',
            'stats.*.label' => 'required_with:stats|string',
            'stats.*.value' => 'required_with:stats|string',
        ];

        if ($request->hasFile('right_image')) {
            $rules['right_image'] = 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120';
        } else {
            $rules['right_image'] = 'nullable|string|max:1000';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $section = AboutSectionOne::first();
        $imagePath = null;

        if ($request->hasFile('right_image')) {
            if ($section && $section->right_image && !filter_var($section->right_image, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($section->right_image);
            }
            $file = $request->file('right_image');
            $imagePath = $file->store('about-section-one', 'public');
        } elseif ($request->has('right_image') && !empty($request->right_image)) {
            $imagePath = $request->right_image;
        } elseif ($section && $section->right_image) {
            $imagePath = $section->right_image;
        }

        $data = [
            'title' => $request->title ?? ($section->title ?? '— WELCOME'),
            'subtitle' => $request->subtitle ?? ($section->subtitle ?? 'Your Home Away from Home.'),
            'description' => $request->description,
            'right_image' => $imagePath,
            'card_title' => $request->card_title ?? ($section->card_title ?? 'Please feel at home.'),
            'stats' => $request->stats ?? ($section->stats ?? [
                ['label' => 'Years of heritage', 'value' => '150+'],
                ['label' => 'Hills of Rwanda', 'value' => '1,000'],
                ['label' => 'You are part of', 'value' => 'Family']
            ]),
        ];

        if ($section) {
            $section->update($data);
            $message = 'About section one updated successfully';
        } else {
            $section = AboutSectionOne::create($data);
            $message = 'About section one created successfully';
        }

        return $this->sendResponse([
            'id' => $section->id,
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'description' => $section->description,
            'right_image' => $section->right_image_url,
            'card_title' => $section->card_title,
            'stats' => $section->stats,
        ], $message);
    }

    // PUT /api/dana/about/section-one/{id} - UPDATE (Admin only)
    public function update(Request $request, $id)
    {
        $section = AboutSectionOne::find($id);
        
        if (!$section) {
            return $this->sendError('About section not found', [], 404);
        }

        $rules = [
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'card_title' => 'nullable|string|max:255',
            'stats' => 'nullable|array',
            'stats.*.label' => 'required_with:stats|string',
            'stats.*.value' => 'required_with:stats|string',
        ];

        if ($request->hasFile('right_image')) {
            $rules['right_image'] = 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120';
        } else {
            $rules['right_image'] = 'nullable|string|max:1000';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $data = [];
        
        if ($request->has('title')) $data['title'] = $request->title;
        if ($request->has('subtitle')) $data['subtitle'] = $request->subtitle;
        if ($request->has('description')) $data['description'] = $request->description;
        if ($request->has('card_title')) $data['card_title'] = $request->card_title;
        if ($request->has('stats')) $data['stats'] = $request->stats;

        if ($request->hasFile('right_image')) {
            if ($section->right_image && !filter_var($section->right_image, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($section->right_image);
            }
            $file = $request->file('right_image');
            $data['right_image'] = $file->store('about-section-one', 'public');
        } elseif ($request->has('right_image')) {
            $data['right_image'] = $request->right_image;
        }

        $section->update($data);

        return $this->sendResponse([
            'id' => $section->id,
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'description' => $section->description,
            'right_image' => $section->right_image_url,
            'card_title' => $section->card_title,
            'stats' => $section->stats,
        ], 'About section one updated successfully');
    }

    // DELETE /api/dana/about/section-one/{id} - DELETE (Admin only)
    public function destroy($id)
    {
        $section = AboutSectionOne::find($id);
        
        if (!$section) {
            return $this->sendError('About section not found', [], 404);
        }

        if ($section->right_image && !filter_var($section->right_image, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($section->right_image);
        }
        
        $section->delete();

        return $this->sendResponse([], 'About section one deleted successfully');
    }
}