<?php

namespace App\Http\Controllers\Api\HomePages;

use App\Http\Controllers\Api\BaseController;
use App\Models\HomePages\SectionTwo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SectionTwoController extends BaseController
{
    // GET /api/dana/section-two - Get all (Public)
    public function index()
    {
        $sections = SectionTwo::orderBy('id', 'desc')->get();
        
        $data = $sections->map(function ($section) {
            return [
                'id' => $section->id,
                'title' => $section->title,
                'subtitle' => $section->subtitle,
                'rooms' => $section->rooms, // This uses the accessor
            ];
        });

        return $this->sendResponse($data, 'Sections retrieved successfully');
    }

    // GET /api/dana/section-two/{id} - Get single (Public)
    public function show($id)
    {
        $section = SectionTwo::find($id);
        
        if (!$section) {
            return $this->sendError('Section not found', [], 404);
        }

        return $this->sendResponse([
            'id' => $section->id,
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'rooms' => $section->rooms,
        ], 'Section retrieved successfully');
    }

    // POST /api/dana/section-two - CREATE (Admin only)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'rooms' => 'required|array|min:1',
            'rooms.*.name' => 'required|string',
            'rooms.*.price' => 'required|string',
            'rooms.*.description' => 'required|string',
            'rooms.*.button_text' => 'required|string',
            'rooms.*.image' => 'nullable|string', // Can be URL or will be set by file upload
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $section = SectionTwo::create([
            'title' => $request->title ?? '— THE RIDGE COLLECTION',
            'subtitle' => $request->subtitle ?? 'Rooms & Suites',
            'rooms' => $request->rooms,
        ]);

        return $this->sendResponse([
            'id' => $section->id,
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'rooms' => $section->rooms,
        ], 'Section created successfully', 201);
    }

    // POST /api/dana/section-two/upload-room-image - Upload room image (Admin only)
    public function uploadRoomImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'room_index' => 'required|integer',
            'section_id' => 'required|integer|exists:section_twos,id',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $section = SectionTwo::find($request->section_id);
        $rooms = json_decode($section->rooms, true) ?? [];
        $roomIndex = $request->room_index;

        if (!isset($rooms[$roomIndex])) {
            return $this->sendError('Room not found', [], 404);
        }

        // Delete old image if exists and is local file
        if (isset($rooms[$roomIndex]['image_path']) && $rooms[$roomIndex]['image_path']) {
            Storage::disk('public')->delete($rooms[$roomIndex]['image_path']);
        }
        if (isset($rooms[$roomIndex]['image']) && !filter_var($rooms[$roomIndex]['image'], FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($rooms[$roomIndex]['image']);
        }

        // Store new image
        $file = $request->file('image');
        $path = $file->store('section-two/rooms', 'public');

        // Update room image
        $rooms[$roomIndex]['image'] = $path;
        $section->rooms = $rooms;
        $section->save();

        // Get updated rooms with URLs
        $updatedSection = SectionTwo::find($request->section_id);

        return $this->sendResponse([
            'room_index' => $roomIndex,
            'rooms' => $updatedSection->rooms,
        ], 'Room image uploaded successfully');
    }

    // PUT /api/dana/section-two/{id} - UPDATE (Admin only)
    public function update(Request $request, $id)
    {
        $section = SectionTwo::find($id);
        
        if (!$section) {
            return $this->sendError('Section not found', [], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'rooms' => 'nullable|array',
            'rooms.*.name' => 'required_with:rooms|string',
            'rooms.*.price' => 'required_with:rooms|string',
            'rooms.*.description' => 'required_with:rooms|string',
            'rooms.*.button_text' => 'required_with:rooms|string',
            'rooms.*.image' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $data = [];
        
        if ($request->has('title')) {
            $data['title'] = $request->title;
        }
        if ($request->has('subtitle')) {
            $data['subtitle'] = $request->subtitle;
        }
        
        if ($request->has('rooms')) {
            $existingRooms = json_decode($section->rooms, true) ?? [];
            $newRooms = $request->rooms;
            
            // Preserve existing image paths if not provided in update
            foreach ($newRooms as $key => $room) {
                if (!isset($room['image']) && isset($existingRooms[$key]['image'])) {
                    $newRooms[$key]['image'] = $existingRooms[$key]['image'];
                }
            }
            $data['rooms'] = $newRooms;
        }

        $section->update($data);

        // Get updated section with URLs
        $updatedSection = SectionTwo::find($id);

        return $this->sendResponse([
            'id' => $updatedSection->id,
            'title' => $updatedSection->title,
            'subtitle' => $updatedSection->subtitle,
            'rooms' => $updatedSection->rooms,
        ], 'Section updated successfully');
    }

    // DELETE /api/dana/section-two/{id} - DELETE (Admin only)
    public function destroy($id)
    {
        $section = SectionTwo::find($id);
        
        if (!$section) {
            return $this->sendError('Section not found', [], 404);
        }

        // Delete all room images from storage
        $rooms = json_decode($section->rooms, true) ?? [];
        foreach ($rooms as $room) {
            if (isset($room['image']) && $room['image'] && !filter_var($room['image'], FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($room['image']);
            }
        }
        
        $section->delete();

        return $this->sendResponse([], 'Section deleted successfully');
    }
}