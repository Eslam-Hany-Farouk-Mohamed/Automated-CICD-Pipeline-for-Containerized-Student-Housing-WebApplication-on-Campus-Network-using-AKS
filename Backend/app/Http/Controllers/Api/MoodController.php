<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MoodEntry;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MoodController extends Controller
{
    public function saveMood(Request $request)
    {
        $validated = $request->validate([
            'mood' => 'required|in:amazing,good,ok,sad,stressed,angry,anxious',
            'date' => 'sometimes|date',
            'notes' => 'nullable|string|max:500',
        ]);

        $user = $request->user();
        $date = $validated['date'] ?? today();

        $moodEntry = MoodEntry::updateOrCreate(
            [
                'user_id' => $user->id,
                'date' => $date,
            ],
            [
                'mood' => $validated['mood'],
                'notes' => $validated['notes'] ?? null,
            ]
        );

        return response()->json([
            'message' => 'Mood saved successfully',
            'mood' => $moodEntry,
        ]);
    }

    public function getWeeklyMoods(Request $request)
    {
        $user = $request->user();
        
        // Get last 7 days of mood entries
        $moods = MoodEntry::where('user_id', $user->id)
            ->where('date', '>=', today()->subDays(6))
            ->where('date', '<=', today())
            ->orderBy('date', 'asc')
            ->get();

        // Create array for all 7 days
        $weeklyData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = today()->subDays($i);
            $mood = $moods->firstWhere('date', $date);
            
            $weeklyData[] = [
                'date' => $date->toDateString(),
                'day' => $date->format('D'),
                'dayName' => ['Sunday' => 'الأحد', 'Monday' => 'الاثنين', 'Tuesday' => 'الثلاثاء', 'Wednesday' => 'الأربعاء', 'Thursday' => 'الخميس', 'Friday' => 'الجمعة', 'Saturday' => 'السبت'][$date->format('l')],
                'mood' => $mood?->mood ?? null,
                'notes' => $mood?->notes ?? null,
            ];
        }

        return response()->json([
            'moods' => $weeklyData,
            'currentMood' => $moods->lastWhere('date', today())?->mood ?? null,
        ]);
    }

    public function getTodayMood(Request $request)
    {
        $user = $request->user();
        $today = today();

        $mood = MoodEntry::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        return response()->json([
            'mood' => $mood?->mood ?? null,
            'notes' => $mood?->notes ?? null,
            'date' => $today->toDateString(),
        ]);
    }
}
