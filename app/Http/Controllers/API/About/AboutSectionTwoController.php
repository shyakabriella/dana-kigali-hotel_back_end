<?php

namespace App\Http\Controllers\Api\About;

use App\Http\Controllers\Api\BaseController;
use App\Models\About\AboutSectionTwo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AboutSectionTwoController extends BaseController
{
    // GET /api/dana/about/section-two - Get all (Public)
    public function index()
    {
        $sections = AboutSectionTwo::orderBy('id', 'desc')->get();
        
        $data = $sections->map(function ($section) {
            return [
                'id' => $section->id,
                'title' => $section->title,
                'subtitle' => $section->subtitle,
                'values' => $section->values,
            ];
        });

        return $this->sendResponse($data, 'About section two retrieved successfully');
    }

    // GET /api/dana/about/section-two/{id} - Get single (Public)
    public function show($id)
    {
        $section = AboutSectionTwo::find($id);
        
        if (!$section) {
            return $this->sendError('About section not found', [], 404);
        }

        return $this->sendResponse([
            'id' => $section->id,
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'values' => $section->values,
        ], 'About section retrieved successfully');
    }

    // POST /api/dana/about/section-two - CREATE (Admin only)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'values' => 'required|array|min:1',
            'values.*.title' => 'required|string',
            'values.*.description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $section = AboutSectionTwo::first();

        $data = [
            'title' => $request->title ?? ($section->title ?? '— OUR VALUES'),
            'subtitle' => $request->subtitle ?? ($section->subtitle ?? 'The spirit of Dana, in everything we do.'),
            'values' => $request->values,
        ];

        if ($section) {
            $section->update($data);
            $message = 'About section two updated successfully';
        } else {
            $section = AboutSectionTwo::create($data);
            $message = 'About section two created successfully';
        }

        return $this->sendResponse([
            'id' => $section->id,
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'values' => $section->values,
        ], $message);
    }

    // PUT /api/dana/about/section-two/{id} - UPDATE (Admin only)
    public function update(Request $request, $id)
    {
        $section = AboutSectionTwo::find($id);
        
        if (!$section) {
            return $this->sendError('About section not found', [], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'values' => 'nullable|array',
            'values.*.title' => 'required_with:values|string',
            'values.*.description' => 'required_with:values|string',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $data = [];
        
        if ($request->has('title')) $data['title'] = $request->title;
        if ($request->has('subtitle')) $data['subtitle'] = $request->subtitle;
        if ($request->has('values')) $data['values'] = $request->values;

        $section->update($data);

        return $this->sendResponse([
            'id' => $section->id,
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'values' => $section->values,
        ], 'About section two updated successfully');
    }

    // DELETE /api/dana/about/section-two/{id} - DELETE (Admin only)
    public function destroy($id)
    {
        $section = AboutSectionTwo::find($id);
        
        if (!$section) {
            return $this->sendError('About section not found', [], 404);
        }
        
        $section->delete();

        return $this->sendResponse([], 'About section two deleted successfully');
    }

    // DELETE /api/dana/about/section-two/{id}/value/{index} - Delete single value card
    public function deleteValue($id, $index)
    {
        $section = AboutSectionTwo::find($id);
        
        if (!$section) {
            return $this->sendError('About section not found', [], 404);
        }

        $values = $section->values;
        
        if (!isset($values[$index])) {
            return $this->sendError('Value not found', [], 404);
        }
        
        array_splice($values, $index, 1);
        $section->update(['values' => $values]);

        return $this->sendResponse([], 'Value deleted successfully');
    }
}