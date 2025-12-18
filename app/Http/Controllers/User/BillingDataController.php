<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\UserBillingData;
use Illuminate\Http\Request;

class BillingDataController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $billingData = auth()->user()->billingData()->orderBy('is_default', 'desc')->get();
        return view('user.billing-data.index', compact('billingData'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('user.billing-data.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tax_id' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'nullable|string|max:255',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:2',
            'phone' => 'nullable|string|max:20',
            'is_default' => 'boolean',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['is_default'] = $request->has('is_default');

        // Si se marca como predeterminado, desmarcar los demás
        if ($validated['is_default']) {
            auth()->user()->billingData()->update(['is_default' => false]);
        }

        UserBillingData::create($validated);

        return redirect()->route('user.billing-data.index')
            ->with('success', 'Datos de facturación guardados exitosamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserBillingData $billingData)
    {
        // Verificar que el usuario sea el propietario
        if ($billingData->user_id !== auth()->id()) {
            abort(403);
        }

        return view('user.billing-data.edit', compact('billingData'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UserBillingData $billingData)
    {
        // Verificar que el usuario sea el propietario
        if ($billingData->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'tax_id' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'nullable|string|max:255',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:2',
            'phone' => 'nullable|string|max:20',
            'is_default' => 'boolean',
        ]);

        $validated['is_default'] = $request->has('is_default');

        // Si se marca como predeterminado, desmarcar los demás
        if ($validated['is_default']) {
            auth()->user()->billingData()->where('id', '!=', $billingData->id)->update(['is_default' => false]);
        }

        $billingData->update($validated);

        return redirect()->route('user.billing-data.index')
            ->with('success', 'Datos de facturación actualizados exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserBillingData $billingData)
    {
        // Verificar que el usuario sea el propietario
        if ($billingData->user_id !== auth()->id()) {
            abort(403);
        }

        $billingData->delete();

        return redirect()->route('user.billing-data.index')
            ->with('success', 'Datos de facturación eliminados exitosamente.');
    }
}
