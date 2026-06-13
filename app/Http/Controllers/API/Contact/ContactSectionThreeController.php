<?php

namespace App\Http\Controllers\Api\Contact;

use App\Http\Controllers\Api\BaseController;
use App\Models\Contact\ContactSectionThree;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactSectionThreeController extends BaseController
{
    // GET /api/dana/contact/section-three - Get all (Public)
    public function index()
    {
        $sections = ContactSectionThree::orderBy('id', 'desc')->get();
        
        $data = $sections->map(function ($section) {
            return [
                'id' => $section->id,
                'title' => $section->title,
                'subtitle' => $section->subtitle,
                'description' => $section->description,
                'button_one_text' => $section->button_one_text,
                'button_two_text' => $section->button_two_text,
            ];
        });

        return $this->sendResponse($data, 'Contact section three retrieved successfully');
    }

    // GET /api/dana/contact/section-three/{id} - Get single (Public)
    public function show($id)
    {
        $section = ContactSectionThree::find($id);
        
        if (!$section) {
            return $this->sendError('Contact section not found', [], 404);
        }

        return $this->sendResponse([
            'id' => $section->id,
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'description' => $section->description,
            'button_one_text' => $section->button_one_text,
            'button_two_text' => $section->button_two_text,
        ], 'Contact section retrieved successfully');
    }

    // POST /api/dana/contact/section-three - CREATE (Admin only)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'button_one_text' => 'nullable|string|max:100',
            'button_two_text' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $section = ContactSectionThree::first();

        $data = [
            'title' => $request->title ?? ($section->title ?? '— INSTANT MESSAGING'),
            'subtitle' => $request->subtitle ?? ($section->subtitle ?? 'Prefer to chat on WhatsApp?'),
            'description' => $request->description ?? ($section->description ?? 'Send us a message anytime on WhatsApp and our team will respond as soon as possible. Perfect for quick questions and last-minute requests.'),
            'button_one_text' => $request->button_one_text ?? ($section->button_one_text ?? 'Chat on WhatsApp'),
            'button_two_text' => $request->button_two_text ?? ($section->button_two_text ?? 'View Rooms'),
        ];

        if ($section) {
            $section->update($data);
            $message = 'Contact section three updated successfully';
        } else {
            $section = ContactSectionThree::create($data);
            $message = 'Contact section three created successfully';
        }

        return $this->sendResponse([
            'id' => $section->id,
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'description' => $section->description,
            'button_one_text' => $section->button_one_text,
            'button_two_text' => $section->button_two_text,
        ], $message);
    }

    // PUT /api/dana/contact/section-three/{id} - UPDATE (Admin only)
    public function update(Request $request, $id)
    {
        $section = ContactSectionThree::find($id);
        
        if (!$section) {
            return $this->sendError('Contact section not found', [], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'button_one_text' => 'nullable|string|max:100',
            'button_two_text' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $data = [];
        
        if ($request->has('title')) $data['title'] = $request->title;
        if ($request->has('subtitle')) $data['subtitle'] = $request->subtitle;
        if ($request->has('description')) $data['description'] = $request->description;
        if ($request->has('button_one_text')) $data['button_one_text'] = $request->button_one_text;
        if ($request->has('button_two_text')) $data['button_two_text'] = $request->button_two_text;

        $section->update($data);

        return $this->sendResponse([
            'id' => $section->id,
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'description' => $section->description,
            'button_one_text' => $section->button_one_text,
            'button_two_text' => $section->button_two_text,
        ], 'Contact section three updated successfully');
    }

    // DELETE /api/dana/contact/section-three/{id} - DELETE (Admin only)
    public function destroy($id)
    {
        $section = ContactSectionThree::find($id);
        
        if (!$section) {
            return $this->sendError('Contact section not found', [], 404);
        }
        
        $section->delete();

        return $this->sendResponse([], 'Contact section three deleted successfully');
    }
}