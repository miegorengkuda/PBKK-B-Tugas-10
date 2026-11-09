@extends('adminlte::page')

@section('title', 'Detail Proyek — ' . $project->title)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <h1 class="m-0 text-dark font-weight-bold">{{ $project->title }}</h1>
                @if($project->status === 'active')
                    <span class="badge text-bg-success">Active</span>
                @elseif($project->status === 'draft')
                    <span class="badge text-bg-secondary">Draft</span>
                @else
                    <span class="badge text-bg-warning">Archived</span>
                @endif
            </div>
            <p class="text-muted mb-0 small">
                Kategori: <strong>{{ $project->category ?? 'Umum' }}</strong> &bull; Dibuat pada {{ $project->created_at->format('d M Y, H:i') }}
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary btn-sm shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
            </a>
            <a href="{{ route('projects.edit', $project) }}" class="btn btn-warning btn-sm shadow-sm fw-bold">
                <i class="bi bi-pencil me-1"></i> Edit Proyek
            </a>
            <form method="POST" action="{{ route('projects.destroy', $project) }}" onsubmit="return confirm('Pindahkan proyek ini ke kotak sampah (Soft Delete)?')" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger btn-sm shadow-sm">
                    <i class="bi bi-trash3 me-1"></i> Hapus
                </button>
            </form>
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

    <div class="row g-4">
        {{-- Kolom Kiri: Detail Proyek --}}
        <div class="col-lg-8">
            <div class="card card-outline card-primary shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="card-title m-0 fw-bold">
                        <i class="bi bi-file-text me-1 text-primary"></i> Deskripsi & Catatan Proyek
                    </h5>
                </div>
                <div class="card-body">
                    @if($project->description)
                        <p class="card-text text-dark" style="white-space: pre-line; line-height: 1.7;">
                            {{ $project->description }}
                        </p>
                    @else
                        <p class="text-muted fst-italic mb-0">Tidak ada deskripsi tambahan yang dicatat untuk proyek ini.</p>
                    @endif
                </div>
            </div>

            <div class="card card-outline card-info shadow-sm">
                <div class="card-header">
                    <h5 class="card-title m-0 fw-bold">
                        <i class="bi bi-cpu me-1 text-info"></i> Stack Teknologi (Tech Stack)
                    </h5>
                </div>
                <div class="card-body">
                    @if($project->tech_stack)
                        <div class="d-flex flex-wrap gap-2">
                            @foreach(array_map('trim', explode(',', $project->tech_stack)) as $tech)
                                <span class="badge text-bg-light border px-3 py-2 fs-6">
                                    <i class="bi bi-code-slash me-1 text-primary"></i>{{ $tech }}
                                </span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted fst-italic mb-0">Stack teknologi belum ditentukan.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Metadata & Informasi Kepemilikan --}}
        <div class="col-lg-4">
            <div class="card card-outline card-secondary shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="card-title m-0 fw-bold">
                        <i class="bi bi-info-circle me-1 text-secondary"></i> Metadata Proyek
                    </h5>
                </div>
                <ul class="list-group list-group-flush small">
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <span class="text-muted">Pemilik (Author):</span>
                        <strong class="text-dark">{{ $project->user->name }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <span class="text-muted">Email Pemilik:</span>
                        <code class="text-dark">{{ $project->user->email }}</code>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <span class="text-muted">Kategori:</span>
                        <span class="badge text-bg-light border">{{ $project->category ?? 'Umum' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <span class="text-muted">Tanggal Mulai:</span>
                        <strong class="text-dark">{{ $project->started_at ? $project->started_at->format('d M Y') : '—' }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <span class="text-muted">Terakhir Diperbarui:</span>
                        <span class="text-muted">{{ $project->updated_at->diffForHumans() }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

@stop
