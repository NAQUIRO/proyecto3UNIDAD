<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ImpersonationLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ImpersonationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Start impersonating a user
     */
    public function start(Request $request, User $user)
    {
        // Verificar permisos (solo Super Admin o Admin)
        if (!auth()->user()->hasRole(['Super Admin', 'Admin'])) {
            abort(403, 'No tienes permisos para suplantar usuarios');
        }

        // No permitir suplantar a sí mismo
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes suplantar tu propia cuenta');
        }

        // Guardar el ID del admin original en la sesión
        Session::put('impersonate_admin_id', auth()->id());
        Session::put('impersonate_user_id', $user->id);

        // Crear log de suplantación
        $log = ImpersonationLog::create([
            'admin_id' => auth()->id(),
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'started_at' => now(),
            'reason' => $request->input('reason', 'Soporte técnico'),
        ]);

        // Iniciar sesión como el usuario
        Auth::login($user);

        return redirect()->route('home')
            ->with('success', "Ahora estás suplantando a {$user->name}. Usa el botón 'Dejar de suplantar' para volver a tu cuenta.");
    }

    /**
     * Stop impersonating
     */
    public function stop()
    {
        if (!Session::has('impersonate_admin_id')) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No estás suplantando a ningún usuario');
        }

        $adminId = Session::get('impersonate_admin_id');
        $userId = Session::get('impersonate_user_id');

        // Actualizar el log de suplantación
        $log = ImpersonationLog::where('admin_id', $adminId)
            ->where('user_id', $userId)
            ->whereNull('ended_at')
            ->latest()
            ->first();

        if ($log) {
            $log->endImpersonation();
        }

        // Limpiar la sesión
        Session::forget('impersonate_admin_id');
        Session::forget('impersonate_user_id');

        // Volver a iniciar sesión como el admin
        $admin = User::findOrFail($adminId);
        Auth::login($admin);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Has dejado de suplantar al usuario');
    }

    /**
     * List impersonation logs
     */
    public function logs()
    {
        // Solo Super Admin puede ver todos los logs
        if (!auth()->user()->hasRole('Super Admin')) {
            abort(403);
        }

        $logs = ImpersonationLog::with(['admin', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.impersonation.logs', compact('logs'));
    }
}
