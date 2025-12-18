<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NewsletterController extends Controller
{
    /**
     * Store a newly created subscription.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:newsletter_subscriptions,email',
            'name' => 'nullable|string|max:255',
            'privacy_accepted' => 'required|accepted',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $subscription = NewsletterSubscription::create([
            'email' => $request->email,
            'name' => $request->name,
            'privacy_accepted' => true,
        ]);

        // Aquí se podría enviar un email de verificación
        // Mail::to($subscription->email)->send(new NewsletterVerificationMail($subscription));

        return back()->with('success', '¡Gracias por suscribirte a nuestro newsletter!');
    }
}
