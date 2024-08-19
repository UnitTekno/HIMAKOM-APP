<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Puzzle;
use Illuminate\Support\Facades\Auth;

class CheckPuzzleAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $puzzleId = $request->query('id');
        $puzzle = Puzzle::find($puzzleId);

        if (!$puzzle) {
            return redirect()->back();
        }

        $user = Auth::user();

        if ($user->puzzle_level <= $puzzle->index) {
            return redirect()->back();
        }

        return $next($request);
    }
}
