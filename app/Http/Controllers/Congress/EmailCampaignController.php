<?php

namespace App\Http\Controllers\Congress;

use App\Http\Controllers\Controller;
use App\Models\Congress;
use App\Models\EmailCampaign;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailCampaignController extends Controller
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
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        $validated['congress_id'] = $congress->id;
        $validated['created_by'] = auth()->id();
        $validated['status'] = $request->scheduled_at ? 'scheduled' : 'draft';

        $campaign = EmailCampaign::create($validated);

        // Preparar destinatarios
        $recipients = $this->getRecipients($congress, $validated['segment_type']);

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
            return redirect()->route('congress.email-campaigns.send', [$congress, $campaign])
                ->with('success', 'Campaña creada. ¿Deseas enviarla ahora?');
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
     * Send campaign
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

        $campaign->update(['status' => 'sending']);

        $sentCount = 0;
        $failedCount = 0;

        foreach ($campaign->recipients()->where('status', 'pending')->get() as $recipient) {
            try {
                Mail::send([], [], function ($message) use ($campaign, $recipient) {
                    $message->to($recipient->email, $recipient->name)
                        ->subject($campaign->subject)
                        ->html($campaign->content);
                });

                $recipient->markAsSent();
                $sentCount++;
            } catch (\Exception $e) {
                $recipient->markAsFailed($e->getMessage());
                $failedCount++;
            }
        }

        $campaign->update([
            'status' => 'sent',
            'sent_count' => $sentCount,
            'failed_count' => $failedCount,
            'sent_at' => now(),
        ]);

        return redirect()->route('congress.email-campaigns.show', [$congress, $campaign])
            ->with('success', "Campaña enviada. {$sentCount} exitosos, {$failedCount} fallidos.");
    }

    /**
     * Get recipients based on segment type
     */
    private function getRecipients(Congress $congress, string $segmentType): \Illuminate\Database\Eloquent\Collection
    {
        return match ($segmentType) {
            'all' => $congress->users()->get(),
            'attendees' => $congress->users()->wherePivot('role', 'attendee')->get(),
            'speakers' => $congress->users()->wherePivot('role', 'speaker')->get(),
            'accepted_speakers' => User::whereHas('papers', function($q) use ($congress) {
                $q->where('congress_id', $congress->id)
                  ->where('status', 'accepted');
            })->get(),
            'reviewers' => $congress->reviewers()->get(),
            default => collect(),
        };
    }
}
