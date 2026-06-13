<?php

namespace App\Http\Controllers\Api\Rooms;

use App\Http\Controllers\Api\BaseController;
use App\Models\Rooms\RoomsSectionOne;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class RoomsSectionOneController extends BaseController
{
    // GET /api/dana/rooms/section-one - Get all (Public)
    public function index()
    {
        $sections = RoomsSectionOne::orderBy('id', 'desc')->get();
        
        $data = $sections->map(function ($section) {
            return [
                'id' => $section->id,
                'title' => $section->title,
                'subtitle' => $section->subtitle,
                'description' => $section->description,
                'rooms' => $section->rooms,
            ];
        });

        return $this->sendResponse($data, 'Rooms section one retrieved successfully');
    }

    // GET /api/dana/rooms/section-one/{id} - Get single (Public)
    public function show($id)
    {
        $section = RoomsSectionOne::find($id);
        
        if (!$section) {
            return $this->sendError('Rooms section not found', [], 404);
        }

        return $this->sendResponse([
            'id' => $section->id,
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'description' => $section->description,
            'rooms' => $section->rooms,
        ], 'Rooms section retrieved successfully');
    }

    // POST /api/dana/rooms/section-one - CREATE (Admin only)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'rooms' => 'required|array|min:1',
            'rooms.*.name' => 'required|string',
            'rooms.*.category' => 'nullable|string',
            'rooms.*.badge' => 'nullable|string',
            'rooms.*.price' => 'required|string',
            'rooms.*.size' => 'required|string',
            'rooms.*.beds' => 'required|string',
            'rooms.*.guests' => 'required|string',
            'rooms.*.baths' => 'required|string',
            'rooms.*.button_text' => 'nullable|string',
            'rooms.*.main_image' => 'nullable|string',
            'rooms.*.gallery' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $section = RoomsSectionOne::create([
            'title' => $request->title ?? '— SIX WAYS TO STAY',
            'subtitle' => $request->subtitle ?? 'Choose your ridge.',
            'description' => $request->description ?? 'Each room at DANA KIGALI HOTEL is shaped around its view — from compact alpine retreats to suites with private terraces and stone fireplaces. All include daily housekeeping, hand-finished linens, and unhurried mornings.',
            'rooms' => $request->rooms,
        ]);

        return $this->sendResponse([
            'id' => $section->id,
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'description' => $section->description,
            'rooms' => $section->rooms,
        ], 'Rooms section created successfully', 201);
    }

    // PUT /api/dana/rooms/section-one/{id} - UPDATE (Admin only)
    public function update(Request $request, $id)
    {
        $section = RoomsSectionOne::find($id);
        
        if (!$section) {
            return $this->sendError('Rooms section not found', [], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'rooms' => 'nullable|array',
            'rooms.*.name' => 'required_with:rooms|string',
            'rooms.*.price' => 'required_with:rooms|string',
            'rooms.*.size' => 'required_with:rooms|string',
            'rooms.*.beds' => 'required_with:rooms|string',
            'rooms.*.guests' => 'required_with:rooms|string',
            'rooms.*.baths' => 'required_with:rooms|string',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $data = [];
        
        if ($request->has('title')) $data['title'] = $request->title;
        if ($request->has('subtitle')) $data['subtitle'] = $request->subtitle;
        if ($request->has('description')) $data['description'] = $request->description;
        if ($request->has('rooms')) $data['rooms'] = $request->rooms;

        $section->update($data);

        return $this->sendResponse([
            'id' => $section->id,
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'description' => $section->description,
            'rooms' => $section->rooms,
        ], 'Rooms section updated successfully');
    }

    // DELETE /api/dana/rooms/section-one/{id} - DELETE (Admin only)
    public function destroy($id)
    {
        $section = RoomsSectionOne::find($id);
        
        if (!$section) {
            return $this->sendError('Rooms section not found', [], 404);
        }
        
        // Delete all images from storage
        $rooms = $section->rooms;
        if (is_array($rooms)) {
            foreach ($rooms as $room) {
                if (isset($room['main_image']) && $room['main_image'] && !filter_var($room['main_image'], FILTER_VALIDATE_URL)) {
                    Storage::disk('public')->delete($room['main_image']);
                }
                if (isset($room['gallery']) && is_array($room['gallery'])) {
                    foreach ($room['gallery'] as $image) {
                        if ($image && !filter_var($image, FILTER_VALIDATE_URL)) {
                            Storage::disk('public')->delete($image);
                        }
                    }
                }
            }
        }
        
        $section->delete();

        return $this->sendResponse([], 'Rooms section deleted successfully');
    }
}