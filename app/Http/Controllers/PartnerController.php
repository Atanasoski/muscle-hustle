<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePartnerRequest;
use App\Http\Requests\UpdatePartnerRequest;
use App\Models\Partner;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PartnerController extends Controller
{
    /**
     * Display a listing of the partners.
     */
    public function index(): View
    {
        $partners = Partner::with(['identity', 'users'])->latest()->get();

        return view('partners.index', compact('partners'));
    }

    /**
     * Show the form for creating a new partner.
     */
    public function create(): View
    {
        return view('partners.create');
    }

    /**
     * Store a newly created partner in storage.
     */
    public function store(StorePartnerRequest $request): RedirectResponse
    {
        $partner = Partner::create($request->only(['name', 'slug', 'domain', 'is_active']));

        $identityData = $request->only([
            'primary_color',
            'secondary_color',
            'font_family',
            'background_color',
            'card_background_color',
            'text_primary_color',
            'text_secondary_color',
            'text_on_primary_color',
            'success_color',
            'warning_color',
            'danger_color',
            'accent_color',
            'border_color',
            'background_pattern',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('partners', 'public');
            $identityData['logo'] = 'storage/'.$logoPath;
        }

        $partner->identity()->create($identityData);

        return redirect()->route('partners.index')
            ->with('success', 'Partner created successfully.');
    }

    /**
     * Display the specified partner.
     */
    public function show(Partner $partner): View
    {
        $partner->load('identity', 'users');

        return view('partners.show', compact('partner'));
    }

    /**
     * Show the form for editing the specified partner.
     */
    public function edit(Partner $partner): View
    {
        $partner->load('identity');

        return view('partners.edit', compact('partner'));
    }

    /**
     * Update the specified partner in storage.
     */
    public function update(UpdatePartnerRequest $request, Partner $partner): RedirectResponse
    {
        $partner->update($request->only(['name', 'slug', 'domain', 'is_active']));

        $identityData = $request->only([
            'primary_color',
            'secondary_color',
            'font_family',
            'background_color',
            'card_background_color',
            'text_primary_color',
            'text_secondary_color',
            'text_on_primary_color',
            'success_color',
            'warning_color',
            'danger_color',
            'accent_color',
            'border_color',
            'background_pattern',
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

        return redirect()->route('partners.index')
            ->with('success', 'Partner updated successfully.');
    }

    /**
     * Remove the specified partner from storage.
     */
    public function destroy(Partner $partner): RedirectResponse
    {
        // Delete logo if exists
        if ($partner->identity?->logo) {
            $logoPath = str_replace('storage/', '', $partner->identity->logo);
            \Storage::disk('public')->delete($logoPath);
        }

        $partner->delete();

        return redirect()->route('partners.index')
            ->with('success', 'Partner deleted successfully.');
    }
}
