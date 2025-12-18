<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Congress;
use App\Models\Editorial;
use App\Models\EditorialDownload;
use App\Models\Paper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class EditorialDownloadController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Generate ZIP file for editorial
     */
    public function generate(Congress $congress, Editorial $editorial)
    {
        // Solo admins pueden generar descargas
        if (!auth()->user()->hasRole(['Super Admin', 'Admin'])) {
            abort(403);
        }

        // Obtener papers de esta editorial
        $papers = $congress->papers()
            ->where('editorial_id', $editorial->id)
            ->where('status', 'accepted')
            ->with('files')
            ->get();

        if ($papers->isEmpty()) {
            return back()->with('error', 'No hay papers aceptados para esta editorial.');
        }

        // Crear registro de descarga
        $download = EditorialDownload::create([
            'congress_id' => $congress->id,
            'editorial_id' => $editorial->id,
            'downloaded_by' => auth()->id(),
            'status' => 'generating',
        ]);

        // Generar ZIP
        $this->generateZip($download, $papers, $congress, $editorial);

        return redirect()->route('admin.editorial-downloads.show', [$congress, $editorial, $download])
            ->with('success', 'Archivo ZIP generado exitosamente.');
    }

    /**
     * Display download information
     */
    public function show(Congress $congress, Editorial $editorial, EditorialDownload $download)
    {
        // Solo admins pueden ver
        if (!auth()->user()->hasRole(['Super Admin', 'Admin'])) {
            abort(403);
        }

        return view('admin.editorial-downloads.show', compact('congress', 'editorial', 'download'));
    }

    /**
     * Download ZIP file
     */
    public function download(Congress $congress, Editorial $editorial, EditorialDownload $download)
    {
        // Solo admins pueden descargar
        if (!auth()->user()->hasRole(['Super Admin', 'Admin'])) {
            abort(403);
        }

        if (!$download->zip_path || !Storage::disk('public')->exists($download->zip_path)) {
            return back()->with('error', 'El archivo ZIP no existe. Por favor, regenera el archivo.');
        }

        if ($download->isExpired()) {
            return back()->with('error', 'El enlace de descarga ha expirado. Por favor, genera uno nuevo.');
        }

        $download->markAsDownloaded();

        return Storage::disk('public')->download($download->zip_path, "papers_{$editorial->name}_{$congress->slug}.zip");
    }

    /**
     * Generate ZIP file with papers
     */
    private function generateZip(EditorialDownload $download, $papers, Congress $congress, Editorial $editorial): void
    {
        $zip = new ZipArchive();
        $zipFilename = 'editorial-downloads/' . $congress->id . '/' . $editorial->id . '_' . now()->format('Y-m-d_His') . '.zip';
        $fullPath = storage_path('app/public/' . $zipFilename);

        // Crear directorio si no existe
        $directory = dirname($fullPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        if ($zip->open($fullPath, ZipArchive::CREATE) !== TRUE) {
            throw new \Exception('No se pudo crear el archivo ZIP');
        }

        $filesCount = 0;
        $totalSize = 0;

        foreach ($papers as $paper) {
            // Agregar archivos del paper
            foreach ($paper->files as $file) {
                $filePath = storage_path('app/public/' . $file->file_path);
                if (file_exists($filePath)) {
                    $zip->addFile($filePath, "Paper_{$paper->id}/{$file->file_name}");
                    $filesCount++;
                    $totalSize += filesize($filePath);
                }
            }

            // Agregar información del paper como texto
            $paperInfo = "Paper ID: {$paper->id}\n";
            $paperInfo .= "Título: {$paper->title}\n";
            $paperInfo .= "Autor: {$paper->author->name}\n";
            $paperInfo .= "Resumen: {$paper->abstract}\n";
            $zip->addFromString("Paper_{$paper->id}/info.txt", $paperInfo);
        }

        $zip->close();

        // Guardar en storage público
        Storage::disk('public')->put($zipFilename, file_get_contents($fullPath));
        unlink($fullPath); // Eliminar archivo temporal

        $download->update([
            'zip_path' => $zipFilename,
            'files_count' => $filesCount,
            'total_size' => $totalSize,
            'status' => 'ready',
            'generated_at' => now(),
            'expires_at' => now()->addDays(7),
        ]);
    }
}
