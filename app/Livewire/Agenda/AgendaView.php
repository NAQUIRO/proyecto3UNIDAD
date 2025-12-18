<?php

namespace App\Livewire\Agenda;

use App\Models\Congress;
use App\Models\VirtualSession;
use Livewire\Component;
use Livewire\WithPagination;

class AgendaView extends Component
{
    use WithPagination;

    public Congress $congress;
    public ?string $filterDate = null;
    public ?string $filterSymposium = null;
    public string $viewMode = 'list'; // list, calendar, grid

    protected $queryString = [
        'filterDate' => ['except' => ''],
        'filterSymposium' => ['except' => ''],
    ];

    public function mount(Congress $congress)
    {
        $this->congress = $congress;
    }

    public function setViewMode(string $mode): void
    {
        $this->viewMode = $mode;
        $this->resetPage();
    }

    public function setFilterDate(?string $date): void
    {
        $this->filterDate = $date;
        $this->resetPage();
    }

    public function setFilterSymposium(?string $symposiumId): void
    {
        $this->filterSymposium = $symposiumId;
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->filterDate = null;
        $this->filterSymposium = null;
        $this->resetPage();
    }

    public function render()
    {
        $query = VirtualSession::where('congress_id', $this->congress->id)
            ->with(['symposium', 'paper.author']);

        // Filtrar por fecha
        if ($this->filterDate) {
            $query->whereDate('scheduled_at', $this->filterDate);
        }

        // Filtrar por simposio
        if ($this->filterSymposium) {
            $query->where('symposium_id', $this->filterSymposium);
        }

        // Ordenar por fecha programada
        $sessions = $query->orderBy('scheduled_at', 'asc')
            ->paginate(20);

        // Agrupar por fecha para vista de calendario
        $sessionsByDate = $this->viewMode === 'calendar' 
            ? $sessions->groupBy(function ($session) {
                return $session->scheduled_at ? $session->scheduled_at->format('Y-m-d') : 'sin-fecha';
            })
            : collect();

        // Obtener simposios para filtro
        $symposia = $this->congress->symposia()
            ->where('is_active', true)
            ->withCount('sessions')
            ->get();

        // Obtener fechas disponibles
        $availableDates = VirtualSession::where('congress_id', $this->congress->id)
            ->whereNotNull('scheduled_at')
            ->selectRaw('DATE(scheduled_at) as date')
            ->distinct()
            ->orderBy('date', 'asc')
            ->pluck('date');

        return view('livewire.agenda.agenda-view', [
            'sessions' => $sessions,
            'sessionsByDate' => $sessionsByDate,
            'symposia' => $symposia,
            'availableDates' => $availableDates,
        ]);
    }
}
