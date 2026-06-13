<?php

namespace App\Http\Controllers\Api\Contact;

use App\Http\Controllers\Api\BaseController;
use App\Models\Contact\ContactSectionTwo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ContactSectionTwoController extends BaseController
{
    // GET /api/dana/contact/section-two - Get all (Public)
    public function index()
    {
        $sections = ContactSectionTwo::orderBy('id', 'desc')->get();
        
        $data = $sections->map(function ($section) {
            return [
                'id' => $section->id,
                'title' => $section->title,
                'subtitle' => $section->subtitle,
                'description' => $section->description,
                'right_image' => $section->right_image_url,
                'image_caption' => $section->image_caption,
                'image_address' => $section->image_address,
                'opening_hours_title' => $section->opening_hours_title,
                'opening_hours_subtitle' => $section->opening_hours_subtitle,
                'opening_hours' => $section->opening_hours,
            ];
        });

        return $this->sendResponse($data, 'Contact section two retrieved successfully');
    }

    // GET /api/dana/contact/section-two/{id} - Get single (Public)
    public function show($id)
    {
        $section = ContactSectionTwo::find($id);
        
        if (!$section) {
            return $this->sendError('Contact section not found', [], 404);
        }

        return $this->sendResponse([
            'id' => $section->id,
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'description' => $section->description,
            'right_image' => $section->right_image_url,
            'image_caption' => $section->image_caption,
            'image_address' => $section->image_address,
            'opening_hours_title' => $section->opening_hours_title,
            'opening_hours_subtitle' => $section->opening_hours_subtitle,
            'opening_hours' => $section->opening_hours,
        ], 'Contact section retrieved successfully');
    }

    // POST /api/dana/contact/section-two - CREATE (Admin only)
    public function store(Request $request)
    {
        $rules = [
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image_caption' => 'nullable|string|max:255',
            'image_address' => 'nullable|string|max:255',
            'opening_hours_title' => 'nullable|string|max:255',
            'opening_hours_subtitle' => 'nullable|string|max:255',
            'opening_hours' => 'nullable|array',
            'opening_hours.*.service' => 'required_with:opening_hours|string',
            'opening_hours.*.hours' => 'required_with:opening_hours|string',
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

        $section = ContactSectionTwo::first();
        $imagePath = null;

        if ($request->hasFile('right_image')) {
            if ($section && $section->right_image && !filter_var($section->right_image, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($section->right_image);
            }
            $file = $request->file('right_image');
            $imagePath = $file->store('contact-section-two', 'public');
        } elseif ($request->has('right_image') && !empty($request->right_image)) {
            $imagePath = $request->right_image;
        } elseif ($section && $section->right_image) {
            $imagePath = $section->right_image;
        }

        $data = [
            'title' => $request->title ?? ($section->title ?? '— SEND A MESSAGE'),
            'subtitle' => $request->subtitle ?? ($section->subtitle ?? 'Write to us.'),
            'description' => $request->description ?? ($section->description ?? 'Fill in the form below and we will get back to you within 24 hours. For urgent matters, please call us directly.'),
            'right_image' => $imagePath,
            'image_caption' => $request->image_caption ?? ($section->image_caption ?? 'DANA KIGALI HOTEL terrace view'),
            'image_address' => $request->image_address ?? ($section->image_address ?? 'KG 7 Ave, Kigali, Rwanda'),
            'opening_hours_title' => $request->opening_hours_title ?? ($section->opening_hours_title ?? '— OPENING HOURS'),
            'opening_hours_subtitle' => $request->opening_hours_subtitle ?? ($section->opening_hours_subtitle ?? 'At your service.'),
            'opening_hours' => $request->opening_hours ?? ($section->opening_hours ?? [
                ['service' => 'Reception', 'hours' => '24 / 7'],
                ['service' => 'Restaurant', 'hours' => '6:00 AM – 11:00 PM'],
                ['service' => 'Room Service', 'hours' => '24 / 7'],
                ['service' => 'Spa & Wellness', 'hours' => '9:00 AM – 9:00 PM'],
                ['service' => 'Concierge', 'hours' => '7:00 AM – 10:00 PM'],
                ['service' => 'Airport Transfer', 'hours' => 'On request, 24h advance'],
            ]),
        ];

        if ($section) {
            $section->update($data);
            $message = 'Contact section two updated successfully';
        } else {
            $section = ContactSectionTwo::create($data);
            $message = 'Contact section two created successfully';
        }

        return $this->sendResponse([
            'id' => $section->id,
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'description' => $section->description,
            'right_image' => $section->right_image_url,
            'image_caption' => $section->image_caption,
            'image_address' => $section->image_address,
            'opening_hours_title' => $section->opening_hours_title,
            'opening_hours_subtitle' => $section->opening_hours_subtitle,
            'opening_hours' => $section->opening_hours,
        ], $message);
    }

    // PUT /api/dana/contact/section-two/{id} - UPDATE (Admin only)
    public function update(Request $request, $id)
    {
        $section = ContactSectionTwo::find($id);
        
        if (!$section) {
            return $this->sendError('Contact section not found', [], 404);
        }

        $rules = [
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image_caption' => 'nullable|string|max:255',
            'image_address' => 'nullable|string|max:255',
            'opening_hours_title' => 'nullable|string|max:255',
            'opening_hours_subtitle' => 'nullable|string|max:255',
            'opening_hours' => 'nullable|array',
            'opening_hours.*.service' => 'required_with:opening_hours|string',
            'opening_hours.*.hours' => 'required_with:opening_hours|string',
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
        if ($request->has('image_caption')) $data['image_caption'] = $request->image_caption;
        if ($request->has('image_address')) $data['image_address'] = $request->image_address;
        if ($request->has('opening_hours_title')) $data['opening_hours_title'] = $request->opening_hours_title;
        if ($request->has('opening_hours_subtitle')) $data['opening_hours_subtitle'] = $request->opening_hours_subtitle;
        if ($request->has('opening_hours')) $data['opening_hours'] = $request->opening_hours;

        if ($request->hasFile('right_image')) {
            if ($section->right_image && !filter_var($section->right_image, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($section->right_image);
            }
            $file = $request->file('right_image');
            $data['right_image'] = $file->store('contact-section-two', 'public');
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
            'image_caption' => $section->image_caption,
            'image_address' => $section->image_address,
            'opening_hours_title' => $section->opening_hours_title,
            'opening_hours_subtitle' => $section->opening_hours_subtitle,
            'opening_hours' => $section->opening_hours,
        ], 'Contact section two updated successfully');
    }

    // DELETE /api/dana/contact/section-two/{id} - DELETE (Admin only)
    public function destroy($id)
    {
        $section = ContactSectionTwo::find($id);
        
        if (!$section) {
            return $this->sendError('Contact section not found', [], 404);
        }

        if ($section->right_image && !filter_var($section->right_image, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($section->right_image);
        }
        
        $section->delete();

        return $this->sendResponse([], 'Contact section two deleted successfully');
    }
}