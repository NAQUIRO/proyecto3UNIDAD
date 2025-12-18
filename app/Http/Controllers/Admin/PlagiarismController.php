<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Congress;
use App\Models\Paper;
use Illuminate\Http\Request;

class PlagiarismController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Mark a paper as suspected plagiarism
     */
    public function mark(Request $request, Congress $congress, Paper $paper)
    {
        // Solo admins pueden marcar plagio
        if (!auth()->user()->hasRole(['Super Admin', 'Admin'])) {
            abort(403);
        }

        $validated = $request->validate([
            'plagiarism_notes' => 'required|string|max:1000',
        ]);

        $paper->markAsPlagiarism($validated['plagiarism_notes']);

        // Aquí se podría enviar una notificación al autor
        // Mail::to($paper->author->email)->send(new PlagiarismWarningMail($paper));

        return back()->with('success', 'Paper marcado con sospecha de plagio.');
    }

    /**
     * Remove plagiarism mark
     */
    public function unmark(Congress $congress, Paper $paper)
    {
        // Solo admins pueden desmarcar plagio
        if (!auth()->user()->hasRole(['Super Admin', 'Admin'])) {
            abort(403);
        }

        $paper->update([
            'plagiarism_suspected' => false,
            'plagiarism_notes' => null,
        ]);

        return back()->with('success', 'Marca de plagio removida.');
    }
}
