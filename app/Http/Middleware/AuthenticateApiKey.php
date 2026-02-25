<?php

namespace App\Http\Middleware;

use App\Models\Project;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Validates the X-Api-Key header, resolves the Project,
 * and rejects suspended or inactive projects early.
 */
class AuthenticateApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-Api-Key') ?? $request->query('api_key');

        if (! $apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'API key is missing. Provide it via X-Api-Key header.',
                'error'   => 'missing_api_key',
            ], 401);
        }

        $project = Project::with('provider')
            ->where('api_key', $apiKey)
            ->first();


        if (! $project->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'This project is inactive.',
                'error'   => 'project_inactive',
            ], 403);
        }

        $request->attributes->set('project', $project);

        return $next($request);
    }
}
