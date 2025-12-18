<?php

namespace App\Http\Controllers\Congress;

use App\Http\Controllers\Controller;
use App\Models\Congress;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Congress $congress)
    {
        $coupons = $congress->coupons()->orderBy('created_at', 'desc')->get();
        return view('congress.coupons.index', compact('congress', 'coupons'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Congress $congress)
    {
        return view('congress.coupons.create', compact('congress'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Congress $congress)
    {
        $validated = $request->validate([
            'code' => 'nullable|string|max:255|unique:coupons,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after:valid_from',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_limit_per_user' => 'required|integer|min:1',
            'minimum_amount' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['congress_id'] = $congress->id;
        $validated['is_active'] = $request->has('is_active');
        
        // Si no se proporciona código, se generará automáticamente en el modelo
        if (empty($validated['code'])) {
            unset($validated['code']);
        }

        Coupon::create($validated);

        return redirect()->route('congress.coupons.index', $congress)
            ->with('success', 'Cupón creado exitosamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Congress $congress, Coupon $coupon)
    {
        return view('congress.coupons.edit', compact('congress', 'coupon'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Congress $congress, Coupon $coupon)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:coupons,code,' . $coupon->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after:valid_from',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_limit_per_user' => 'required|integer|min:1',
            'minimum_amount' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $coupon->update($validated);

        return redirect()->route('congress.coupons.index', $congress)
            ->with('success', 'Cupón actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Congress $congress, Coupon $coupon)
    {
        $coupon->delete();

        return redirect()->route('congress.coupons.index', $congress)
            ->with('success', 'Cupón eliminado exitosamente.');
    }

    /**
     * Validate a coupon code
     */
    public function validate(Request $request, Congress $congress)
    {
        $request->validate([
            'code' => 'required|string',
            'amount' => 'required|numeric|min:0',
        ]);

        $coupon = Coupon::where('congress_id', $congress->id)
            ->where('code', strtoupper($request->code))
            ->first();

        if (!$coupon) {
            return response()->json([
                'valid' => false,
                'message' => 'Cupón no encontrado'
            ], 404);
        }

        if (!$coupon->isValid()) {
            return response()->json([
                'valid' => false,
                'message' => 'El cupón no es válido o ha expirado'
            ], 400);
        }

        if (!$coupon->canBeUsedByUser(auth()->id())) {
            return response()->json([
                'valid' => false,
                'message' => 'Has alcanzado el límite de usos para este cupón'
            ], 400);
        }

        $discount = $coupon->calculateDiscount($request->amount);
        $finalAmount = $request->amount - $discount;

        return response()->json([
            'valid' => true,
            'coupon' => [
                'id' => $coupon->id,
                'code' => $coupon->code,
                'name' => $coupon->name,
                'discount_type' => $coupon->discount_type,
                'discount_value' => $coupon->discount_value,
            ],
            'discount' => $discount,
            'original_amount' => $request->amount,
            'final_amount' => max(0, $finalAmount),
        ]);
    }
}
