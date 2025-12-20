<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePartnerRequest;
use App\Http\Requests\UpdatePartnerRequest;
use App\Http\Resources\Api\PartnerResource;
use App\Models\Partner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PartnerController extends Controller
{
    /**
     * Display a listing of the partners.
     */
    public function index(): AnonymousResourceCollection
    {
        $partners = Partner::with('identity')->latest()->get();

        return PartnerResource::collection($partners);
    }

    /**
     * Store a newly created partner in storage.
     */
    public function store(StorePartnerRequest $request): JsonResponse
    {
        $partner = Partner::create($request->only(['name', 'slug', 'domain', 'is_active']));

        $identityData = $request->only([
            'primary_color',
            'secondary_color',
            'font_family',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('partners', 'public');
            $identityData['logo'] = 'storage/'.$logoPath;
        }

        $partner->identity()->create($identityData);

        return response()->json([
            'message' => 'Partner created successfully',
            'data' => new PartnerResource($partner->load('identity')),
        ], 201);
    }

    /**
     * Display the specified partner.
     */
    public function show(Partner $partner): JsonResponse
    {
        $partner->load('identity', 'users');

        return response()->json([
            'data' => new PartnerResource($partner),
        ]);
    }

    /**
     * Update the specified partner in storage.
     */
    public function update(UpdatePartnerRequest $request, Partner $partner): JsonResponse
    {
        $partner->update($request->only(['name', 'slug', 'domain', 'is_active']));

        $identityData = $request->only([
            'primary_color',
            'secondary_color',
            'font_family',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($partner->identity?->logo) {
                $oldLogoPath = str_replace('storage/', '', $partner->identity->logo);
                \Storage::disk('public')->delete($oldLogoPath);
            }

            $logoPath = $request->file('logo')->store('partners', 'public');
            $identityData['logo'] = 'storage/'.$logoPath;
        }

        if ($partner->identity) {
            $partner->identity->update($identityData);
        } else {
            $partner->identity()->create($identityData);
        }

        return response()->json([
            'message' => 'Partner updated successfully',
            'data' => new PartnerResource($partner->load('identity')),
        ]);
    }

    /**
     * Remove the specified partner from storage.
     */
    public function destroy(Partner $partner): JsonResponse
    {
        // Delete logo if exists
        if ($partner->identity?->logo) {
            $logoPath = str_replace('storage/', '', $partner->identity->logo);
            \Storage::disk('public')->delete($logoPath);
        }

        $partner->delete();

        return response()->json([
            'message' => 'Partner deleted successfully',
        ]);
    }
}
