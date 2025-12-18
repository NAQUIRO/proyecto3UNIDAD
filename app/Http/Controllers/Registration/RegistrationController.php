<?php

namespace App\Http\Controllers\Registration;

use App\Http\Controllers\Controller;
use App\Models\Congress;
use App\Models\Coupon;
use App\Models\Registration;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show registration form
     */
    public function create(Congress $congress)
    {
        // Verificar que el congreso esté publicado
        if (!$congress->isPublished()) {
            return redirect()->route('public.congresses.show', $congress->slug)
                ->with('error', 'Este congreso no está disponible para inscripciones.');
        }

        // Verificar que no esté ya registrado
        $existingRegistration = Registration::where('congress_id', $congress->id)
            ->where('user_id', auth()->id())
            ->whereIn('status', ['pending', 'confirmed'])
            ->first();

        if ($existingRegistration) {
            return redirect()->route('registration.show', [$congress, $existingRegistration])
                ->with('info', 'Ya tienes una inscripción pendiente o confirmada para este congreso.');
        }

        // Obtener tarifa actual
        $currentFee = $congress->activeFees()->first();
        if (!$currentFee) {
            return redirect()->route('public.congresses.show', $congress->slug)
                ->with('error', 'No hay tarifas disponibles para este congreso.');
        }

        // Determinar rol (si tiene papers aceptados, puede ser speaker)
        $userRole = 'attendee';
        $hasAcceptedPapers = $congress->papers()
            ->where('user_id', auth()->id())
            ->where('status', 'accepted')
            ->exists();

        if ($hasAcceptedPapers) {
            $userRole = 'speaker';
        }

        $coupons = $congress->coupons()
            ->where('is_active', true)
            ->where('is_public', true)
            ->where('valid_from', '<=', now())
            ->where('valid_until', '>=', now())
            ->get();

        return view('registration.create', compact('congress', 'currentFee', 'userRole', 'coupons'));
    }

    /**
     * Store registration
     */
    public function store(Request $request, Congress $congress)
    {
        // Verificar que el congreso esté publicado
        if (!$congress->isPublished()) {
            return back()->with('error', 'Este congreso no está disponible para inscripciones.');
        }

        // Verificar que no esté ya registrado
        $existingRegistration = Registration::where('congress_id', $congress->id)
            ->where('user_id', auth()->id())
            ->whereIn('status', ['pending', 'confirmed'])
            ->first();

        if ($existingRegistration) {
            return redirect()->route('registration.show', [$congress, $existingRegistration])
                ->with('info', 'Ya tienes una inscripción pendiente o confirmada.');
        }

        $validated = $request->validate([
            'role' => 'required|in:attendee,speaker',
            'coupon_code' => 'nullable|string|max:50',
        ]);

        // Obtener tarifa actual
        $currentFee = $congress->activeFees()->first();
        if (!$currentFee) {
            return back()->with('error', 'No hay tarifas disponibles.');
        }

        $amount = $currentFee->amount;
        $discountAmount = 0;
        $coupon = null;

        // Aplicar cupón si se proporciona
        if ($request->coupon_code) {
            $coupon = Coupon::where('congress_id', $congress->id)
                ->where('code', $request->coupon_code)
                ->where('is_active', true)
                ->where('valid_from', '<=', now())
                ->where('valid_until', '>=', now())
                ->first();

            if ($coupon && $coupon->isValid()) {
                $discountAmount = $coupon->calculateDiscount($amount);
            }
        }

        $finalAmount = $amount - $discountAmount;

        // Crear registro
        $registration = Registration::create([
            'congress_id' => $congress->id,
            'user_id' => auth()->id(),
            'role' => $validated['role'],
            'status' => 'pending',
            'amount' => $amount,
            'discount_amount' => $discountAmount,
            'final_amount' => $finalAmount,
            'coupon_id' => $coupon?->id,
            'fee_id' => $currentFee->id,
            'payment_status' => 'pending',
            'registered_at' => now(),
        ]);

        // Incrementar uso del cupón si se aplicó
        if ($coupon) {
            $coupon->incrementUses(auth()->id(), $congress->id);
        }

        return redirect()->route('payment.create', [$congress, $registration])
            ->with('success', 'Inscripción creada. Procede con el pago.');
    }

    /**
     * Show registration details
     */
    public function show(Congress $congress, Registration $registration)
    {
        if ($registration->user_id !== auth()->id()) {
            abort(403);
        }

        $registration->load(['payment', 'coupon', 'fee']);

        return view('registration.show', compact('congress', 'registration'));
    }
}
