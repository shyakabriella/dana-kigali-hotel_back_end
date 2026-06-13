<?php

namespace App\Http\Controllers\Api\Contact;

use App\Http\Controllers\Api\BaseController;
use App\Models\Contact\ContactSectionOne;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactSectionOneController extends BaseController
{
    // GET /api/dana/contact/section-one - Get all (Public)
    public function index()
    {
        $sections = ContactSectionOne::orderBy('id', 'desc')->get();
        
        $data = $sections->map(function ($section) {
            return [
                'id' => $section->id,
                'title' => $section->title,
                'subtitle' => $section->subtitle,
                'description' => $section->description,
                'cards' => $section->cards,
            ];
        });

        return $this->sendResponse($data, 'Contact section one retrieved successfully');
    }

    // GET /api/dana/contact/section-one/{id} - Get single (Public)
    public function show($id)
    {
        $section = ContactSectionOne::find($id);
        
        if (!$section) {
            return $this->sendError('Contact section not found', [], 404);
        }

        return $this->sendResponse([
            'id' => $section->id,
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'description' => $section->description,
            'cards' => $section->cards,
        ], 'Contact section retrieved successfully');
    }

    // POST /api/dana/contact/section-one - CREATE (Admin only)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'cards' => 'required|array|min:1',
            'cards.*.title' => 'required|string',
            'cards.*.content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $section = ContactSectionOne::first();

        $data = [
            'title' => $request->title ?? ($section->title ?? '— REACH OUT'),
            'subtitle' => $request->subtitle ?? ($section->subtitle ?? 'We are here for you.'),
            'description' => $request->description ?? ($section->description ?? 'Whether you have a question, a special request, or simply want to say hello — our team is ready to welcome you with the warmth of the DANA family.'),
            'cards' => $request->cards,
        ];

        if ($section) {
            $section->update($data);
            $message = 'Contact section one updated successfully';
        } else {
            $section = ContactSectionOne::create($data);
            $message = 'Contact section one created successfully';
        }

        return $this->sendResponse([
            'id' => $section->id,
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'description' => $section->description,
            'cards' => $section->cards,
        ], $message);
    }

    // PUT /api/dana/contact/section-one/{id} - UPDATE (Admin only)
    public function update(Request $request, $id)
    {
        $section = ContactSectionOne::find($id);
        
        if (!$section) {
            return $this->sendError('Contact section not found', [], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'cards' => 'nullable|array',
            'cards.*.title' => 'required_with:cards|string',
            'cards.*.content' => 'required_with:cards|string',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $data = [];
        
        if ($request->has('title')) $data['title'] = $request->title;
        if ($request->has('subtitle')) $data['subtitle'] = $request->subtitle;
        if ($request->has('description')) $data['description'] = $request->description;
        if ($request->has('cards')) $data['cards'] = $request->cards;

        $section->update($data);

        return $this->sendResponse([
            'id' => $section->id,
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'description' => $section->description,
            'cards' => $section->cards,
        ], 'Contact section one updated successfully');
    }

    // DELETE /api/dana/contact/section-one/{id} - DELETE (Admin only)
    public function destroy($id)
    {
        $section = ContactSectionOne::find($id);
        
        if (!$section) {
            return $this->sendError('Contact section not found', [], 404);
        }
        
        $section->delete();

        return $this->sendResponse([], 'Contact section one deleted successfully');
    }
}