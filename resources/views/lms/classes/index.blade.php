@extends('layouts.lms')

@section('header_title', isset($selectedProdi) && $selectedProdi ? __('Kelas Aktif') . ' — ' . $selectedProdi->nama_prodi : __('Kelas Aktif'))

@section('content')

@if(isset($selectedProdi) && $selectedProdi)
<div style="background:#eff6ff; border-left:4px solid #3b82f6; border-radius:6px; padding:0.6rem 1rem; margin-bottom:1.25rem; display:flex; align-items:center; justify-content:space-between; font-size:0.85rem;">
    <span>📚 <strong>{{ $selectedProdi->nama_prodi }}</strong> — {{ __('Kelas Aktif') }}</span>
    @if(Auth::user()->hasRole(['admin','rektor','dekan']))
    <a href="{{ route('dashboard') }}" style="color:#3b82f6; font-weight:600; text-decoration:none;">← {{ __('Ganti Prodi') }}</a>
    @endif
</div>
@endif

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <h2 style="margin: 0; font-size: 1.5rem; color: var(--text-main);">{{ __('Kelas Aktif') }}</h2>
    @if(Auth::user()->hasRole(['admin', 'kaprodi']))
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAddClass">
        <i>➕</i> {{ __('Tambah Kelas') }}
    </button>
    @endif
</div>



<!-- Filter Form -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-body">
        <form action="{{ route('classes.index') }}" method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 1; min-width: 250px;">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">{{ __('Search') }}</label>
                <input type="text" name="search" placeholder="{{ __('Search by class name, subject, dosen...') }}" value="{{ request('search') }}" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 4px;">
            </div>
            <div style="display: flex; gap: 0.5rem; align-items: flex-end;">
                <button type="submit" class="btn btn-outline-primary btn-sm">{{ __('Filter') }}</button>
                <a href="{{ route('classes.index') }}" class="btn btn-outline-secondary btn-sm">{{ __('Reset') }}</a>
            </div>
        </form>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem;">
    @forelse($classRooms as $class)
    <div class="card" style="margin-bottom: 0; display: flex; flex-direction: column; height: 100%;">
        <div class="card-header" style="background-color: #f8fafc; display: flex; justify-content: space-between;">
            <span style="font-weight: 700; color: var(--primary);">{{ $class->nama_kelas }}</span>
            @php
                $statusColors = [
                    'active'   => ['bg' => '#dcfce7', 'color' => '#166534', 'label' => __('Aktif')],
                    'archived' => ['bg' => '#fef9c3', 'color' => '#854d0e', 'label' => __('Arsip')],
                    'deleted'  => ['bg' => '#fee2e2', 'color' => '#991b1b', 'label' => __('Dihapus')],
                ];
                $sc = $statusColors[$class->status] ?? $statusColors['active'];
            @endphp
            <span style="font-size: 0.75rem; background: {{ $sc['bg'] }}; color: {{ $sc['color'] }}; padding: 0.25rem 0.5rem; border-radius: 9999px; font-weight: 600;">
                {{ $sc['label'] }}
            </span>
        </div>
        <div class="card-body" style="display: flex; flex-direction: column; justify-content: space-between; flex: 1; padding: 1.5rem 1.5rem 1.875rem 1.5rem;">
            <div>
                <h3 style="margin: 0 0 0.5rem 0; font-size: 1.1rem;">{{ optional($class->subject)->nama_subject ?? __('Unknown Subject') }}</h3>
                <p style="margin: 0 0 1rem 0; font-size: 0.875rem; color: var(--text-muted);">
                    <i>👨‍🏫</i> {{ optional($class->dosen)->nama_dosen ?? __('Unknown Lecturer') }}
                </p>
                
                <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem; font-size: 0.875rem;">
                    <div style="background: #f1f5f9; padding: 0.5rem; border-radius: 4px; flex: 1; text-align: center;">
                        <strong style="display: block; color: var(--text-main);">{{ __('Tahun Akademik') }}</strong>
                        <span style="color: var(--text-muted);">{{ $class->tahun_akademik }}</span>
                    </div>
                    <div style="background: #f1f5f9; padding: 0.5rem; border-radius: 4px; flex: 1; text-align: center;">
                        <strong style="display: block; color: var(--text-main);">{{ __('Semester') }}</strong>
                        <span style="color: var(--text-muted);">{{ $class->semester }}</span>
                    </div>
                </div>
            </div>

            <div style="display: flex; gap: 0.5rem; margin-top: auto; align-items: center;">
                <a href="{{ route('classes.show', $class) }}" class="btn btn-primary" style="flex: 1; text-align: center; display: inline-flex; align-items: center; justify-content: center; height: 40px; font-weight: 600; border-radius: 6px;">
                    {{ __('Manage Enrollment') }}
                </a>
                
                @if(Auth::user()->hasRole(['admin', 'kaprodi']))
                <button class="btn btn-outline-secondary" style="padding: 0; width: 40px; height: 40px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid #cbd5e1; background: #fff; border-radius: 6px;" onclick="openEditModal('{{ $class->id }}', '{{ $class->subject_id }}', '{{ $classPrimaryDosenMap[$class->id] ?? '' }}', '{{ addslashes($class->nama_kelas) }}', '{{ $class->tahun_akademik }}', '{{ $class->semester }}', '{{ $class->status }}')" title="{{ __('Edit Class') }}">
                    ✏️
                </button>
                <form action="{{ route('classes.destroy', $class) }}" method="POST" style="margin: 0; display: inline-flex;" onsubmit="return confirm('{{ __('Hapus kelas ini? Kelas aktif yang memiliki kegiatan tidak dapat dihapus. Arsipkan dulu jika perlu.') }}')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger" style="padding: 0; width: 40px; height: 40px; display: inline-flex; align-items: center; justify-content: center; color: #dc2626; border: 1px solid #fee2e2; background: #fff; border-radius: 6px;" title="{{ __('Delete Class') }}">
                        🗑️
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div style="grid-column: 1 / -1; padding: 3rem; text-align: center; background: var(--white); border-radius: var(--radius); border: 1px dashed var(--border);">
        <span style="font-size: 3rem;">📭</span>
        <h3 style="margin: 1rem 0 0.5rem 0; color: var(--text-main);">{{ __('Belum Ada Kelas') }}</h3>
        <p style="color: var(--text-muted); margin: 0;">{{ __('Tidak ada data kelas yang dapat ditampilkan.') }}</p>
    </div>
    @endforelse
