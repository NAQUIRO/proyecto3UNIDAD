<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:import {source} {--type=logo} {--congress=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa imágenes desde una carpeta local al storage del proyecto';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $source = $this->argument('source');
        $type = $this->option('type'); // logo o banner
        $congressId = $this->option('congress');

        if (!file_exists($source)) {
            $this->error("La ruta fuente no existe: {$source}");
            return 1;
        }

        if (!is_dir($source)) {
            // Es un archivo individual
            $this->importFile($source, $type, $congressId);
        } else {
            // Es un directorio
            $files = glob($source . '/*.{jpg,jpeg,png,gif,svg}', GLOB_BRACE);
            $this->info("Encontradas " . count($files) . " imágenes para importar.");
            
            foreach ($files as $file) {
                $this->importFile($file, $type, $congressId);
            }
        }

        $this->info("¡Importación completada!");
        return 0;
    }

    private function importFile($filePath, $type, $congressId = null)
    {
        $fileName = basename($filePath);
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        $name = pathinfo($fileName, PATHINFO_FILENAME);
        
        // Generar nombre único
        $uniqueName = Str::slug($name) . '_' . time() . '.' . $extension;
        
        // Determinar ruta de destino
        if ($congressId) {
            $destination = "congresses/{$type}s/{$congressId}/{$uniqueName}";
        } else {
            $destination = "congresses/{$type}s/{$uniqueName}";
        }

        // Copiar archivo
        $content = file_get_contents($filePath);
        Storage::disk('public')->put($destination, $content);

        $this->info("✓ Importado: {$fileName} → storage/app/public/{$destination}");
        
        return $destination;
    }
}
