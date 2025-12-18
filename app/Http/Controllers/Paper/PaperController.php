<?php

namespace App\Http\Controllers\Paper;

use App\Http\Controllers\Controller;
use App\Mail\PaperSubmittedMail;
use App\Models\Congress;
use App\Models\Editorial;
use App\Models\Paper;
use App\Models\ThematicArea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PaperController extends Controller
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
        $papers = Paper::where('congress_id', $congress->id)
            ->where('user_id', auth()->id())
            ->with(['thematicArea', 'editorial', 'coauthors', 'reviews'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('paper.index', compact('congress', 'papers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Congress $congress)
    {
        // Verificar que el usuario esté registrado como ponente en el congreso
        if (!auth()->user()->isRegisteredInCongress($congress->id) || 
            auth()->user()->getRoleInCongress($congress->id) !== 'speaker') {
            return redirect()->route('public.congresses.show', $congress->slug)
                ->with('error', 'Debes estar registrado como ponente para enviar propuestas.');
        }

        // Verificar que no haya pasado la fecha límite de recepción
        $submissionDeadline = $congress->milestones()
            ->where('type', 'paper_submission_end')
            ->where('blocks_actions', true)
            ->where('deadline', '>=', now())
            ->first();

        if (!$submissionDeadline) {
            $submissionDeadline = $congress->milestones()
                ->where('type', 'paper_submission_end')
                ->where('blocks_actions', true)
                ->first();
            
            if ($submissionDeadline && $submissionDeadline->shouldBlockActions()) {
                return redirect()->route('paper.index', $congress)
                    ->with('error', 'El período de recepción de propuestas ha finalizado.');
            }
        }

        $thematicAreas = $congress->thematicAreas()->where('is_active', true)->get();
        $editorials = Editorial::where('is_active', true)->orderBy('sort_order')->get();

        return view('paper.create', compact('congress', 'thematicAreas', 'editorials'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Congress $congress)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'abstract' => 'required|string|min:100',
            'keywords' => 'nullable|string|max:500',
            'thematic_area_id' => 'required|exists:thematic_areas,id',
            'editorial_id' => 'nullable|exists:editorials,id',
            'word_limit' => 'nullable|integer|min:100|max:5000',
            'video_url' => 'nullable|url|max:500',
        ]);

        // Calcular conteo de palabras
        $wordCount = str_word_count(strip_tags($validated['abstract']));
        $wordLimit = $validated['word_limit'] ?? 500;

        if ($wordCount > $wordLimit) {
            return back()->withErrors([
                'abstract' => "El resumen excede el límite de {$wordLimit} palabras. Actual: {$wordCount} palabras."
            ])->withInput();
        }

        $validated['congress_id'] = $congress->id;
        $validated['user_id'] = auth()->id();
        $validated['word_count'] = $wordCount;
        $validated['status'] = 'draft';

        $paper = Paper::create($validated);

        return redirect()->route('paper.show', [$congress, $paper])
            ->with('success', 'Propuesta creada exitosamente. Puedes agregar coautores y archivos antes de enviarla.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Congress $congress, Paper $paper)
    {
        // Verificar que el paper pertenezca al congreso y al usuario
        if ($paper->congress_id !== $congress->id || $paper->user_id !== auth()->id()) {
            abort(403);
        }

        $paper->load(['thematicArea', 'editorial', 'coauthors.user', 'files', 'reviews.reviewer']);

        return view('paper.show', compact('congress', 'paper'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Congress $congress, Paper $paper)
    {
        // Verificar que el paper pertenezca al congreso y al usuario
        if ($paper->congress_id !== $congress->id || $paper->user_id !== auth()->id()) {
            abort(403);
        }

        // No permitir editar si ya está en revisión o aceptado
        if (in_array($paper->status, ['under_review', 'accepted'])) {
            return redirect()->route('paper.show', [$congress, $paper])
                ->with('error', 'No puedes editar una propuesta que está en revisión o ha sido aceptada.');
        }

        $thematicAreas = $congress->thematicAreas()->where('is_active', true)->get();
        $editorials = Editorial::where('is_active', true)->orderBy('sort_order')->get();

        return view('paper.edit', compact('congress', 'paper', 'thematicAreas', 'editorials'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Congress $congress, Paper $paper)
    {
        // Verificar que el paper pertenezca al congreso y al usuario
        if ($paper->congress_id !== $congress->id || $paper->user_id !== auth()->id()) {
            abort(403);
        }

        // No permitir editar si ya está en revisión o aceptado
        if (in_array($paper->status, ['under_review', 'accepted'])) {
            return redirect()->route('paper.show', [$congress, $paper])
                ->with('error', 'No puedes editar una propuesta que está en revisión o ha sido aceptada.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'abstract' => 'required|string|min:100',
            'keywords' => 'nullable|string|max:500',
            'thematic_area_id' => 'required|exists:thematic_areas,id',
            'editorial_id' => 'nullable|exists:editorials,id',
            'word_limit' => 'nullable|integer|min:100|max:5000',
            'video_url' => 'nullable|url|max:500',
        ]);

        // Calcular conteo de palabras
        $wordCount = str_word_count(strip_tags($validated['abstract']));
        $wordLimit = $validated['word_limit'] ?? $paper->word_limit;

        if ($wordCount > $wordLimit) {
            return back()->withErrors([
                'abstract' => "El resumen excede el límite de {$wordLimit} palabras. Actual: {$wordCount} palabras."
            ])->withInput();
        }

        $validated['word_count'] = $wordCount;

        $paper->update($validated);

        return redirect()->route('paper.show', [$congress, $paper])
            ->with('success', 'Propuesta actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Congress $congress, Paper $paper)
    {
        // Verificar que el paper pertenezca al congreso y al usuario
        if ($paper->congress_id !== $congress->id || $paper->user_id !== auth()->id()) {
            abort(403);
        }

        // Solo permitir eliminar si está en borrador o rechazado
        if (!in_array($paper->status, ['draft', 'rejected'])) {
            return redirect()->route('paper.show', [$congress, $paper])
                ->with('error', 'Solo puedes eliminar propuestas en borrador o rechazadas.');
        }

        // Eliminar archivos asociados
        foreach ($paper->files as $file) {
            Storage::disk('public')->delete($file->file_path);
        }

        $paper->delete();

        return redirect()->route('paper.index', $congress)
            ->with('success', 'Propuesta eliminada exitosamente.');
    }

    /**
     * Submit the paper for review
     */
    public function submit(Request $request, Congress $congress, Paper $paper)
    {
        // Verificar que el paper pertenezca al congreso y al usuario
        if ($paper->congress_id !== $congress->id || $paper->user_id !== auth()->id()) {
            abort(403);
        }

        // Validar que tenga los datos mínimos
        if (empty($paper->abstract) || empty($paper->title)) {
            return back()->with('error', 'La propuesta debe tener título y resumen antes de ser enviada.');
        }

        // Verificar que no haya pasado la fecha límite
        $submissionDeadline = $congress->milestones()
            ->where('type', 'paper_submission_end')
            ->where('blocks_actions', true)
            ->first();

        if ($submissionDeadline && $submissionDeadline->shouldBlockActions()) {
            return back()->with('error', 'El período de recepción de propuestas ha finalizado.');
        }

        $paper->submit();

        // Enviar notificación por email
        Mail::to($paper->author->email)->send(new PaperSubmittedMail($paper));

        return redirect()->route('paper.show', [$congress, $paper])
            ->with('success', 'Propuesta enviada exitosamente. Será revisada por el comité científico.');
    }
}
