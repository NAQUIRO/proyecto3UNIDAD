<?php

namespace App\Http\Controllers\Paper;

use App\Http\Controllers\Controller;
use App\Models\Congress;
use App\Models\Paper;
use App\Models\PaperFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PaperFileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a newly created file
     */
    public function store(Request $request, Congress $congress, Paper $paper)
    {
        // Verificar que el paper pertenezca al usuario
        if ($paper->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'file' => 'required|file|mimes:doc,docx,pdf|max:10240', // Máximo 10MB
            'file_type' => 'required|in:abstract,full_paper,presentation,other',
            'notes' => 'nullable|string|max:500',
        ]);

        $file = $request->file('file');
        $fileName = time() . '_' . Str::slug($file->getClientOriginalName());
        $filePath = $file->storeAs('papers/' . $paper->id, $fileName, 'public');

        // Determinar versión
        $latestVersion = $paper->files()
            ->where('file_type', $validated['file_type'])
            ->max('version_number') ?? 0;

        PaperFile::create([
            'paper_id' => $paper->id,
            'file_path' => $filePath,
            'file_name' => $file->getClientOriginalName(),
            'file_type' => $validated['file_type'],
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'version' => $latestVersion > 0 ? 'revised' : 'draft',
            'version_number' => $latestVersion + 1,
            'notes' => $validated['notes'] ?? null,
        ]);

        return back()->with('success', 'Archivo subido exitosamente.');
    }

    /**
     * Remove the specified file
     */
    public function destroy(Congress $congress, Paper $paper, PaperFile $file)
    {
        // Verificar que el paper pertenezca al usuario
        if ($paper->user_id !== auth()->id()) {
            abort(403);
        }

        // Verificar que el archivo pertenezca al paper
        if ($file->paper_id !== $paper->id) {
            abort(403);
        }

        // Eliminar archivo físico
        Storage::disk('public')->delete($file->file_path);

        $file->delete();

        return back()->with('success', 'Archivo eliminado exitosamente.');
    }

    /**
     * Download the file
     */
    public function download(Congress $congress, Paper $paper, PaperFile $file)
    {
        // Verificar permisos (autor o revisor del paper)
        $canAccess = false;
        
        if ($paper->user_id === auth()->id()) {
            $canAccess = true;
        } elseif (auth()->user()->isReviewerForCongress($congress->id)) {
            $canAccess = $paper->reviews()
                ->where('reviewer_id', auth()->id())
                ->exists();
        }

        if (!$canAccess) {
            abort(403);
        }

        if (!Storage::disk('public')->exists($file->file_path)) {
            abort(404);
        }

        return Storage::disk('public')->download($file->file_path, $file->file_name);
    }
}
