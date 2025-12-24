<?php

namespace App\Http\Controllers;

use App\Helpers\ColorHelper;
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
        $partners = Partner::with(['identity', 'users'])->latest()->get()
            ->map(function ($partner) {
                $partner->users_count = $partner->users->count();

                return $partner;
            });

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

        // Handle background pattern upload
        if ($request->hasFile('background_pattern')) {
            $patternPath = $request->file('background_pattern')->store('partners', 'public');
            $identityData['background_pattern'] = 'storage/'.$patternPath;
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

        // Process colors using ColorHelper
        $colors = ColorHelper::processPartnerColors($partner->identity);
        $colorPalette = ColorHelper::getColorPalette($partner->identity);

        // Pre-calculate counts
        $usersCount = $partner->users->count();

        return view('partners.show', compact('partner', 'colors', 'colorPalette', 'usersCount'));
    }

    /**
     * Show the form for editing the specified partner.
     */
    public function edit(Partner $partner): View
    {
        $partner->load('identity');

        // Process colors using ColorHelper
        $colors = ColorHelper::processPartnerColors($partner->identity);

        // Build color array for form
        $colorFormData = [
            ['id' => 'primary_color', 'name' => 'Primary Color', 'value' => old('primary_color', $colors['primary']), 'required' => true],
            ['id' => 'secondary_color', 'name' => 'Secondary Color', 'value' => old('secondary_color', $colors['secondary']), 'required' => true],
            ['id' => 'background_color', 'name' => 'Background', 'value' => old('background_color', $colors['background']), 'required' => false],
            ['id' => 'card_background_color', 'name' => 'Card Background', 'value' => old('card_background_color', $colors['card_background']), 'required' => false],
            ['id' => 'text_primary_color', 'name' => 'Text Primary', 'value' => old('text_primary_color', $colors['text_primary']), 'required' => false],
            ['id' => 'text_secondary_color', 'name' => 'Text Secondary', 'value' => old('text_secondary_color', $colors['text_secondary']), 'required' => false],
            ['id' => 'text_on_primary_color', 'name' => 'Text On Primary', 'value' => old('text_on_primary_color', $colors['text_on_primary']), 'required' => false],
            ['id' => 'success_color', 'name' => 'Success', 'value' => old('success_color', $colors['success']), 'required' => false],
            ['id' => 'warning_color', 'name' => 'Warning', 'value' => old('warning_color', $colors['warning']), 'required' => false],
            ['id' => 'danger_color', 'name' => 'Danger', 'value' => old('danger_color', $colors['danger']), 'required' => false],
            ['id' => 'accent_color', 'name' => 'Accent', 'value' => old('accent_color', $colors['accent']), 'required' => false],
            ['id' => 'border_color', 'name' => 'Border', 'value' => old('border_color', $colors['border']), 'required' => false],
        ];

        return view('partners.edit', compact('partner', 'colorFormData'));
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

        // Handle background pattern upload
        if ($request->hasFile('background_pattern')) {
            // Delete old pattern if exists
            if ($partner->identity?->background_pattern) {
                $oldPatternPath = str_replace('storage/', '', $partner->identity->background_pattern);
                \Storage::disk('public')->delete($oldPatternPath);
            }

            $patternPath = $request->file('background_pattern')->store('partners', 'public');
            $identityData['background_pattern'] = 'storage/'.$patternPath;
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

        // Delete background pattern if exists
        if ($partner->identity?->background_pattern) {
            $patternPath = str_replace('storage/', '', $partner->identity->background_pattern);
            \Storage::disk('public')->delete($patternPath);
        }

        $partner->delete();

        return redirect()->route('partners.index')
            ->with('success', 'Partner deleted successfully.');
    }
}
