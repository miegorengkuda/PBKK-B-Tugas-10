@extends('adminlte::page')

@section('title', 'Kotak Sampah Proyek (Trash)')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1 class="m-0 text-dark font-weight-bold">Kotak Sampah (Trash)</h1>
            <p class="text-muted mb-0 small">Daftar proyek yang dihapus secara lunak (Soft Delete). Anda dapat memulihkannya kapan saja.</p>
        </div>
        <div>
            <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary btn-sm shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Proyek Aktif
            </a>
        </div>
    </div>
@stop

@section('content')

    {{-- Flash Notifications --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($trashedProjects->isEmpty())
        <div class="card card-outline card-secondary shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-trash3 display-4 text-muted mb-3 d-block"></i>
                <h5 class="fw-bold text-dark">Kotak Sampah Kosong</h5>
                <p class="text-muted small mb-0">Tidak ada proyek yang sedang dihapus sementara.</p>
            </div>
        </div>
    @else
        <div class="card card-outline card-danger shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead class="table-light text-uppercase small text-muted">
                            <tr>
                                <th class="ps-3">Judul Proyek</th>
                                <th>Kategori</th>
                                <th>Dihapus Pada</th>
                                <th class="text-end pe-3">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($trashedProjects as $project)
                                <tr>
                                    <td class="ps-3">
                                        <span class="fw-bold text-muted text-decoration-line-through">
                                            {{ $project->title }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge text-bg-light border">
                                            {{ $project->category ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="small text-muted text-nowrap">
                                        <i class="bi bi-calendar3 me-1"></i>{{ $project->deleted_at->format('d M Y, H:i') }}
                                    </td>
                                    <td class="text-end pe-3 text-nowrap">
                                        <form method="POST" action="{{ route('projects.restore', $project->id) }}" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-success btn-sm shadow-sm">
                                                <i class="bi bi-arrow-counterclockwise me-1"></i> Pulihkan (Restore)
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            @if($trashedProjects->hasPages())
                <div class="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="text-muted small">
                        Menampilkan {{ $trashedProjects->firstItem() }} sampai {{ $trashedProjects->lastItem() }} dari {{ $trashedProjects->total() }} proyek terhapus
                    </div>
                    <div>
                        {{ $trashedProjects->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @endif
        </div>
    @endif

@stop
