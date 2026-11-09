<?php

namespace App\Http\Controllers;

use App\Http\Requests\BulkDestroyProjectRequest;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ProjectController extends Controller
{
    /**
     * Tampilkan daftar proyek milik user yang sedang login.
     */
    public function index(Request $request)
    {
        $projects = $request->user()->projects()
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $trashedCount = $request->user()->projects()
            ->onlyTrashed()
            ->count();

        return view('projects.index', compact('projects', 'trashedCount'));
    }

    /**
     * Tampilkan form untuk membuat proyek baru.
     */
    public function create()
    {
        return view('projects.create');
    }

    /**
     * Simpan proyek baru ke database.
     * Menggunakan StoreProjectRequest dan DB::transaction.
     */
    public function store(StoreProjectRequest $request)
    {
        $project = DB::transaction(function () use ($request) {
            return $request->user()->projects()->create($request->validated());
        });

        return redirect()->route('projects.index')
            ->with('success', 'Proyek "' . $project->title . '" berhasil ditambahkan!');
    }

    /**
     * Tampilkan detail proyek.
     * Otorisasi kepemilikan diperiksa melalui Policy.
     */
    public function show(Project $project)
    {
        Gate::authorize('view', $project);

        return view('projects.show', compact('project'));
    }

    /**
     * Tampilkan form edit proyek.
     * Otorisasi kepemilikan diperiksa melalui Policy.
     */
    public function edit(Project $project)
    {
        Gate::authorize('update', $project);

        return view('projects.edit', compact('project'));
    }

    /**
     * Perbarui data proyek.
     * Otorisasi dan validasi melalui UpdateProjectRequest serta DB::transaction.
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        Gate::authorize('update', $project);

        DB::transaction(function () use ($request, $project) {
            $project->update($request->validated());
        });

        return redirect()->route('projects.show', $project)
            ->with('success', 'Proyek "' . $project->title . '" berhasil diperbarui!');
    }

    /**
     * Hapus proyek secara lunak (Soft Delete).
     * Otorisasi diperiksa melalui Policy.
     */
    public function destroy(Project $project)
    {
        Gate::authorize('delete', $project);

        $title = $project->title;
        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Proyek "' . $title . '" berhasil dipindahkan ke kotak sampah (Soft Delete).');
    }

    /**
     * Tampilkan daftar proyek yang terhapus secara lunak (Trash) milik user.
     */
    public function trash(Request $request)
    {
        $trashedProjects = $request->user()->projects()
            ->onlyTrashed()
            ->latest('deleted_at')
            ->paginate(10)
            ->withQueryString();

        return view('projects.trash', compact('trashedProjects'));
    }

    /**
     * Pulihkan (Restore) proyek yang telah dihapus lunak.
     * Query dibatasi ketat pada proyek user yang sedang login + pengecekan Policy.
     */
    public function restore(Request $request, int|string $id)
    {
        $project = $request->user()->projects()->onlyTrashed()->findOrFail($id);

        Gate::authorize('restore', $project);

        $project->restore();

        return redirect()->route('projects.trash')
            ->with('success', 'Proyek "' . $project->title . '" berhasil dipulihkan!');
    }

    /**
     * Hapus banyak proyek terpilih secara massal (Bulk Soft Delete).
     * Validasi melalui BulkDestroyProjectRequest dan eksekusi atomik dalam DB::transaction.
     */
    public function bulkDestroy(BulkDestroyProjectRequest $request)
    {
        $count = DB::transaction(function () use ($request) {
            return $request->user()->projects()
                ->whereIn('id', $request->validated('ids'))
                ->delete();
        });

        return redirect()->route('projects.index')
            ->with('success', $count . ' proyek terpilih berhasil dipindahkan ke kotak sampah.');
    }
}
