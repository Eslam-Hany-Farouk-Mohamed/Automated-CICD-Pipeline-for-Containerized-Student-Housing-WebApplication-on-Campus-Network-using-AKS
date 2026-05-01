<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ListingController extends Controller
{
    public function index(Request $request)
    {
        $query = Listing::with(['user', 'city', 'region', 'images'])
            ->where('is_available', true);

        // Filter by city
        if ($request->has('city_id')) {
            $query->where('city_id', $request->city_id);
        }

        // Filter by region
        if ($request->has('region_id')) {
            $query->where('region_id', $request->region_id);
        }

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filter by price range
        if ($request->has('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }
        if ($request->has('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        // Search by title/description
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('title_ar', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('description_ar', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $listings = $query->paginate($request->get('per_page', 12));

        return response()->json($listings);
    }

    public function show(Listing $listing)
    {
        return response()->json([
            'listing' => $listing->load(['user', 'city', 'region', 'images']),
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        if (!$user->canManageListings()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'description' => 'required|string',
            'description_ar' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'city_id' => 'required|exists:cities,id',
            'region_id' => 'required|exists:regions,id',
            'type' => 'required|in:room,apartment,studio,shared',
            'rules' => 'nullable|string',
            'rules_ar' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'address_ar' => 'nullable|string|max:255',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'area_sqm' => 'nullable|integer|min:0',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $listing = $user->listings()->create($validated);

        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('listings', 'public');
                $listing->images()->create([
                    'path' => $path,
                    'is_primary' => $index === 0,
                    'order' => $index,
                ]);
            }
        }

        return response()->json([
            'message' => 'Listing created successfully',
            'listing' => $listing->load(['city', 'region', 'images']),
        ], 201);
    }

    public function update(Request $request, Listing $listing)
    {
        $user = $request->user();

        if ($listing->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'description' => 'sometimes|string',
            'description_ar' => 'nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'city_id' => 'sometimes|exists:cities,id',
            'region_id' => 'sometimes|exists:regions,id',
            'type' => 'sometimes|in:room,apartment,studio,shared',
            'rules' => 'nullable|string',
            'rules_ar' => 'nullable|string',
            'is_available' => 'sometimes|boolean',
            'address' => 'nullable|string|max:255',
            'address_ar' => 'nullable|string|max:255',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'area_sqm' => 'nullable|integer|min:0',
        ]);

        $listing->update($validated);

        return response()->json([
            'message' => 'Listing updated successfully',
            'listing' => $listing->fresh()->load(['city', 'region', 'images']),
        ]);
    }

    public function destroy(Request $request, Listing $listing)
    {
        $user = $request->user();

        if ($listing->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Delete associated images from storage
        foreach ($listing->images as $image) {
            Storage::disk('public')->delete($image->path);
        }

        $listing->delete();

        return response()->json([
            'message' => 'Listing deleted successfully',
        ]);
    }

    public function myListings(Request $request)
    {
        $listings = $request->user()
            ->listings()
            ->with(['city', 'region', 'images'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 12));

        return response()->json($listings);
    }
}
