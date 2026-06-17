<?php

namespace App\Http\Controllers\Api\Rooms;

use App\Http\Controllers\Api\BaseController;
use App\Models\Rooms\RoomsSectionOne;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class RoomsSectionOneController extends BaseController
{
    // ─── Helpers ────────────────────────────────────────────────────────────

    private function processRooms(array $rooms): array
    {
        return array_map(function ($room) {
            $imagePath = $room['image'] ?? null;
            $room['image_url'] = ($imagePath && !filter_var($imagePath, FILTER_VALIDATE_URL))
                ? Storage::url($imagePath)
                : $imagePath;
            return $room;
        }, $rooms);
    }

    // ─── GET /api/dana/rooms/section-one ────────────────────────────────────

    public function index()
    {
        $sections = RoomsSectionOne::orderBy('id', 'desc')->get();

        $data = $sections->map(fn($s) => [
            'id'          => $s->id,
            'title'       => $s->title,
            'subtitle'    => $s->subtitle,
            'description' => $s->description,
            'rooms'       => $this->processRooms($s->rooms ?? []),
        ]);

        return $this->sendResponse($data, 'Rooms section one retrieved successfully');
    }

    // ─── GET /api/dana/rooms/section-one/{id} ───────────────────────────────

    public function show($id)
    {
        $section = RoomsSectionOne::find($id);
        if (!$section) return $this->sendError('Not found', [], 404);

        return $this->sendResponse([
            'id'          => $section->id,
            'title'       => $section->title,
            'subtitle'    => $section->subtitle,
            'description' => $section->description,
            'rooms'       => $this->processRooms($section->rooms ?? []),
        ], 'Rooms section retrieved successfully');
    }

    // ─── POST /api/dana/rooms/section-one ───────────────────────────────────

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'                  => 'nullable|string|max:255',
            'subtitle'               => 'nullable|string|max:255',
            'description'            => 'nullable|string',
            'rooms'                  => 'required|array|min:1',
            'rooms.*.name'           => 'required|string',
            'rooms.*.description'    => 'required|string',
            'rooms.*.button_text'    => 'nullable|string',
        ]);

        if ($validator->fails()) return $this->sendValidationError($validator->errors());

        $section = RoomsSectionOne::create([
            'title'       => $request->title ?? '— SIX WAYS TO STAY',
            'subtitle'    => $request->subtitle ?? 'Choose your ridge.',
            'description' => $request->description ?? '',
            'rooms'       => $request->rooms,
        ]);

        return $this->sendResponse([
            'id'          => $section->id,
            'title'       => $section->title,
            'subtitle'    => $section->subtitle,
            'description' => $section->description,
            'rooms'       => $this->processRooms($section->rooms ?? []),
        ], 'Created successfully', 201);
    }

    // ─── PUT /api/dana/rooms/section-one/{id} ───────────────────────────────

    public function update(Request $request, $id)
    {
        $section = RoomsSectionOne::find($id);
        if (!$section) return $this->sendError('Not found', [], 404);

        $validator = Validator::make($request->all(), [
            'title'                  => 'nullable|string|max:255',
            'subtitle'               => 'nullable|string|max:255',
            'description'            => 'nullable|string',
            'rooms'                  => 'nullable|array',
            'rooms.*.name'           => 'required_with:rooms|string',
            'rooms.*.description'    => 'required_with:rooms|string',
            'rooms.*.button_text'    => 'nullable|string',
            'rooms.*.image'          => 'nullable|string',
        ]);

        if ($validator->fails()) return $this->sendValidationError($validator->errors());

        $data = [];
        if ($request->has('title'))       $data['title']       = $request->title;
        if ($request->has('subtitle'))    $data['subtitle']    = $request->subtitle;
        if ($request->has('description')) $data['description'] = $request->description;

        if ($request->has('rooms')) {
            $existingRooms  = $section->rooms ?? [];
            $incomingRooms  = $request->rooms;

            // ✅ Merge: keep existing image if incoming doesn't send a new path
            $data['rooms'] = array_map(function ($incoming, $index) use ($existingRooms) {
                $existing = $existingRooms[$index] ?? [];
                return [
                    'name'        => $incoming['name']        ?? ($existing['name'] ?? ''),
                    'description' => $incoming['description'] ?? ($existing['description'] ?? ''),
                    'button_text' => $incoming['button_text'] ?? ($existing['button_text'] ?? 'Book Now'),
                    'image'       => $incoming['image']       ?? ($existing['image'] ?? null), // ✅ never wipe
                ];
            }, $incomingRooms, array_keys($incomingRooms));
        }

        $section->update($data);

        return $this->sendResponse([
            'id'          => $section->id,
            'title'       => $section->title,
            'subtitle'    => $section->subtitle,
            'description' => $section->description,
            'rooms'       => $this->processRooms($section->rooms ?? []),
        ], 'Updated successfully');
    }

    // ─── POST /api/dana/rooms/section-one/upload-room-image ─────────────────

    public function uploadRoomImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image'      => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'room_index' => 'required|integer|min:0',
            'section_id' => 'required|integer|exists:rooms_section_one,id',
        ]);

        if ($validator->fails()) return $this->sendValidationError($validator->errors());

        $section   = RoomsSectionOne::find($request->section_id);
        $rooms     = $section->rooms ?? [];
        $roomIndex = (int) $request->room_index;

        if (!isset($rooms[$roomIndex])) return $this->sendError('Room index not found', [], 404);

        // Delete old image if it's a local file
        $oldImage = $rooms[$roomIndex]['image'] ?? null;
        if ($oldImage && !filter_var($oldImage, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($oldImage);
        }

        // Store new image
        $path = $request->file('image')->store('rooms-section-one', 'public');

        // ✅ Update only the target room's image
        $rooms[$roomIndex]['image'] = $path;
        $section->rooms = $rooms;
        $section->save();

        $fresh = RoomsSectionOne::find($request->section_id);

        return $this->sendResponse([
            'room_index' => $roomIndex,
            'rooms'      => $this->processRooms($fresh->rooms ?? []),
        ], 'Image uploaded successfully');
    }

    // ─── DELETE /api/dana/rooms/section-one/{id} ────────────────────────────

    public function destroy($id)
    {
        $section = RoomsSectionOne::find($id);
        if (!$section) return $this->sendError('Not found', [], 404);

        foreach ($section->rooms ?? [] as $room) {
            $img = $room['image'] ?? null;
            if ($img && !filter_var($img, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($img);
            }
        }

        $section->delete();
        return $this->sendResponse([], 'Deleted successfully');
    }
}