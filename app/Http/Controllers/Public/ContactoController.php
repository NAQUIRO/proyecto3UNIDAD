<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ContactoController extends Controller
{
    public function index()
    {
        return view('public.contacto.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'asunto' => 'nullable|string|max:255',
            'mensaje' => 'required|string|min:10|max:2000',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico no es válido.',
            'mensaje.required' => 'El mensaje es obligatorio.',
            'mensaje.min' => 'El mensaje debe tener al menos 10 caracteres.',
            'mensaje.max' => 'El mensaje no puede exceder 2000 caracteres.',
        ]);

        // Aquí puedes agregar lógica para enviar el email o guardar en BD
        // Por ejemplo: Mail::to('info@eventhub.com')->send(new ContactMail($validated));

        return redirect()->route('contacto.index')
            ->with('success', '¡Mensaje enviado exitosamente! Nos pondremos en contacto contigo pronto.');
    }
}