</div>

<div style="margin-top: 1.5rem;">
    {{ $classRooms->links() }}
</div>

@if(Auth::user()->hasRole(['admin', 'kaprodi']))
<!-- Modal Add Class -->
<div class="modal fade" id="modalAddClass" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Tambah Kelas Baru') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('classes.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-dark font-w600">{{ __('Mata Kuliah') }} <span class="text-danger">*</span></label>
                        <select name="subject_id" id="add-subject" class="form-control" required>
                            <option value="">-- {{ __('Pilih Mata Kuliah') }} --</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>{{ $subject->kode_subject }} - {{ $subject->nama_subject }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark font-w600">{{ __('Dosen Pengampu') }} <span class="text-danger">*</span></label>
                        <select name="dosen_id" id="add-dosen" class="form-control" required>
                            <option value="">-- {{ __('Pilih Dosen') }} --</option>
                            @foreach($dosens as $dosen)
                                <option value="{{ $dosen->id }}" {{ old('dosen_id') == $dosen->id ? 'selected' : '' }}>{{ $dosen->nama_dosen }} ({{ $dosen->nidn }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark font-w600">{{ __('Nama Kelas (e.g. Kelas A)') }} <span class="text-danger">*</span></label>
                        <input type="text" name="nama_kelas" class="form-control" required value="{{ old('nama_kelas') }}">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-dark font-w600">{{ __('Tahun Akademik') }} <span class="text-danger">*</span></label>
                            <input type="text" name="tahun_akademik" class="form-control" placeholder="e.g. 2023/2024" required value="{{ old('tahun_akademik') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-dark font-w600">{{ __('Semester') }} <span class="text-danger">*</span></label>
                            <select name="semester" class="default-select form-control wide" required>
                                <option value="Ganjil" {{ old('semester') == 'Ganjil' ? 'selected' : '' }}>{{ __('Ganjil') }}</option>
                                <option value="Genap" {{ old('semester') == 'Genap' ? 'selected' : '' }}>{{ __('Genap') }}</option>
                                <option value="Antara" {{ old('semester') == 'Antara' ? 'selected' : '' }}>{{ __('Antara') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer px-0 pb-0 mt-3 border-0">
                        <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">{{ __('Batal') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Simpan Kelas') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Class -->
<div class="modal fade" id="modalEditClass" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Edit Kelas') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="edit-form" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label text-dark font-w600">{{ __('Mata Kuliah') }} <span class="text-danger">*</span></label>
                        <select name="subject_id" id="edit-subject" class="form-control" required>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->kode_subject }} - {{ $subject->nama_subject }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark font-w600">{{ __('Dosen Pengampu') }} <span class="text-danger">*</span></label>
                        <select name="dosen_id" id="edit-dosen" class="form-control" required>
                            @foreach($dosens as $dosen)
                                <option value="{{ $dosen->id }}">{{ $dosen->nama_dosen }} ({{ $dosen->nidn }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark font-w600">{{ __('Nama Kelas (e.g. Kelas A)') }} <span class="text-danger">*</span></label>
                        <input type="text" name="nama_kelas" id="edit-nama" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-dark font-w600">{{ __('Tahun Akademik') }} <span class="text-danger">*</span></label>
                            <input type="text" name="tahun_akademik" id="edit-tahun" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-dark font-w600">{{ __('Semester') }} <span class="text-danger">*</span></label>
                            <select name="semester" id="edit-semester" class="default-select form-control wide" required>
                                <option value="Ganjil">{{ __('Ganjil') }}</option>
                                <option value="Genap">{{ __('Genap') }}</option>
                                <option value="Antara">{{ __('Antara') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark font-w600">{{ __('Status Kelas') }} <span class="text-danger">*</span></label>
                        <select name="status" id="edit-status" class="default-select form-control wide" required>
                            <option value="active">{{ __('Aktif') }}</option>
                            <option value="archived">{{ __('Arsip (Read-only)') }}</option>
                        </select>
                        <div class="alert alert-warning light mt-2 p-2" style="font-size: 0.75rem;">
                            ⚠️ {!! __('Jika diubah ke <strong>Arsip</strong>, semua konten kelas menjadi read-only.') !!}
                        </div>
                    </div>
                    <div class="modal-footer px-0 pb-0 mt-3 border-0">
                        <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">{{ __('Batal') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Update Kelas') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
    /* Styling to make Select2 look similar to original inputs */
    .select2-container .select2-selection--single {
        height: 38px;
        border: 1px solid var(--border);
        border-radius: 4px;
        display: flex;
        align-items: center;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: normal;
        color: var(--text-main);
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
</style>

<script>
    $(document).ready(function() {
        $('#add-subject').select2({
            dropdownParent: $('#modalAddClass'),
            width: '100%',
            placeholder: '-- Pilih Mata Kuliah --'
        });
        $('#add-dosen').select2({
            dropdownParent: $('#modalAddClass'),
            width: '100%',
            placeholder: '-- Pilih Dosen --'
        });
        
        $('#edit-subject').select2({
            dropdownParent: $('#modalEditClass'),
            width: '100%'
        });
        $('#edit-dosen').select2({
            dropdownParent: $('#modalEditClass'),
            width: '100%'
        });
    });

    function openEditModal(id, subject_id, dosen_id, nama_kelas, tahun_akademik, semester, status) {
        document.getElementById('edit-form').action = '/classes/' + id;
        $('#edit-subject').val(subject_id).trigger('change');
        $('#edit-dosen').val(dosen_id).trigger('change');
        document.getElementById('edit-nama').value = nama_kelas;
        document.getElementById('edit-tahun').value = tahun_akademik;
        
        $('#edit-semester').val(semester);
        $('#edit-status').val(status);
        $('.default-select').selectpicker('refresh');
        
        var editModal = new bootstrap.Modal(document.getElementById('modalEditClass'));
        editModal.show();
    }
</script>
@endif
@endsection
