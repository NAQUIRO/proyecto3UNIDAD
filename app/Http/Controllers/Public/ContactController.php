<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Mail\ContactFormMail;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    /**
     * Show the contact form
     */
    public function index()
    {
        return view('public.contact.index');
    }

    /**
     * Store a newly created contact message
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        // Guardar mensaje en la base de datos
        $contactMessage = ContactMessage::create($validated);

        // Enviar email de notificación
        try {
            Mail::to(config('mail.from.address'))->send(new ContactFormMail($contactMessage));
        } catch (\Exception $e) {
            // Log error pero no fallar el proceso
            \Log::error('Error enviando email de contacto: ' . $e->getMessage());
        }

        return redirect()->route('public.contact.index')
            ->with('success', '¡Mensaje enviado exitosamente! Nos pondremos en contacto contigo pronto.');
    }
}
