@extends('adminlte::page')

@section('title', 'Daftar Proyek Akademik')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1 class="m-0 text-dark font-weight-bold">Proyek Akademik</h1>
            <p class="text-muted mb-0 small">Kelola riset, agentic AI, dan portofolio proyek akademik Anda secara aman.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('projects.trash') }}" class="btn btn-outline-secondary btn-sm shadow-sm">
                <i class="bi bi-trash3 me-1"></i> Kotak Sampah
                @if($trashedCount > 0)
                    <span class="badge text-bg-danger ms-1">{{ $trashedCount }}</span>
                @endif
            </a>
            <a href="{{ route('projects.create') }}" class="btn btn-primary btn-sm shadow-sm">
                <i class="bi bi-plus-circle me-1"></i> Tambah Proyek
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

    @if($projects->isEmpty())
        {{-- Empty State --}}
        <div class="card card-outline card-primary shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-folder-x display-4 text-muted mb-3 d-block"></i>
                <h5 class="fw-bold text-dark">Belum Ada Proyek</h5>
                <p class="text-muted small mb-4">Anda belum mencatat proyek akademik atau agent AI apapun. Buat proyek pertama Anda sekarang!</p>
                <a href="{{ route('projects.create') }}" class="btn btn-primary btn-sm shadow-sm">
                    <i class="bi bi-plus-circle me-1"></i> Buat Proyek Sekarang
                </a>
            </div>
        </div>
    @else
        {{-- Bulk Action Form --}}
        <form id="bulk-delete-form" method="POST" action="{{ route('projects.bulk-destroy') }}">
            @csrf
            @method('DELETE')

            {{-- Bulk Actions Toolbar --}}
            <div class="card bg-light border shadow-sm mb-3">
                <div class="card-body py-2 px-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted small">Pilihan:</span>
                        <span id="selected-count" class="badge text-bg-primary">0 proyek terpilih</span>
                    </div>
                    <div>
                        <button type="button" id="bulk-delete-btn" disabled
                            onclick="confirmBulkDelete()"
                            class="btn btn-danger btn-sm">
                            <i class="bi bi-trash3 me-1"></i> Hapus Terpilih (Bulk Delete)
                        </button>
                    </div>
                </div>
            </div>

            {{-- Projects Table Card --}}
            <div class="card card-outline card-primary shadow-sm mb-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle mb-0">
                            <thead class="table-light text-uppercase small text-muted">
                                <tr>
                                    <th style="width: 40px;" class="text-center ps-3">
                                        <input type="checkbox" id="select-all" class="form-check-input" title="Pilih Semua">
                                    </th>
                                    <th>Proyek</th>
                                    <th>Status</th>
                                    <th>Tech Stack</th>
                                    <th>Tanggal Mulai</th>
                                    <th class="text-end pe-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($projects as $project)
                                    <tr>
                                        <td class="text-center ps-3">
                                            <input type="checkbox" name="ids[]" value="{{ $project->id }}"
                                                class="form-check-input project-checkbox"
                                                onchange="updateBulkToolbar()">
                                        </td>
                                        <td>
                                            <a href="{{ route('projects.show', $project) }}" class="fw-bold text-dark text-decoration-none">
                                                {{ $project->title }}
                                            </a>
                                            @if($project->category)
                                                <div class="text-muted small">
                                                    <i class="bi bi-tag me-1"></i>{{ $project->category }}
                                                </div>
                                            @endif
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
                                                    @foreach(array_slice(array_map('trim', explode(',', $project->tech_stack)), 0, 3) as $tech)
                                                        <span class="badge text-bg-light border small">{{ $tech }}</span>
                                                    @endforeach
                                                    @if(count(explode(',', $project->tech_stack)) > 3)
                                                        <span class="badge text-bg-light border text-muted">+{{ count(explode(',', $project->tech_stack)) - 3 }}</span>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>
                                        <td class="small text-muted text-nowrap">
                                            {{ $project->started_at ? $project->started_at->format('d M Y') : '—' }}
                                        </td>
                                        <td class="text-end pe-3 text-nowrap">
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-info" title="Detail Proyek">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('projects.edit', $project) }}" class="btn btn-outline-primary" title="Edit Proyek">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button type="button"
                                                    onclick="confirmSingleDelete('{{ $project->id }}', '{{ addslashes($project->title) }}')"
                                                    class="btn btn-outline-danger" title="Pindahkan ke Kotak Sampah">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Pagination Footer --}}
                @if($projects->hasPages())
                    <div class="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div class="text-muted small">
                            Menampilkan {{ $projects->firstItem() }} sampai {{ $projects->lastItem() }} dari {{ $projects->total() }} proyek
                        </div>
                        <div>
                            {{ $projects->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                @endif
            </div>
        </form>

        {{-- Hidden Form for Single Delete --}}
        <form id="single-delete-form" method="POST" class="d-none">
            @csrf
            @method('DELETE')
        </form>
    @endif

@stop

@section('js')
<script>
    const selectAllCheckbox = document.getElementById('select-all');
    const projectCheckboxes = document.querySelectorAll('.project-checkbox');
    const selectedCountSpan = document.getElementById('selected-count');
    const bulkDeleteBtn = document.getElementById('bulk-delete-btn');

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function () {
            projectCheckboxes.forEach(cb => cb.checked = selectAllCheckbox.checked);
            updateBulkToolbar();
        });
    }

    function updateBulkToolbar() {
        const checkedCount = document.querySelectorAll('.project-checkbox:checked').length;
        if (selectedCountSpan) {
            selectedCountSpan.textContent = checkedCount + ' proyek terpilih';
        }
        if (bulkDeleteBtn) {
            bulkDeleteBtn.disabled = checkedCount === 0;
        }
        if (selectAllCheckbox && projectCheckboxes.length > 0) {
            selectAllCheckbox.checked = checkedCount === projectCheckboxes.length;
        }
    }

    function confirmBulkDelete() {
        const count = document.querySelectorAll('.project-checkbox:checked').length;
        if (count === 0) return;

        if (confirm(`Apakah Anda yakin ingin memindahkan ${count} proyek terpilih ke kotak sampah (Soft Delete)?`)) {
            document.getElementById('bulk-delete-form').submit();
        }
    }

    function confirmSingleDelete(id, title) {
        if (confirm(`Apakah Anda yakin ingin memindahkan proyek "${title}" ke kotak sampah?`)) {
            const form = document.getElementById('single-delete-form');
            form.action = `/projects/${id}`;
            form.submit();
        }
    }
</script>
@stop
