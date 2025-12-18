<?php

namespace App\Http\Controllers;

use App\Models\Congress;
use App\Models\Paper;
use App\Models\Symposium;
use App\Models\VirtualSession;
use App\Notifications\CommentOnPaperNotification;
use Illuminate\Http\Request;

class VirtualSessionController extends Controller
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
        $sessions = VirtualSession::where('congress_id', $congress->id)
            ->with(['symposium', 'paper.author'])
            ->orderBy('scheduled_at', 'asc')
            ->paginate(20);

        $symposia = $congress->symposia()
            ->where('is_active', true)
            ->withCount('sessions')
            ->get();

        return view('virtual-sessions.index', compact('congress', 'sessions', 'symposia'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Congress $congress)
    {
        // Solo admins pueden crear sesiones
        if (!auth()->user()->hasRole(['Super Admin', 'Admin'])) {
            abort(403);
        }

        $symposia = $congress->symposia()->where('is_active', true)->get();
        $papers = $congress->papers()
            ->where('status', 'accepted')
            ->with('author')
            ->get();

        return view('virtual-sessions.create', compact('congress', 'symposia', 'papers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Congress $congress)
    {
        // Solo admins pueden crear sesiones
        if (!auth()->user()->hasRole(['Super Admin', 'Admin'])) {
            abort(403);
        }

        $validated = $request->validate([
            'symposium_id' => 'nullable|exists:symposia,id',
            'paper_id' => 'nullable|exists:papers,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_url' => 'required|url|max:500',
            'video_provider' => 'required|in:youtube,vimeo,custom',
            'video_id' => 'nullable|string|max:100',
            'duration_minutes' => 'nullable|integer|min:1',
            'scheduled_at' => 'nullable|date',
            'is_recorded' => 'boolean',
        ]);

        // Extraer video_id de la URL si es YouTube o Vimeo
        if (empty($validated['video_id']) && in_array($validated['video_provider'], ['youtube', 'vimeo'])) {
            $validated['video_id'] = $this->extractVideoId($validated['video_url'], $validated['video_provider']);
        }

        $validated['congress_id'] = $congress->id;
        $validated['status'] = 'scheduled';
        $validated['is_recorded'] = $request->has('is_recorded');

        VirtualSession::create($validated);

        return redirect()->route('virtual-sessions.index', $congress)
            ->with('success', 'Sesión virtual creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Congress $congress, VirtualSession $session)
    {
        // Incrementar vistas
        $session->incrementViews();

        $session->load([
            'symposium',
            'paper.author',
            'comments.user',
            'comments.replies.user',
            'questions.user'
        ]);

        return view('virtual-sessions.show', compact('congress', 'session'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Congress $congress, VirtualSession $session)
    {
        // Solo admins pueden editar
        if (!auth()->user()->hasRole(['Super Admin', 'Admin'])) {
            abort(403);
        }

        $symposia = $congress->symposia()->where('is_active', true)->get();
        $papers = $congress->papers()
            ->where('status', 'accepted')
            ->with('author')
            ->get();

        return view('virtual-sessions.edit', compact('congress', 'session', 'symposia', 'papers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Congress $congress, VirtualSession $session)
    {
        // Solo admins pueden editar
        if (!auth()->user()->hasRole(['Super Admin', 'Admin'])) {
            abort(403);
        }

        $validated = $request->validate([
            'symposium_id' => 'nullable|exists:symposia,id',
            'paper_id' => 'nullable|exists:papers,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_url' => 'required|url|max:500',
            'video_provider' => 'required|in:youtube,vimeo,custom',
            'video_id' => 'nullable|string|max:100',
            'duration_minutes' => 'nullable|integer|min:1',
            'scheduled_at' => 'nullable|date',
            'is_recorded' => 'boolean',
        ]);

        // Extraer video_id si es necesario
        if (empty($validated['video_id']) && in_array($validated['video_provider'], ['youtube', 'vimeo'])) {
            $validated['video_id'] = $this->extractVideoId($validated['video_url'], $validated['video_provider']);
        }

        $validated['is_recorded'] = $request->has('is_recorded');

        $session->update($validated);

        return redirect()->route('virtual-sessions.show', [$congress, $session])
            ->with('success', 'Sesión virtual actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Congress $congress, VirtualSession $session)
    {
        // Solo admins pueden eliminar
        if (!auth()->user()->hasRole(['Super Admin', 'Admin'])) {
            abort(403);
        }

        $session->delete();

        return redirect()->route('virtual-sessions.index', $congress)
            ->with('success', 'Sesión virtual eliminada exitosamente.');
    }

    /**
     * Start a live session
     */
    public function start(Congress $congress, VirtualSession $session)
    {
        // Solo admins pueden iniciar sesiones
        if (!auth()->user()->hasRole(['Super Admin', 'Admin'])) {
            abort(403);
        }

        $session->start();

        return back()->with('success', 'Sesión iniciada.');
    }

    /**
     * End a live session
     */
    public function end(Congress $congress, VirtualSession $session)
    {
        // Solo admins pueden finalizar sesiones
        if (!auth()->user()->hasRole(['Super Admin', 'Admin'])) {
            abort(403);
        }

        $session->end();

        return back()->with('success', 'Sesión finalizada.');
    }

    /**
     * Extract video ID from URL
     */
    private function extractVideoId(string $url, string $provider): ?string
    {
        return match ($provider) {
            'youtube' => $this->extractYouTubeId($url),
            'vimeo' => $this->extractVimeoId($url),
            default => null,
        };
    }

    private function extractYouTubeId(string $url): ?string
    {
        preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $matches);
        return $matches[1] ?? null;
    }

    private function extractVimeoId(string $url): ?string
    {
        preg_match('/vimeo\.com\/(?:.*\/)?(\d+)/', $url, $matches);
        return $matches[1] ?? null;
    }
}
