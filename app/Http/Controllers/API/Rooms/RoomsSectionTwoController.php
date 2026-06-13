<?php

namespace App\Http\Controllers\Api\Rooms;

use App\Http\Controllers\Api\BaseController;
use App\Models\Rooms\RoomsSectionTwo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoomsSectionTwoController extends BaseController
{
    // GET /api/dana/rooms/section-two - Get all (Public)
    public function index()
    {
        $sections = RoomsSectionTwo::orderBy('id', 'desc')->get();
        
        $data = $sections->map(function ($section) {
            return [
                'id' => $section->id,
                'title' => $section->title,
                'subtitle' => $section->subtitle,
                'button_text' => $section->button_text,
            ];
        });

        return $this->sendResponse($data, 'Rooms section two retrieved successfully');
    }

    // GET /api/dana/rooms/section-two/{id} - Get single (Public)
    public function show($id)
    {
        $section = RoomsSectionTwo::find($id);
        
        if (!$section) {
            return $this->sendError('Rooms section not found', [], 404);
        }

        return $this->sendResponse([
            'id' => $section->id,
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'button_text' => $section->button_text,
        ], 'Rooms section retrieved successfully');
    }

    // POST /api/dana/rooms/section-two - CREATE (Admin only)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'button_text' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $section = RoomsSectionTwo::first();

        $data = [
            'title' => $request->title ?? ($section->title ?? '— NEED HELP CHOOSING?'),
            'subtitle' => $request->subtitle ?? ($section->subtitle ?? 'Our concierge is one call away.'),
            'button_text' => $request->button_text ?? ($section->button_text ?? 'Speak To Concierge'),
        ];

        if ($section) {
            $section->update($data);
            $message = 'Rooms section two updated successfully';
        } else {
            $section = RoomsSectionTwo::create($data);
            $message = 'Rooms section two created successfully';
        }

        return $this->sendResponse([
            'id' => $section->id,
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'button_text' => $section->button_text,
        ], $message);
    }

    // PUT /api/dana/rooms/section-two/{id} - UPDATE (Admin only)
    public function update(Request $request, $id)
    {
        $section = RoomsSectionTwo::find($id);
        
        if (!$section) {
            return $this->sendError('Rooms section not found', [], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'button_text' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $data = [];
        
        if ($request->has('title')) $data['title'] = $request->title;
        if ($request->has('subtitle')) $data['subtitle'] = $request->subtitle;
        if ($request->has('button_text')) $data['button_text'] = $request->button_text;

        $section->update($data);

        return $this->sendResponse([
            'id' => $section->id,
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'button_text' => $section->button_text,
        ], 'Rooms section two updated successfully');
    }

    // DELETE /api/dana/rooms/section-two/{id} - DELETE (Admin only)
    public function destroy($id)
    {
        $section = RoomsSectionTwo::find($id);
        
        if (!$section) {
            return $this->sendError('Rooms section not found', [], 404);
        }
        
        $section->delete();

        return $this->sendResponse([], 'Rooms section two deleted successfully');
    }
}