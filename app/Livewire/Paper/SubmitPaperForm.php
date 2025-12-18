<?php

namespace App\Livewire\Paper;

use App\Models\Congress;
use App\Models\Editorial;
use App\Models\Paper;
use App\Models\ThematicArea;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SubmitPaperForm extends Component
{
    use WithFileUploads;

    public Congress $congress;
    public ?Paper $paper = null;

    // Form fields
    public $title = '';
    public $abstract = '';
    public $keywords = '';
    public $thematic_area_id = null;
    public $editorial_id = null;
    public $word_limit = 500;
    public $video_url = '';

    // File uploads
    public $abstract_file = null;
    public $full_paper_file = null;
    public $presentation_file = null;

    // Computed properties
    public $wordCount = 0;
    public $thematicAreas = [];
    public $editorials = [];

    protected $rules = [
        'title' => 'required|string|max:255',
        'abstract' => 'required|string|min:100',
        'keywords' => 'nullable|string|max:500',
        'thematic_area_id' => 'required|exists:thematic_areas,id',
        'editorial_id' => 'nullable|exists:editorials,id',
        'word_limit' => 'nullable|integer|min:100|max:5000',
        'video_url' => 'nullable|url|max:500',
        'abstract_file' => 'nullable|file|mimes:pdf,doc,docx|max:10240', // 10MB
        'full_paper_file' => 'nullable|file|mimes:pdf,doc,docx|max:51200', // 50MB
        'presentation_file' => 'nullable|file|mimes:pdf,ppt,pptx|max:51200', // 50MB
    ];

    public function mount(Congress $congress, ?Paper $paper = null)
    {
        $this->congress = $congress;
        $this->paper = $paper;

        // Cargar áreas temáticas y editoriales
        $this->thematicAreas = $congress->thematicAreas()
            ->where('is_active', true)
            ->get()
            ->toArray();
        
        $this->editorials = Editorial::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->toArray();

        // Si es edición, cargar datos existentes
        if ($paper) {
            $this->title = $paper->title;
            $this->abstract = $paper->abstract;
            $this->keywords = $paper->keywords;
            $this->thematic_area_id = $paper->thematic_area_id;
            $this->editorial_id = $paper->editorial_id;
            $this->word_limit = $paper->word_limit ?? 500;
            $this->video_url = $paper->video_url;
            $this->wordCount = $paper->word_count;
        }
    }

    public function updatedAbstract()
    {
        $this->wordCount = str_word_count(strip_tags($this->abstract));
        $this->validateOnly('abstract');
    }

    public function save()
    {
        $this->validate();

        // Validar límite de palabras
        if ($this->wordCount > $this->word_limit) {
            $this->addError('abstract', "El resumen excede el límite de {$this->word_limit} palabras. Actual: {$this->wordCount} palabras.");
            return;
        }

        $data = [
            'congress_id' => $this->congress->id,
            'user_id' => auth()->id(),
            'title' => $this->title,
            'abstract' => $this->abstract,
            'keywords' => $this->keywords,
            'thematic_area_id' => $this->thematic_area_id,
            'editorial_id' => $this->editorial_id,
            'word_limit' => $this->word_limit,
            'word_count' => $this->wordCount,
            'video_url' => $this->video_url,
            'status' => 'draft',
        ];

        if ($this->paper) {
            $this->paper->update($data);
            $paper = $this->paper;
        } else {
            $paper = Paper::create($data);
        }

        // Subir archivos
        $this->uploadFiles($paper);

        session()->flash('success', $this->paper ? 'Propuesta actualizada exitosamente.' : 'Propuesta creada exitosamente.');

        return redirect()->route('paper.show', [$this->congress, $paper]);
    }

    protected function uploadFiles(Paper $paper)
    {
        $files = [
            'abstract_file' => 'abstract',
            'full_paper_file' => 'full_paper',
            'presentation_file' => 'presentation',
        ];

        foreach ($files as $property => $fileType) {
            if ($this->$property) {
                $file = $this->$property;
                $fileName = Str::slug($paper->title) . '_' . $fileType . '_' . time() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('papers/' . $paper->id, $fileName, 'public');

                $paper->files()->create([
                    'file_path' => $filePath,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $fileType,
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'version' => 'draft',
                    'version_number' => 1,
                ]);
            }
        }
    }

    public function render()
    {
        return view('livewire.paper.submit-paper-form');
    }
}
