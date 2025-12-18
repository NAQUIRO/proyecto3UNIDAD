<?php

namespace App\Http\Controllers\Congress;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\Congress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BlogPostController extends Controller
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
        $posts = $congress->blogPosts()
            ->with('author')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('congress.blog.index', compact('congress', 'posts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Congress $congress)
    {
        // Solo admins pueden crear posts
        if (!auth()->user()->hasRole(['Super Admin', 'Admin'])) {
            abort(403);
        }

        return view('congress.blog.create', compact('congress'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Congress $congress)
    {
        // Solo admins pueden crear posts
        if (!auth()->user()->hasRole(['Super Admin', 'Admin'])) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published,archived',
            'is_featured' => 'boolean',
        ]);

        $validated['congress_id'] = $congress->id;
        $validated['author_id'] = auth()->id();
        $validated['is_featured'] = $request->has('is_featured');

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')
                ->store('blog/' . $congress->id, 'public');
        }

        if ($validated['status'] === 'published') {
            $validated['published_at'] = now();
        }

        BlogPost::create($validated);

        return redirect()->route('congress.blog.index', $congress)
            ->with('success', 'Post creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Congress $congress, BlogPost $post)
    {
        // Incrementar vistas
        $post->incrementViews();

        $post->load('author');

        return view('congress.blog.show', compact('congress', 'post'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Congress $congress, BlogPost $post)
    {
        // Solo admins pueden editar
        if (!auth()->user()->hasRole(['Super Admin', 'Admin'])) {
            abort(403);
        }

        return view('congress.blog.edit', compact('congress', 'post'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Congress $congress, BlogPost $post)
    {
        // Solo admins pueden editar
        if (!auth()->user()->hasRole(['Super Admin', 'Admin'])) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published,archived',
            'is_featured' => 'boolean',
        ]);

        $validated['is_featured'] = $request->has('is_featured');

        if ($request->hasFile('featured_image')) {
            // Eliminar imagen anterior
            if ($post->featured_image) {
                Storage::disk('public')->delete($post->featured_image);
            }
            $validated['featured_image'] = $request->file('featured_image')
                ->store('blog/' . $congress->id, 'public');
        }

        if ($validated['status'] === 'published' && !$post->published_at) {
            $validated['published_at'] = now();
        }

        $post->update($validated);

        return redirect()->route('congress.blog.show', [$congress, $post])
            ->with('success', 'Post actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Congress $congress, BlogPost $post)
    {
        // Solo admins pueden eliminar
        if (!auth()->user()->hasRole(['Super Admin', 'Admin'])) {
            abort(403);
        }

        if ($post->featured_image) {
            Storage::disk('public')->delete($post->featured_image);
        }

        $post->delete();

        return redirect()->route('congress.blog.index', $congress)
            ->with('success', 'Post eliminado exitosamente.');
    }
}
