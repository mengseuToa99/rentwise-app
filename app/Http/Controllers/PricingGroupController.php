<?php

namespace App\Http\Controllers;

use App\Models\PricingGroup;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PricingGroupController extends Controller
{
    /**
     * Display a listing of pricing groups for a property.
     */
    public function index(Request $request, $propertyId)
    {
        $property = Property::findOrFail($propertyId);
        
        // Check if the user is authorized to view this property
        if ($property->landlord_id != Auth::id() && !Auth::user()->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $pricingGroups = PricingGroup::where('property_id', $propertyId)->get();
        
        return response()->json([
            'success' => true,
            'data' => $pricingGroups
        ]);
    }

    /**
     * Store a newly created pricing group in storage.
     */
    public function store(Request $request, $propertyId)
    {
        $property = Property::findOrFail($propertyId);
        
        // Check if the user is authorized to modify this property
        if ($property->landlord_id != Auth::id() && !Auth::user()->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'group_name' => 'required|string|max:255',
            'room_type' => 'required|string|max:255',
            'base_price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'amenities' => 'nullable|array',
            'status' => 'nullable|in:active,inactive'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $pricingGroup = new PricingGroup();
        $pricingGroup->property_id = $propertyId;
        $pricingGroup->group_name = $request->group_name;
        $pricingGroup->room_type = $request->room_type;
        $pricingGroup->base_price = $request->base_price;
        $pricingGroup->description = $request->description;
        $pricingGroup->amenities = $request->amenities;
        $pricingGroup->status = $request->status ?? 'active';
        $pricingGroup->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Pricing group created successfully',
            'data' => $pricingGroup
        ], 201);
    }

    /**
     * Display the specified pricing group.
     */
    public function show($propertyId, $groupId)
    {
        $property = Property::findOrFail($propertyId);
        
        // Check if the user is authorized to view this property
        if ($property->landlord_id != Auth::id() && !Auth::user()->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $pricingGroup = PricingGroup::where('property_id', $propertyId)
            ->where('group_id', $groupId)
            ->firstOrFail();
        
        return response()->json([
            'success' => true,
            'data' => $pricingGroup
        ]);
    }

    /**
     * Update the specified pricing group in storage.
     */
    public function update(Request $request, $propertyId, $groupId)
    {
        $property = Property::findOrFail($propertyId);
        
        // Check if the user is authorized to modify this property
        if ($property->landlord_id != Auth::id() && !Auth::user()->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $pricingGroup = PricingGroup::where('property_id', $propertyId)
            ->where('group_id', $groupId)
            ->firstOrFail();
        
        $validator = Validator::make($request->all(), [
            'group_name' => 'sometimes|string|max:255',
            'room_type' => 'sometimes|string|max:255',
            'base_price' => 'sometimes|numeric|min:0',
            'description' => 'nullable|string',
            'amenities' => 'nullable|array',
            'status' => 'nullable|in:active,inactive'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $pricingGroup->fill($request->only([
            'group_name',
            'room_type',
            'base_price',
            'description',
            'amenities',
            'status'
        ]));
        
        $pricingGroup->save();
        
        // Update all units that use this pricing group
        foreach ($pricingGroup->units as $unit) {
            $unit->applyGroupPricing();
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Pricing group updated successfully',
            'data' => $pricingGroup
        ]);
    }

    /**
     * Remove the specified pricing group from storage.
     */
    public function destroy($propertyId, $groupId)
    {
        $property = Property::findOrFail($propertyId);
        
        // Check if the user is authorized to modify this property
        if ($property->landlord_id != Auth::id() && !Auth::user()->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $pricingGroup = PricingGroup::where('property_id', $propertyId)
            ->where('group_id', $groupId)
            ->firstOrFail();
        
        // Set all units using this pricing group to null
        foreach ($pricingGroup->units as $unit) {
            $unit->pricing_group_id = null;
            $unit->save();
        }
        
        $pricingGroup->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Pricing group deleted successfully'
        ]);
    }
}
