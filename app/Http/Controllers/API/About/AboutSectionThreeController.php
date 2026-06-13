<?php

namespace App\Http\Controllers\Api\About;

use App\Http\Controllers\Api\BaseController;
use App\Models\About\AboutSectionThree;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AboutSectionThreeController extends BaseController
{
    // GET /api/dana/about/section-three - Get all (Public)
    public function index()
    {
        $sections = AboutSectionThree::orderBy('id', 'desc')->get();
        
        $data = $sections->map(function ($section) {
            return [
                'id' => $section->id,
                'title' => $section->title,
                'subtitle' => $section->subtitle,
                'timeline' => $section->timeline,
            ];
        });

        return $this->sendResponse($data, 'About section three retrieved successfully');
    }

    // GET /api/dana/about/section-three/{id} - Get single (Public)
    public function show($id)
    {
        $section = AboutSectionThree::find($id);
        
        if (!$section) {
            return $this->sendError('About section not found', [], 404);
        }

        return $this->sendResponse([
            'id' => $section->id,
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'timeline' => $section->timeline,
        ], 'About section retrieved successfully');
    }

    // POST /api/dana/about/section-three - CREATE (Admin only)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'timeline' => 'required|array|min:1',
            'timeline.*.period' => 'required|string',
            'timeline.*.title' => 'required|string',
            'timeline.*.description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $section = AboutSectionThree::first();

        $data = [
            'title' => $request->title ?? ($section->title ?? '— OUR HERITAGE'),
            'subtitle' => $request->subtitle ?? ($section->subtitle ?? 'A legacy from the Nile to the hills.'),
            'timeline' => $request->timeline,
        ];

        if ($section) {
            $section->update($data);
            $message = 'About section three updated successfully';
        } else {
            $section = AboutSectionThree::create($data);
            $message = 'About section three created successfully';
        }

        return $this->sendResponse([
            'id' => $section->id,
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'timeline' => $section->timeline,
        ], $message);
    }

    // PUT /api/dana/about/section-three/{id} - UPDATE (Admin only)
    public function update(Request $request, $id)
    {
        $section = AboutSectionThree::find($id);
        
        if (!$section) {
            return $this->sendError('About section not found', [], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'timeline' => 'nullable|array',
            'timeline.*.period' => 'required_with:timeline|string',
            'timeline.*.title' => 'required_with:timeline|string',
            'timeline.*.description' => 'required_with:timeline|string',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $data = [];
        
        if ($request->has('title')) $data['title'] = $request->title;
        if ($request->has('subtitle')) $data['subtitle'] = $request->subtitle;
        if ($request->has('timeline')) $data['timeline'] = $request->timeline;

        $section->update($data);

        return $this->sendResponse([
            'id' => $section->id,
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'timeline' => $section->timeline,
        ], 'About section three updated successfully');
    }

    // DELETE /api/dana/about/section-three/{id} - DELETE (Admin only)
    public function destroy($id)
    {
        $section = AboutSectionThree::find($id);
        
        if (!$section) {
            return $this->sendError('About section not found', [], 404);
        }
        
        $section->delete();

        return $this->sendResponse([], 'About section three deleted successfully');
    }

    // DELETE /api/dana/about/section-three/{id}/timeline/{index} - Delete single timeline item
    public function deleteTimelineItem($id, $index)
    {
        $section = AboutSectionThree::find($id);
        
        if (!$section) {
            return $this->sendError('About section not found', [], 404);
        }

        $timeline = $section->timeline;
        
        if (!isset($timeline[$index])) {
            return $this->sendError('Timeline item not found', [], 404);
        }
        
        array_splice($timeline, $index, 1);
        $section->update(['timeline' => $timeline]);

        return $this->sendResponse([], 'Timeline item deleted successfully');
    }
}