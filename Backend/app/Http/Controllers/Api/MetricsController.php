<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Listing;
use App\Models\Chat;
use App\Models\Message;
use App\Models\City;
use App\Models\Region;
use Illuminate\Http\JsonResponse;

class MetricsController extends Controller
{
    /**
     * Get application statistics.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $stats = $this->getStats();

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get metrics in Prometheus text format.
     *
     * @return \Illuminate\Http\Response
     */
    public function prometheus()
    {
        $stats = $this->getStats();

        $output = "# HELP users_total Total number of users\n";
        $output .= "# TYPE users_total counter\n";
        $output .= "users_total " . $stats['users']['total'] . "\n\n";

        $output .= "# HELP students_total total students\n";
        $output .= "# TYPE students_total counter\n";
        $output .= "students_total " . $stats['users']['students'] . "\n\n";

        $output .= "# HELP brokers_total total brokers\n";
        $output .= "# TYPE brokers_total counter\n";
        $output .= "brokers_total " . $stats['users']['brokers'] . "\n\n";

        $output .= "# HELP listings_total total listings\n";
        $output .= "# TYPE listings_total counter\n";
        $output .= "listings_total " . $stats['listings']['total'] . "\n\n";

        $output .= "# HELP chats_total total chats\n";
        $output .= "# TYPE chats_total counter\n";
        $output .= "chats_total " . $stats['interactions']['chats'] . "\n\n";

        $output .= "# HELP messages_total total messages\n";
        $output .= "# TYPE messages_total counter\n";
        $output .= "messages_total " . $stats['interactions']['messages'] . "\n";

        return response($output)->header('Content-Type', 'text/plain');
    }

    /**
     * Internal helper to get all stats.
     */
    private function getStats(): array
    {
        // Get user counts
        $usersTotal = User::count();
        $studentsCount = User::whereHas('role', function ($query) {
            $query->where('name', 'student');
        })->count();
        $ownersCount = User::whereHas('role', function ($query) {
            $query->where('name', 'owner');
        })->count();
        $brokersCount = User::whereHas('role', function ($query) {
            $query->where('name', 'broker');
        })->count();

        // Get listing counts
        $listingsTotal = Listing::count();
        $availableListings = Listing::where('is_available', true)->count();
        $unavailableListings = $listingsTotal - $availableListings;

        // Get interaction counts
        $chatsCount = Chat::count();
        $messagesCount = Message::count();

        // Get location counts
        $citiesCount = City::count();
        $regionsCount = Region::count();

        return [
            'users' => [
                'total' => $usersTotal,
                'students' => $studentsCount,
                'owners' => $ownersCount,
                'brokers' => $brokersCount,
            ],
            'listings' => [
                'total' => $listingsTotal,
                'available' => $availableListings,
                'unavailable' => $unavailableListings,
            ],
            'interactions' => [
                'chats' => $chatsCount,
                'messages' => $messagesCount,
            ],
            'locations' => [
                'cities' => $citiesCount,
                'regions' => $regionsCount,
            ],
        ];
    }
}
