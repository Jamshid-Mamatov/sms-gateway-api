<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(): JsonResponse
    {
        $projects = Project::with('provider:id,name,driver')
            ->withCount('smsMessages')
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data'    => $projects,
        ]);
    }

    public function store(StoreProjectRequest $request): JsonResponse
    {
        $project = Project::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Project created. Store the api_key safely — it won\'t be shown again in full.',
            'data'    => $project->load('provider:id,name,driver'),
        ], 201);
    }

    public function show(Project $project): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $project->load('provider:id,name,driver')->loadCount('smsMessages'),
        ]);
    }

    public function update(UpdateProjectRequest $request, Project $project): JsonResponse
    {
        $project->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Project updated.',
            'data'    => $project->fresh()->load('provider:id,name,driver'),
        ]);
    }

    public function destroy(Project $project): JsonResponse
    {
        $project->delete(); // cascade deletes sms_messages

        return response()->json([
            'success' => true,
            'message' => 'Project deleted.',
        ]);
    }
}
