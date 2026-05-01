<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $favorites = $request->user()
            ->favorites()
            ->with(['city', 'region', 'images', 'user'])
            ->orderBy('favorites.created_at', 'desc')
            ->paginate($request->get('per_page', 12));

        return response()->json($favorites);
    }

    public function store(Request $request, Listing $listing)
    {
        $user = $request->user();

        if ($user->favorites()->where('listing_id', $listing->id)->exists()) {
            return response()->json([
                'message' => 'Already in favorites',
            ], 409);
        }

        $user->favorites()->attach($listing->id);

        return response()->json([
            'message' => 'Added to favorites',
        ], 201);
    }

    public function destroy(Request $request, Listing $listing)
    {
        $request->user()->favorites()->detach($listing->id);

        return response()->json([
            'message' => 'Removed from favorites',
        ]);
    }

    public function check(Request $request, Listing $listing)
    {
        $isFavorite = $request->user()
            ->favorites()
            ->where('listing_id', $listing->id)
            ->exists();

        return response()->json([
            'is_favorite' => $isFavorite,
        ]);
    }
}
