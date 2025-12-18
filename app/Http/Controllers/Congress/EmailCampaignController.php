<?php

namespace App\Http\Controllers\Congress;

use App\Http\Controllers\Controller;
use App\Services\BulkEmailService;
use App\Models\Congress;
use App\Models\EmailCampaign;
use Illuminate\Http\Request;

class EmailCampaignController extends Controller
{
    protected BulkEmailService $bulkEmailService;

    public function __construct(BulkEmailService $bulkEmailService)
    {
        $this->middleware('auth');
        $this->bulkEmailService = $bulkEmailService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Congress $congress)
    {
        // Solo admins pueden ver campañas
        if (!auth()->user()->hasRole(['Super Admin', 'Admin'])) {
            abort(403);
        }

        $campaigns = $congress->emailCampaigns()
            ->with('creator')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('congress.email-campaigns.index', compact('congress', 'campaigns'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Congress $congress)
    {
        // Solo admins pueden crear campañas
        if (!auth()->user()->hasRole(['Super Admin', 'Admin'])) {
            abort(403);
        }

        return view('congress.email-campaigns.create', compact('congress'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Congress $congress)
    {
        // Solo admins pueden crear campañas
        if (!auth()->user()->hasRole(['Super Admin', 'Admin'])) {
            abort(403);
        }

        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'segment_type' => 'required|in:all,attendees,speakers,accepted_speakers,reviewers,custom',
            'segment_filters' => 'nullable|array',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        $validated['congress_id'] = $congress->id;
        $validated['created_by'] = auth()->id();
        $validated['status'] = $request->scheduled_at ? 'scheduled' : 'draft';

        $campaign = EmailCampaign::create($validated);

        // Preparar destinatarios usando el servicio
        $recipients = $this->bulkEmailService->prepareRecipients(
            $congress,
            $validated['segment_type'],
            $validated['segment_filters'] ?? null
        );

        // Crear registros de destinatarios
        foreach ($recipients as $recipient) {
            $campaign->recipients()->create([
                'user_id' => $recipient->id,
                'email' => $recipient->email,
                'name' => $recipient->name,
                'status' => 'pending',
            ]);
        }

        $campaign->update(['total_recipients' => count($recipients)]);

        if (!$request->scheduled_at) {
            return redirect()->route('congress.email-campaigns.show', [$congress, $campaign])
                ->with('success', 'Campaña creada. Puedes enviarla desde aquí.');
        }

        return redirect()->route('congress.email-campaigns.show', [$congress, $campaign])
            ->with('success', 'Campaña programada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Congress $congress, EmailCampaign $campaign)
    {
        // Solo admins pueden ver campañas
        if (!auth()->user()->hasRole(['Super Admin', 'Admin'])) {
            abort(403);
        }

        $campaign->load(['recipients.user']);

        return view('congress.email-campaigns.show', compact('congress', 'campaign'));
    }

    /**
     * Send campaign (usando jobs con chunks)
     */
    public function send(Congress $congress, EmailCampaign $campaign)
    {
        // Solo admins pueden enviar
        if (!auth()->user()->hasRole(['Super Admin', 'Admin'])) {
            abort(403);
        }

        if ($campaign->isSent()) {
            return back()->with('error', 'Esta campaña ya fue enviada.');
        }

        // Enviar usando el servicio (procesa en chunks y encola jobs)
        $this->bulkEmailService->sendCampaign($campaign, 50);

        return redirect()->route('congress.email-campaigns.show', [$congress, $campaign])
            ->with('success', 'Campaña en proceso de envío. Los correos se enviarán de forma asíncrona.');
    }

    /**
     * Actualizar estadísticas de la campaña
     */
    public function updateStats(Congress $congress, EmailCampaign $campaign)
    {
        $this->bulkEmailService->updateCampaignStats($campaign);

        return response()->json([
            'sent_count' => $campaign->sent_count,
            'failed_count' => $campaign->failed_count,
            'status' => $campaign->status,
        ]);
    }
}
