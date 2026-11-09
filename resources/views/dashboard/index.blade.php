@extends('adminlte::page')

@section('title', 'Dashboard Overview')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1 class="m-0 text-dark font-weight-bold">Dashboard Overview</h1>
            <p class="text-muted mb-0 small">Ringkasan statistik akademik dan manajemen proyek portfolio Anda.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('projects.create') }}" class="btn btn-primary btn-sm shadow-sm">
                <i class="bi bi-plus-circle me-1"></i> Tambah Proyek Baru
            </a>
            <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary btn-sm shadow-sm">
                <i class="bi bi-folder2-open me-1"></i> Kelola Proyek
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
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('info'))
        <div class="alert alert-info alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-info-circle-fill me-2"></i>
            {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- ── 1. Statistics Widgets (Dinamis dari Database via Eloquent) ── --}}
    <div class="row g-3 mb-4">

        {{-- Widget 1: Total Users --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="small-box text-bg-info shadow-sm h-100 mb-0">
                <div class="inner">
                    <h3 class="fw-bold">{{ $totalUsers }}</h3>
                    <p class="mb-0">Total Pengguna Terdaftar</p>
                    <small class="opacity-75">Akun pengguna aktif di sistem</small>
                </div>
                <div class="small-box-icon">
                    <i class="bi bi-people-fill"></i>
                </div>
                <span class="small-box-footer text-white-50">
                    Sistem Database Portal <i class="bi bi-database ms-1"></i>
                </span>
            </div>
        </div>

        {{-- Widget 2: Total Projects --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="small-box text-bg-success shadow-sm h-100 mb-0">
                <div class="inner">
                    <h3 class="fw-bold">{{ $totalProjects }}</h3>
                    <p class="mb-0">Total Proyek Akademik</p>
                    <small class="opacity-75">{{ $myTotalProjects }} proyek milik Anda</small>
                </div>
                <div class="small-box-icon">
                    <i class="bi bi-folder-fill"></i>
                </div>
                <a href="{{ route('projects.index') }}" class="small-box-footer link-light link-underline-opacity-0">
                    Lihat Semua Proyek <i class="bi bi-arrow-right-circle ms-1"></i>
                </a>
            </div>
        </div>

        {{-- Widget 3: Active Projects --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="small-box text-bg-warning shadow-sm h-100 mb-0">
                <div class="inner">
                    <h3 class="fw-bold">{{ $activeProjects }}</h3>
                    <p class="mb-0">Proyek Aktif (Running)</p>
                    <small class="opacity-75">{{ $myActiveProjects }} aktif milik Anda</small>
                </div>
                <div class="small-box-icon">
                    <i class="bi bi-play-circle-fill"></i>
                </div>
                <a href="{{ route('projects.index') }}" class="small-box-footer link-dark link-underline-opacity-0">
                    Kelola Proyek Aktif <i class="bi bi-arrow-right-circle ms-1"></i>
                </a>
            </div>
        </div>

        {{-- Widget 4: Draft & Archived Projects --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="small-box text-bg-secondary shadow-sm h-100 mb-0">
                <div class="inner">
                    <h3 class="fw-bold">{{ $draftProjects }} <span class="fs-6 fw-normal">/ {{ $archivedProjects }}</span></h3>
                    <p class="mb-0">Draft / Arsip Proyek</p>
                    <small class="opacity-75">{{ $trashedProjects }} berada di kotak sampah</small>
                </div>
                <div class="small-box-icon">
                    <i class="bi bi-archive-fill"></i>
                </div>
                <a href="{{ route('projects.trash') }}" class="small-box-footer link-light link-underline-opacity-0">
                    Buka Kotak Sampah <i class="bi bi-trash3 ms-1"></i>
                </a>
            </div>
        </div>

    </div>

    {{-- ── 2. Content Row: Recent Projects & Profile Summary ── --}}
    <div class="row g-4">

        {{-- Kolom Kiri: Tabel Proyek Terbaru --}}
        <div class="col-lg-8">
            <div class="card card-outline card-primary shadow-sm h-100 mb-0">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title m-0 fw-bold">
                        <i class="bi bi-clock-history me-1 text-primary"></i> Proyek Terbaru Anda
                    </h5>
                    <a href="{{ route('projects.index') }}" class="btn btn-outline-primary btn-xs">
                        Lihat Semua Proyek <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($recentProjects->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-folder2-open display-4 text-muted mb-3 d-block"></i>
                            <h6 class="fw-bold text-dark">Belum Ada Proyek</h6>
                            <p class="text-muted small mb-3">Mulai tambahkan proyek riset, portofolio, atau agentic AI pertama Anda.</p>
                            <a href="{{ route('projects.create') }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-plus-circle me-1"></i> Buat Proyek Sekarang
                            </a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle mb-0">
                                <thead class="table-light text-uppercase small text-muted">
                                    <tr>
                                        <th class="ps-3">Judul Proyek</th>
                                        <th>Kategori</th>
                                        <th>Status</th>
                                        <th>Tech Stack</th>
                                        <th class="text-end pe-3">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentProjects as $project)
                                        <tr>
                                            <td class="ps-3">
                                                <a href="{{ route('projects.show', $project) }}" class="fw-bold text-decoration-none text-dark">
                                                    {{ $project->title }}
                                                </a>
                                                <div class="text-muted small">
                                                    {{ $project->started_at ? $project->started_at->format('d M Y') : 'Tanpa tanggal mulai' }}
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-secondary border">
                                                    {{ $project->category ?? 'Umum' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($project->status === 'active')
                                                    <span class="badge text-bg-success">Active</span>
                                                @elseif($project->status === 'draft')
                                                    <span class="badge text-bg-secondary">Draft</span>
                                                @else
                                                    <span class="badge text-bg-warning">Archived</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($project->tech_stack)
                                                    <div class="d-flex flex-wrap gap-1">
                                                        @foreach(array_slice(array_map('trim', explode(',', $project->tech_stack)), 0, 2) as $tech)
                                                            <span class="badge text-bg-light border small">{{ $tech }}</span>
                                                        @endforeach
                                                        @if(count(explode(',', $project->tech_stack)) > 2)
                                                            <span class="badge text-bg-light border text-muted">+{{ count(explode(',', $project->tech_stack)) - 2 }}</span>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-muted small">—</span>
                                                @endif
                                            </td>
                                            <td class="text-end pe-3">
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-info" title="Lihat Detail">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('projects.edit', $project) }}" class="btn btn-outline-primary" title="Edit Proyek">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
                <div class="card-footer bg-white border-top text-end py-2">
                    <small class="text-muted">Menampilkan hingga 5 proyek terakhir yang Anda tambahkan.</small>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Info Akun & Pintasan Cepat --}}
        <div class="col-lg-4">
            {{-- Card Profil Akun --}}
            <div class="card card-outline card-info shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="card-title m-0 fw-bold">
                        <i class="bi bi-person-circle me-1 text-info"></i> Akun Pengguna Aktif
                    </h5>
                </div>
                <div class="card-body text-center pt-4">
                    <img src="{{ Auth::user()->adminlte_image() }}" class="rounded-circle shadow-sm mb-3 border border-3 border-light" alt="{{ Auth::user()->name }}" width="80" height="80">
                    <h5 class="fw-bold mb-1">{{ Auth::user()->name }}</h5>
                    <p class="text-muted small mb-3">{{ Auth::user()->email }}</p>

                    <div class="row g-2 text-center border-top border-bottom py-2 my-3">
                        <div class="col-4 border-end">
                            <div class="fw-bold fs-5 text-primary">{{ $myTotalProjects }}</div>
                            <div class="small text-muted" style="font-size: 11px;">Total Proyek</div>
                        </div>
                        <div class="col-4 border-end">
                            <div class="fw-bold fs-5 text-success">{{ $myActiveProjects }}</div>
                            <div class="small text-muted" style="font-size: 11px;">Aktif</div>
                        </div>
                        <div class="col-4">
                            <div class="fw-bold fs-5 text-danger">{{ $myTrashedProjects }}</div>
                            <div class="small text-muted" style="font-size: 11px;">Sampah</div>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="{{ route('profile') }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-person-badge me-1"></i> Buka Profil Akademik
                        </a>
                    </div>
                </div>
            </div>

            {{-- Card Pintasan Fitur Tugas Sebelumnya --}}
            <div class="card card-outline card-secondary shadow-sm">
                <div class="card-header">
                    <h5 class="card-title m-0 fw-bold">
                        <i class="bi bi-grid-fill me-1 text-secondary"></i> Modul Terintegrasi
                    </h5>
                </div>
                <div class="list-group list-group-flush small">
                    <a href="{{ route('profile') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-person-badge text-primary me-2"></i> Profil Mahasiswa (Tugas 4)</span>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                    <a href="{{ route('agent') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-robot text-success me-2"></i> Ide Agentic AI (Tugas 4)</span>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                    <a href="{{ route('ipk.kalkulator', ['ip1' => '3.50', 'ip2' => '3.45']) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-calculator text-warning me-2"></i> Kalkulator IPK (Tugas 2)</span>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                    <a href="{{ route('home') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-globe text-info me-2"></i> Website Utama (Public)</span>
                        <i class="bi bi-box-arrow-up-right text-muted"></i>
                    </a>
                </div>
            </div>
        </div>

    </div>

@stop

@section('css')
    {{-- Custom styling untuk widget AdminLTE v4 --}}
    <style>
        .small-box {
            border-radius: 0.5rem;
            position: relative;
            display: block;
            margin-bottom: 20px;
            box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
        }
        .small-box > .inner {
            padding: 15px;
        }
        .small-box > .small-box-footer {
            background-color: rgba(0,0,0,.1);
            color: rgba(255,255,255,.8);
            display: block;
            padding: 4px 0;
            position: relative;
            text-align: center;
            text-decoration: none;
            z-index: 10;
            font-size: 12px;
        }
        .small-box .small-box-icon {
            font-size: 55px;
            position: absolute;
            right: 15px;
            top: 15px;
            color: rgba(0,0,0,.15);
            transition: transform .3s linear;
        }
        .small-box:hover .small-box-icon {
            transform: scale(1.1);
        }
    </style>
@stop
