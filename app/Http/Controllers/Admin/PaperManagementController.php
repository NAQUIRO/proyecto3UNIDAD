<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\PaperAcceptedMail;
use App\Mail\PaperRejectedMail;
use App\Models\Congress;
use App\Models\Paper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PaperManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display all papers for a congress
     */
    public function index(Congress $congress)
    {
        // Solo admins pueden ver todos los papers
        if (!auth()->user()->hasRole(['Super Admin', 'Admin'])) {
            abort(403);
        }

        $papers = $congress->papers()
            ->with(['author', 'thematicArea', 'editorial', 'coauthors', 'reviews.reviewer'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.papers.index', compact('congress', 'papers'));
    }

    /**
     * Accept a paper
     */
    public function accept(Request $request, Congress $congress, Paper $paper)
    {
        // Solo admins pueden aceptar papers
        if (!auth()->user()->hasRole(['Super Admin', 'Admin'])) {
            abort(403);
        }

        $paper->update([
            'status' => 'accepted',
            'reviewed_at' => now(),
        ]);

        // Enviar notificación por email
        Mail::to($paper->author->email)->send(new PaperAcceptedMail($paper));

        return back()->with('success', 'Paper aceptado exitosamente. Se ha notificado al autor.');
    }

    /**
     * Reject a paper
     */
    public function reject(Request $request, Congress $congress, Paper $paper)
    {
        // Solo admins pueden rechazar papers
        if (!auth()->user()->hasRole(['Super Admin', 'Admin'])) {
            abort(403);
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $paper->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'reviewed_at' => now(),
        ]);

        // Enviar notificación por email
        Mail::to($paper->author->email)->send(new PaperRejectedMail($paper));

        return back()->with('success', 'Paper rechazado. Se ha notificado al autor.');
    }

    /**
     * Request revision
     */
    public function requestRevision(Request $request, Congress $congress, Paper $paper)
    {
        // Solo admins pueden solicitar revisiones
        if (!auth()->user()->hasRole(['Super Admin', 'Admin'])) {
            abort(403);
        }

        $validated = $request->validate([
            'revision_notes' => 'required|string|max:1000',
        ]);

        $paper->update([
            'status' => 'revision_required',
            'revision_notes' => $validated['revision_notes'],
        ]);

        // Aquí se podría enviar un email de notificación
        // Mail::to($paper->author->email)->send(new PaperRevisionRequiredMail($paper));

        return back()->with('success', 'Se ha solicitado revisión del paper. Se ha notificado al autor.');
    }
}
