<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Models\Footer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FooterController extends BaseController
{
    // GET /api/dana/footer - Get footer (Public)
    public function index()
    {
        $footer = Footer::first();
        
        if (!$footer) {
            return $this->sendResponse(null, 'No footer found');
        }

        return $this->sendResponse([
            'id' => $footer->id,
            'hotel_name' => $footer->hotel_name,
            'description' => $footer->description,
            'address' => $footer->address,
            'phone' => $footer->phone,
            'email' => $footer->email,
            'newsletter_placeholder' => $footer->newsletter_placeholder,
            'newsletter_button' => $footer->newsletter_button,
            'social_links' => $footer->social_links,
            'copyright_text' => $footer->copyright_text,
        ], 'Footer retrieved successfully');
    }

    // POST /api/dana/footer - CREATE (Admin only)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hotel_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:255',
            'newsletter_placeholder' => 'nullable|string|max:100',
            'newsletter_button' => 'nullable|string|max:100',
            'social_links' => 'nullable|array',
            'social_links.*.platform' => 'required_with:social_links|string',
            'social_links.*.url' => 'required_with:social_links|string',
            'copyright_text' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $footer = Footer::create([
            'hotel_name' => $request->hotel_name ?? 'DANA KIGALI HOTEL',
            'description' => $request->description ?? 'A welcoming home in Kigali, Rwanda, where kindness, family, and hospitality come first.',
            'address' => $request->address ?? 'KG 7 Ave, Kigali, Rwanda',
            'phone' => $request->phone ?? '+250 788 000 000',
            'email' => $request->email ?? 'stay@danakigali.rw',
            'newsletter_placeholder' => $request->newsletter_placeholder ?? 'Your email',
            'newsletter_button' => $request->newsletter_button ?? 'Join',
            'social_links' => $request->social_links ?? [],
            'copyright_text' => $request->copyright_text ?? '© DANA KIGALI HOTEL. All rights reserved.',
        ]);

        return $this->sendResponse([
            'id' => $footer->id,
            'hotel_name' => $footer->hotel_name,
            'description' => $footer->description,
            'address' => $footer->address,
            'phone' => $footer->phone,
            'email' => $footer->email,
            'newsletter_placeholder' => $footer->newsletter_placeholder,
            'newsletter_button' => $footer->newsletter_button,
            'social_links' => $footer->social_links,
            'copyright_text' => $footer->copyright_text,
        ], 'Footer created successfully', 201);
    }

    // PUT /api/dana/footer/{id} - UPDATE (Admin only)
    public function update(Request $request, $id)
    {
        $footer = Footer::find($id);
        
        if (!$footer) {
            return $this->sendError('Footer not found', [], 404);
        }

        $validator = Validator::make($request->all(), [
            'hotel_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:255',
            'newsletter_placeholder' => 'nullable|string|max:100',
            'newsletter_button' => 'nullable|string|max:100',
            'social_links' => 'nullable|array',
            'social_links.*.platform' => 'required_with:social_links|string',
            'social_links.*.url' => 'required_with:social_links|string',
            'copyright_text' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $data = [];
        
        if ($request->has('hotel_name')) $data['hotel_name'] = $request->hotel_name;
        if ($request->has('description')) $data['description'] = $request->description;
        if ($request->has('address')) $data['address'] = $request->address;
        if ($request->has('phone')) $data['phone'] = $request->phone;
        if ($request->has('email')) $data['email'] = $request->email;
        if ($request->has('newsletter_placeholder')) $data['newsletter_placeholder'] = $request->newsletter_placeholder;
        if ($request->has('newsletter_button')) $data['newsletter_button'] = $request->newsletter_button;
        if ($request->has('social_links')) $data['social_links'] = $request->social_links;
        if ($request->has('copyright_text')) $data['copyright_text'] = $request->copyright_text;

        $footer->update($data);

        return $this->sendResponse([
            'id' => $footer->id,
            'hotel_name' => $footer->hotel_name,
            'description' => $footer->description,
            'address' => $footer->address,
            'phone' => $footer->phone,
            'email' => $footer->email,
            'newsletter_placeholder' => $footer->newsletter_placeholder,
            'newsletter_button' => $footer->newsletter_button,
            'social_links' => $footer->social_links,
            'copyright_text' => $footer->copyright_text,
        ], 'Footer updated successfully');
    }

    // DELETE /api/dana/footer/{id} - DELETE (Admin only)
    public function destroy($id)
    {
        $footer = Footer::find($id);
        
        if (!$footer) {
            return $this->sendError('Footer not found', [], 404);
        }
        
        $footer->delete();

        return $this->sendResponse([], 'Footer deleted successfully');
    }
}