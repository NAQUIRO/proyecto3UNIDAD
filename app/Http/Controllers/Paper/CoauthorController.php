<?php

namespace App\Http\Controllers\Paper;

use App\Http\Controllers\Controller;
use App\Models\Congress;
use App\Models\Paper;
use App\Models\PaperCoauthor;
use App\Models\User;
use Illuminate\Http\Request;

class CoauthorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a newly created coauthor
     */
    public function store(Request $request, Congress $congress, Paper $paper)
    {
        // Verificar que el paper pertenezca al usuario
        if ($paper->user_id !== auth()->id()) {
            abort(403);
        }

        // Verificar que no haya más de 3 coautores
        if (!$paper->canAddCoauthor()) {
            return back()->with('error', 'No puedes agregar más de 3 coautores.');
        }

        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'affiliation' => 'nullable|string|max:255',
        ]);

        // Si se proporciona user_id, verificar que existe
        if ($request->has('user_id') && $request->user_id) {
            $user = User::find($request->user_id);
            if ($user) {
                $validated['user_id'] = $user->id;
                $validated['name'] = $user->name;
                $validated['email'] = $user->email;
                $validated['is_registered_user'] = true;
            }
        } else {
            $validated['user_id'] = null;
            $validated['is_registered_user'] = false;
        }

        // Determinar el orden (siguiente número disponible)
        $maxOrder = $paper->coauthors()->max('order') ?? 0;
        $validated['order'] = $maxOrder + 1;
        $validated['paper_id'] = $paper->id;

        PaperCoauthor::create($validated);

        return back()->with('success', 'Coautor agregado exitosamente.');
    }

    /**
     * Search users for coauthors
     */
    public function search(Request $request, Congress $congress)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $users = User::where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            })
            ->where('id', '!=', auth()->id()) // No incluir al usuario actual
            ->limit(10)
            ->get(['id', 'name', 'email']);

        return response()->json($users);
    }

    /**
     * Remove the specified coauthor
     */
    public function destroy(Congress $congress, Paper $paper, PaperCoauthor $coauthor)
    {
        // Verificar que el paper pertenezca al usuario
        if ($paper->user_id !== auth()->id()) {
            abort(403);
        }

        // Verificar que el coautor pertenezca al paper
        if ($coauthor->paper_id !== $paper->id) {
            abort(403);
        }

        $coauthor->delete();

        // Reordenar los coautores restantes
        $coauthors = $paper->coauthors()->orderBy('order')->get();
        foreach ($coauthors as $index => $coauth) {
            $coauth->update(['order' => $index + 1]);
        }

        return back()->with('success', 'Coautor eliminado exitosamente.');
    }
}
