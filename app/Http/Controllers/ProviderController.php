<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProviderRequest;
use App\Models\Provider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProviderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $providers = Provider::withCount('projects')->latest()->get();
        return response()->json([
            'success' => true,
            'data'    => $providers,
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProviderRequest $request)
    {

        $provider = Provider::create($request->validated());
        return response()->json([
            'success' => true,
            'data'    => $provider,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Provider $provider): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $provider->loadCount('projects'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreProviderRequest $request, Provider $provider): JsonResponse
    {
        $provider->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Provider updated successfully.',
            'data'    => $provider->fresh(),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Provider $provider)
    {

        if($provider->projects()->exists()){
            return response()->json([
                'success' => false,
                'message' => 'Provider has projects, cannot be deleted.',
            ],422);
        }
        $provider->delete();
        return response()->json([
            'success' => true,
            'message' => 'Provider deleted successfully.',
        ]);
    }
}
