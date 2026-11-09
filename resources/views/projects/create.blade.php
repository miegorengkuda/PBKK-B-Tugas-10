@extends('adminlte::page')

@section('title', 'Tambah Proyek Baru')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="m-0 text-dark font-weight-bold">Tambah Proyek Baru</h1>
            <p class="text-muted mb-0 small">Daftarkan proyek penelitian, mini-project, atau agentic AI baru Anda.</p>
        </div>
        <div>
            <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary btn-sm shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
            </a>
        </div>
    </div>
@stop

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card card-outline card-primary shadow-sm">
                <div class="card-header">
                    <h5 class="card-title m-0 fw-bold">
                        <i class="bi bi-plus-circle-dotted me-1 text-primary"></i> Formulir Data Proyek
                    </h5>
                </div>

                <form method="POST" action="{{ route('projects.store') }}">
                    @csrf

                    <div class="card-body">
                        {{-- Judul Proyek --}}
                        <div class="mb-3">
                            <label for="title" class="form-label fw-bold">
                                Judul Proyek <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}" required autofocus
                                placeholder="Contoh: AI Gmail Inbox Manager"
                                class="form-control @error('title') is-invalid @enderror">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Grid: Kategori & Status --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="category" class="form-label fw-bold">Kategori</label>
                                <input type="text" name="category" id="category" value="{{ old('category') }}"
                                    placeholder="Contoh: Agentic AI, Web App, Research"
                                    class="form-control @error('category') is-invalid @enderror">
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="status" class="form-label fw-bold">
                                    Status <span class="text-danger">*</span>
                                </label>
                                <select name="status" id="status" required
                                    class="form-select @error('status') is-invalid @enderror">
                                    <option value="draft" {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>Draft (Konsep)</option>
                                    <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active (Sedang Berjalan)</option>
                                    <option value="archived" {{ old('status') === 'archived' ? 'selected' : '' }}>Archived (Selesai/Arsip)</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Grid: Tech Stack & Started At --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="tech_stack" class="form-label fw-bold">
                                    Tech Stack (Pisahkan dengan koma)
                                </label>
                                <input type="text" name="tech_stack" id="tech_stack" value="{{ old('tech_stack') }}"
                                    placeholder="Python, LangGraph, Gemini, SQLite"
                                    class="form-control @error('tech_stack') is-invalid @enderror">
                                @error('tech_stack')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="started_at" class="form-label fw-bold">Tanggal Mulai Proyek</label>
                                <input type="date" name="started_at" id="started_at" value="{{ old('started_at') }}"
                                    class="form-control @error('started_at') is-invalid @enderror">
                                @error('started_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Deskripsi --}}
                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold">Deskripsi & Catatan Proyek</label>
                            <textarea name="description" id="description" rows="5"
                                placeholder="Jelaskan tujuan, arsitektur, atau latar belakang proyek ini..."
                                class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="card-footer bg-light d-flex justify-content-between align-items-center">
                        <a href="{{ route('projects.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle me-1"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary shadow-sm">
                            <i class="bi bi-check-circle me-1"></i> Simpan Proyek
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@stop
