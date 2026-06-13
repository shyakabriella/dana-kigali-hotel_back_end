<?php

namespace App\Http\Controllers\Api\HomePages;

use App\Http\Controllers\Api\BaseController;
use App\Models\HomePages\SectionOne;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SectionOneController extends BaseController
{
    // GET /api/dana/section-one - Get all (Public)
    public function index()
    {
        $sections = SectionOne::orderBy('id', 'desc')->get();
        
        $data = $sections->map(function ($section) {
            return [
                'id' => $section->id,
                'title' => $section->title,
                'subtitle' => $section->subtitle,
                'description' => $section->description,
                'left_image' => $section->left_image_url,
                'card1_title' => $section->card1_title,
                'card1_description' => $section->card1_description,
                'card2_title' => $section->card2_title,
                'card2_description' => $section->card2_description,
                'bottom_card_text' => $section->bottom_card_text,
            ];
        });

        return $this->sendResponse($data, 'Sections retrieved successfully');
    }

    // GET /api/dana/section-one/{id} - Get single (Public)
    public function show($id)
    {
        $section = SectionOne::find($id);
        
        if (!$section) {
            return $this->sendError('Section not found', [], 404);
        }

        return $this->sendResponse([
            'id' => $section->id,
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'description' => $section->description,
            'left_image' => $section->left_image_url,
            'card1_title' => $section->card1_title,
            'card1_description' => $section->card1_description,
            'card2_title' => $section->card2_title,
            'card2_description' => $section->card2_description,
            'bottom_card_text' => $section->bottom_card_text,
        ], 'Section retrieved successfully');
    }

    // POST /api/dana/section-one - CREATE (Admin only)
    public function store(Request $request)
    {
        $rules = [
            'title' => 'required|string|max:255',
            'subtitle' => 'required|string|max:255',
            'description' => 'required|string',
            'card1_title' => 'required|string|max:255',
            'card1_description' => 'required|string',
            'card2_title' => 'required|string|max:255',
            'card2_description' => 'required|string',
            'bottom_card_text' => 'required|string',
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
            $imagePath = $file->store('section-one', 'public');
        } elseif ($request->has('left_image') && !empty($request->left_image)) {
            $imagePath = $request->left_image;
        }

        $section = SectionOne::create([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'description' => $request->description,
            'left_image' => $imagePath,
            'card1_title' => $request->card1_title,
            'card1_description' => $request->card1_description,
            'card2_title' => $request->card2_title,
            'card2_description' => $request->card2_description,
            'bottom_card_text' => $request->bottom_card_text,
        ]);

        return $this->sendResponse([
            'id' => $section->id,
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'description' => $section->description,
            'left_image' => $section->left_image_url,
            'card1_title' => $section->card1_title,
            'card1_description' => $section->card1_description,
            'card2_title' => $section->card2_title,
            'card2_description' => $section->card2_description,
            'bottom_card_text' => $section->bottom_card_text,
        ], 'Section created successfully', 201);
    }

    // PUT /api/dana/section-one/{id} - UPDATE (Admin only)
    public function update(Request $request, $id)
    {
        $section = SectionOne::find($id);
        
        if (!$section) {
            return $this->sendError('Section not found', [], 404);
        }

        $rules = [
            'title' => 'sometimes|required|string|max:255',
            'subtitle' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'card1_title' => 'sometimes|required|string|max:255',
            'card1_description' => 'sometimes|required|string',
            'card2_title' => 'sometimes|required|string|max:255',
            'card2_description' => 'sometimes|required|string',
            'bottom_card_text' => 'sometimes|required|string',
        ];

        if ($request->hasFile('left_image')) {
            $rules['left_image'] = 'sometimes|required|image|mimes:jpeg,png,jpg,webp|max:5120';
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
        if ($request->has('card1_title')) $data['card1_title'] = $request->card1_title;
        if ($request->has('card1_description')) $data['card1_description'] = $request->card1_description;
        if ($request->has('card2_title')) $data['card2_title'] = $request->card2_title;
        if ($request->has('card2_description')) $data['card2_description'] = $request->card2_description;
        if ($request->has('bottom_card_text')) $data['bottom_card_text'] = $request->bottom_card_text;

        // Handle image upload
        if ($request->hasFile('left_image')) {
            // Delete old image if exists and is local file
            if ($section->left_image && !filter_var($section->left_image, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($section->left_image);
            }
            $file = $request->file('left_image');
            $data['left_image'] = $file->store('section-one', 'public');
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
            'card1_title' => $section->card1_title,
            'card1_description' => $section->card1_description,
            'card2_title' => $section->card2_title,
            'card2_description' => $section->card2_description,
            'bottom_card_text' => $section->bottom_card_text,
        ], 'Section updated successfully');
    }

    // DELETE /api/dana/section-one/{id} - DELETE (Admin only)
    public function destroy($id)
    {
        $section = SectionOne::find($id);
        
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
}