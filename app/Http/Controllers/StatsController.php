<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class StatsController extends Controller
{
    use ApiResponse;
    public function index()
    {
        $cacheKey = 'stats';

        $stats = Cache::remember($cacheKey, 60, function () {
            return [
                'total_users' => User::count(),
                'total_posts' => Post::count(),
                'users_with_zero_posts' => User::doesntHave('posts')->count(),
            ];
        });

        return $this->sendData('Data retrived successfully', $stats);
    }
}