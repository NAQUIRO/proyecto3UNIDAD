<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookOfAbstracts;
use App\Models\Congress;
use App\Models\Paper;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookOfAbstractsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display book of abstracts for a congress
     */
    public function index(Congress $congress)
    {
        // Solo admins pueden ver
        if (!auth()->user()->hasRole(['Super Admin', 'Admin'])) {
            abort(403);
        }

        $book = BookOfAbstracts::where('congress_id', $congress->id)
            ->orderBy('created_at', 'desc')
            ->first();

        $papers = $congress->papers()
            ->where('status', 'accepted')
            ->with(['author', 'thematicArea'])
            ->get();

        return view('admin.book-of-abstracts.index', compact('congress', 'book', 'papers'));
    }

    /**
     * Generate book of abstracts
     */
    public function generate(Request $request, Congress $congress)
    {
        // Solo admins pueden generar
        if (!auth()->user()->hasRole(['Super Admin', 'Admin'])) {
            abort(403);
        }

        $validated = $request->validate([
            'paper_ids' => 'nullable|array',
            'paper_ids.*' => 'exists:papers,id',
            'include_cover' => 'boolean',
            'cover_image' => 'nullable|image|max:2048',
        ]);

        // Obtener papers a incluir
        $query = $congress->papers()->where('status', 'accepted');

        if (!empty($validated['paper_ids'])) {
            $query->whereIn('id', $validated['paper_ids']);
        }

        $papers = $query->with(['author', 'thematicArea', 'coauthors'])->get();

        if ($papers->isEmpty()) {
            return back()->with('error', 'No hay papers aceptados para incluir en el libro.');
        }

        // Crear registro del libro
        $book = BookOfAbstracts::create([
            'congress_id' => $congress->id,
            'created_by' => auth()->id(),
            'title' => "Libro de Resúmenes - {$congress->title}",
            'status' => 'generating',
            'total_papers' => $papers->count(),
            'included_papers' => $papers->pluck('id')->toArray(),
        ]);

        // Generar PDF
        $this->generateBookPdf($book, $papers, $congress, $validated);

        return redirect()->route('admin.book-of-abstracts.show', [$congress, $book])
            ->with('success', 'Libro de resúmenes generado exitosamente.');
    }

    /**
     * Display generated book
     */
    public function show(Congress $congress, BookOfAbstracts $book)
    {
        // Solo admins pueden ver
        if (!auth()->user()->hasRole(['Super Admin', 'Admin'])) {
            abort(403);
        }

        $papers = Paper::whereIn('id', $book->included_papers ?? [])
            ->with(['author', 'thematicArea', 'coauthors'])
            ->get();

        return view('admin.book-of-abstracts.show', compact('congress', 'book', 'papers'));
    }

    /**
     * Download book PDF
     */
    public function download(Congress $congress, BookOfAbstracts $book)
    {
        // Solo admins pueden descargar
        if (!auth()->user()->hasRole(['Super Admin', 'Admin'])) {
            abort(403);
        }

        if (!$book->pdf_path || !Storage::disk('public')->exists($book->pdf_path)) {
            return back()->with('error', 'El archivo PDF no existe. Por favor, regenera el libro.');
        }

        return Storage::disk('public')->download($book->pdf_path, "libro_resumenes_{$congress->slug}.pdf");
    }

    /**
     * Generate book PDF
     */
    private function generateBookPdf(BookOfAbstracts $book, $papers, Congress $congress, array $options): void
    {
        $html = view('admin.book-of-abstracts.pdf', [
            'book' => $book,
            'papers' => $papers,
            'congress' => $congress,
        ])->render();

        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper('a4', 'portrait');

        $filename = 'books-of-abstracts/' . $congress->id . '/' . now()->format('Y-m-d_His') . '.pdf';
        Storage::disk('public')->put($filename, $pdf->output());

        $book->update([
            'pdf_path' => $filename,
            'status' => 'completed',
            'generated_at' => now(),
        ]);
    }
}
