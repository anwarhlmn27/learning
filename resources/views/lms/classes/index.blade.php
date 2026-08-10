@extends('layouts.lms')

@section('header_title', isset($selectedProdi) && $selectedProdi ? __('Kelas Aktif') . ' — ' . $selectedProdi->nama_prodi : __('Kelas Aktif'))

@section('content')

@if(isset($selectedProdi) && $selectedProdi)
<div style="background:#eff6ff; border-left:4px solid #3b82f6; border-radius:6px; padding:0.6rem 1rem; margin-bottom:1.25rem; display:flex; align-items:center; justify-content:space-between; font-size:0.85rem;">
    <span>📚 <strong>{{ $selectedProdi->nama_prodi }}</strong> — {{ __('Kelas Aktif') }}</span>
    <a href="{{ route('dashboard') }}" style="color:#3b82f6; font-weight:600; text-decoration:none;">← {{ __('Ganti Program Studi') }}</a>
</div>
@endif

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 1rem;">
    <h2 style="margin: 0; font-size: 1.5rem; color: var(--text-main);">{{ __('Kelas Aktif') }}</h2>
    @if(Auth::user()->can('create-classes') || Auth::user()->hasRole(['admin', 'kaprodi', 'baak']))
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAddClass">
        <i>➕</i> {{ __('Tambah Kelas') }}
    </button>
    @endif
</div>

{{-- Navigation Tabs: Kelas yang Saya Ampu vs Kelas Program Studi --}}
<div style="display: flex; gap: 0.75rem; margin-bottom: 1.5rem; border-bottom: 2px solid #e2e8f0; padding-bottom: 0.75rem; flex-wrap: wrap; align-items: center;">
    @if($myClassesCount > 0 || Auth::user()->hasRole('dosen') || Auth::user()->dosen)
    <a href="{{ route('classes.index', array_merge(request()->except(['page', 'tab']), ['tab' => 'my_classes', 'prodi_id' => request('prodi_id') ?? ($selectedProdi->id ?? null)])) }}" 
       class="btn {{ ($activeTab ?? '') === 'my_classes' ? 'btn-primary' : 'btn-outline-primary' }} btn-sm" 
       style="border-radius: 9999px; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.45rem 1rem;">
        <span>👨‍🏫 {{ __('Kelas yang Saya Ampu') }}</span>
        <span class="badge {{ ($activeTab ?? '') === 'my_classes' ? 'bg-white text-primary' : 'bg-primary text-white' }}" style="border-radius: 9999px; font-size: 0.75rem;">{{ $myClassesCount }}</span>
    </a>
    @endif

    @if(isset($selectedProdi) && $selectedProdi)
    <a href="{{ route('classes.index', array_merge(request()->except(['page', 'tab']), ['tab' => 'prodi_classes', 'prodi_id' => $selectedProdi->id])) }}" 
       class="btn {{ ($activeTab ?? '') === 'prodi_classes' ? 'btn-primary' : 'btn-outline-primary' }} btn-sm" 
       style="border-radius: 9999px; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.45rem 1rem;">
        <span>📚 {{ __('Kelas Prodi') }} ({{ $selectedProdi->nama_prodi }})</span>
        <span class="badge {{ ($activeTab ?? '') === 'prodi_classes' ? 'bg-white text-primary' : 'bg-primary text-white' }}" style="border-radius: 9999px; font-size: 0.75rem;">{{ $prodiClassesCount }}</span>
    </a>
    @endif
</div>

{{-- Helpful banner if current prodi tab is empty but user has teaching classes in another prodi --}}
@if(($activeTab ?? '') === 'prodi_classes' && $prodiClassesCount == 0 && $myClassesCount > 0)
<div class="alert alert-info d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2" style="border-left: 4px solid #3b82f6; background: #eff6ff; border-radius: 8px; padding: 1rem 1.25rem;">
    <div>
        <strong style="color: #1e40af; font-size: 0.95rem;">📌 {{ __('Informasi Dosen Pengampu') }}</strong>
        <p class="mb-0 text-muted" style="font-size: 0.875rem;">
            {{ __('Belum ada kelas di prodi ini, namun Anda terdaftar sebagai dosen pengampu di') }} <strong>{{ $myClassesCount }} {{ __('kelas aktif') }}</strong> ({{ __('misalnya di prodi lain seperti TI') }}).
        </p>
    </div>
    <a href="{{ route('classes.index', array_merge(request()->except(['page']), ['tab' => 'my_classes', 'prodi_id' => request('prodi_id')])) }}" class="btn btn-primary btn-sm text-nowrap" style="border-radius: 6px; font-weight: 600;">
        👨‍🏫 {{ __('Buka Kelas yang Saya Ampu') }} ({{ $myClassesCount }})
    </a>
</div>
@endif

<!-- Filter Form -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-body">
        <form action="{{ route('classes.index') }}" method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            @if(request('tab'))
                <input type="hidden" name="tab" value="{{ request('tab') }}">
            @endif
            @if(request('prodi_id'))
                <input type="hidden" name="prodi_id" value="{{ request('prodi_id') }}">
            @endif
            <div style="flex: 1; min-width: 250px;">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">{{ __('Search') }}</label>
                <input type="text" name="search" placeholder="{{ __('Search by class name, subject, dosen...') }}" value="{{ request('search') }}" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 4px;">
            </div>
            <div style="display: flex; gap: 0.5rem; align-items: flex-end;">
                <button type="submit" class="btn btn-outline-primary btn-sm">{{ __('Filter') }}</button>
                <a href="{{ route('classes.index', array_filter(['tab' => request('tab'), 'prodi_id' => request('prodi_id')])) }}" class="btn btn-outline-secondary btn-sm">{{ __('Reset') }}</a>
            </div>
        </form>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem;">
    @forelse($classRooms as $class)
    <div class="card" style="margin-bottom: 0; display: flex; flex-direction: column; height: 100%;">
        <div class="card-header" style="background-color: #f8fafc; display: flex; justify-content: space-between; align-items: center;">
            <span style="font-weight: 700; color: var(--primary);">{{ $class->nama_kelas }}</span>
            @php
                $statusColors = [
                    'active'   => ['bg' => '#dcfce7', 'color' => '#166534', 'label' => __('Aktif')],
                    'archived' => ['bg' => '#fef9c3', 'color' => '#854d0e', 'label' => __('Arsip')],
                    'deleted'  => ['bg' => '#fee2e2', 'color' => '#991b1b', 'label' => __('Dihapus')],
                ];
                $sc = $statusColors[$class->status] ?? $statusColors['active'];
            @endphp
            <div style="display: flex; align-items: center; gap: 0.4rem;">
                @if(optional(optional($class->subject)->prodi)->nama_prodi)
                    <span style="font-size: 0.7rem; background: #e0e7ff; color: #3730a3; padding: 0.2rem 0.5rem; border-radius: 4px; font-weight: 600;">
                        {{ $class->subject->prodi->nama_prodi }}
                    </span>
                @endif
                <span style="font-size: 0.75rem; background: {{ $sc['bg'] }}; color: {{ $sc['color'] }}; padding: 0.25rem 0.5rem; border-radius: 9999px; font-weight: 600;">
                    {{ $sc['label'] }}
                </span>
            </div>
        </div>
        <div class="card-body" style="display: flex; flex-direction: column; justify-content: space-between; flex: 1; padding: 1.5rem 1.5rem 1.875rem 1.5rem;">
            <div>
                <h3 style="margin: 0 0 0.5rem 0; font-size: 1.1rem;">{{ optional($class->subject)->nama_subject ?? __('Unknown Subject') }}</h3>
                <p style="margin: 0 0 1rem 0; font-size: 0.875rem; color: var(--text-muted);">
                    @php
                        $firstDosenUser = $class->dosens()->first();
                    @endphp
                    <i>👨‍🏫</i> {{ $firstDosenUser ? ($firstDosenUser->dosen->nama_dosen ?? $firstDosenUser->name) : __('Unknown Lecturer') }}
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
                
                @if(Auth::user()->can('edit-classes') || Auth::user()->can('delete-classes') || Auth::user()->hasRole(['admin', 'kaprodi', 'baak']))
                <button class="btn btn-outline-secondary" style="padding: 0; width: 40px; height: 40px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid #cbd5e1; background: #fff; border-radius: 6px;" onclick="openEditModal('{{ $class->id }}', '{{ $class->subject_id }}', '{{ $classPrimaryDosenMap[$class->id] ?? '' }}', '{{ addslashes($class->nama_kelas) }}', '{{ $class->tahun_akademik }}', '{{ $class->semester }}', '{{ $class->status }}')" title="{{ __('Edit Class') }}">
                    ✏️
                </button>
                <form action="{{ route('classes.destroy', $class) }}" method="POST" style="margin: 0; display: inline-flex;" class="swal-confirm-form" data-swal-msg="{{ __('Hapus kelas ini? Kelas aktif yang memiliki kegiatan tidak dapat dihapus. Arsipkan dulu jika perlu.') }}">
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

@if(Auth::user()->can('create-classes') || Auth::user()->hasRole(['admin', 'kaprodi', 'baak']))
<!-- Modal Add Class -->
<div class="modal fade" id="modalAddClass" role="dialog" aria-hidden="true">
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
                        <select name="subject_id" id="add-subject" class="default-select form-control wide" data-live-search="true" required>
                            <option value="">-- {{ __('Pilih Mata Kuliah') }} --</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>{{ $subject->kode_subject }} - {{ $subject->nama_subject }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark font-w600">{{ __('Dosen Pengampu') }} <span class="text-danger">*</span></label>
                        <div style="border: 1px solid #e2e8f0; border-radius: 6px; padding: 1rem; background: #f8fafc;">
                            <div class="mb-3">
                                <label class="form-label" style="font-size: 0.85rem; color: var(--text-muted);">{{ __('Filter Fakultas') }}</label>
                                <select id="add-dosen-fakultas" class="default-select form-control wide" data-live-search="true">
                                    <option value="">-- {{ __('Semua Fakultas') }} --</option>
                                    @foreach($allFakultas as $fakultas)
                                        <option value="{{ $fakultas->id }}">{{ $fakultas->nama_fakultas }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" style="font-size: 0.85rem; color: var(--text-muted);">{{ __('Filter Prodi') }}</label>
                                <select id="add-dosen-prodi" class="default-select form-control wide" data-live-search="true">
                                    <option value="">-- {{ __('Semua Prodi') }} --</option>
                                </select>
                            </div>
                            <div class="mb-0">
                                <label class="form-label" style="font-size: 0.85rem; font-weight: 600;">{{ __('Pilih Dosen') }} <span class="text-danger">*</span></label>
                                <select name="dosen_id" id="add-dosen" class="default-select form-control wide" data-live-search="true" required>
                                    <option value="">-- {{ __('Pilih Dosen') }} --</option>
                                    @foreach($dosens as $dosen)
                                        @php
                                            $prodiName = $dosen->prodi ? $dosen->prodi->nama_prodi : __('Tanpa Prodi');
                                            $fakultasName = ($dosen->prodi && $dosen->prodi->fakultas) ? $dosen->prodi->fakultas->nama_fakultas : '';
                                            $location = $fakultasName ? "{$fakultasName} - {$prodiName}" : $prodiName;
                                            
                                            $prefix = '';
                                            if (isset($selectedProdi) && $selectedProdi) {
                                                $prefix = ($dosen->prodi_id == $selectedProdi->id) ? '[' . __('Internal') . '] ' : '[' . __('Eksternal') . '] ';
                                            }
                                        @endphp
                                        <option value="{{ $dosen->id }}" {{ old('dosen_id') == $dosen->id ? 'selected' : '' }}>
                                            {{ $prefix }}{{ $dosen->nama_dosen }} ({{ $dosen->nidn }}) @if($dosen->prodi_id != ($selectedProdi->id ?? '')) - {{ $location }} @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
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
<div class="modal fade" id="modalEditClass" role="dialog" aria-hidden="true">
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
                        <select name="subject_id" id="edit-subject" class="default-select form-control wide" data-live-search="true" required>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->kode_subject }} - {{ $subject->nama_subject }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark font-w600">{{ __('Dosen Pengampu') }} <span class="text-danger">*</span></label>
                        <div style="border: 1px solid #e2e8f0; border-radius: 6px; padding: 1rem; background: #f8fafc;">
                            <div class="mb-3">
                                <label class="form-label" style="font-size: 0.85rem; color: var(--text-muted);">{{ __('Filter Fakultas') }}</label>
                                <select id="edit-dosen-fakultas" class="default-select form-control wide" data-live-search="true">
                                    <option value="">-- {{ __('Semua Fakultas') }} --</option>
                                    @foreach($allFakultas as $fakultas)
                                        <option value="{{ $fakultas->id }}">{{ $fakultas->nama_fakultas }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" style="font-size: 0.85rem; color: var(--text-muted);">{{ __('Filter Prodi') }}</label>
                                <select id="edit-dosen-prodi" class="default-select form-control wide" data-live-search="true">
                                    <option value="">-- {{ __('Semua Prodi') }} --</option>
                                </select>
                            </div>
                            <div class="mb-0">
                                <label class="form-label" style="font-size: 0.85rem; font-weight: 600;">{{ __('Pilih Dosen') }} <span class="text-danger">*</span></label>
                                <select name="dosen_id" id="edit-dosen" class="default-select form-control wide" data-live-search="true" required>
                                    <option value="">-- {{ __('Pilih Dosen') }} --</option>
                                    @foreach($dosens as $dosen)
                                        @php
                                            $prodiName = $dosen->prodi ? $dosen->prodi->nama_prodi : __('Tanpa Prodi');
                                            $fakultasName = ($dosen->prodi && $dosen->prodi->fakultas) ? $dosen->prodi->fakultas->nama_fakultas : '';
                                            $location = $fakultasName ? "{$fakultasName} - {$prodiName}" : $prodiName;
                                            
                                            $prefix = '';
                                            if (isset($selectedProdi) && $selectedProdi) {
                                                $prefix = ($dosen->prodi_id == $selectedProdi->id) ? '[' . __('Internal') . '] ' : '[' . __('Eksternal') . '] ';
                                            }
                                        @endphp
                                        <option value="{{ $dosen->id }}">
                                            {{ $prefix }}{{ $dosen->nama_dosen }} ({{ $dosen->nidn }}) @if($dosen->prodi_id != ($selectedProdi->id ?? '')) - {{ $location }} @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
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



<script>
    const allProdis = @json($allProdis);
    const allDosens = @json($dosens);

    function setupFacultyProdiDosenChain(fakultasSelectId, prodiSelectId, dosenSelectId) {
        const $fakultas = $(`#${fakultasSelectId}`);
        const $prodi = $(`#${prodiSelectId}`);
        const $dosen = $(`#${dosenSelectId}`);

        // When Fakultas changes
        $fakultas.on('change', function() {
            const fakultasId = $(this).val();
            $prodi.empty().append('<option value="">-- ' + "{{ __('Semua Prodi') }}" + ' --</option>');
            $dosen.empty().append('<option value="">-- ' + "{{ __('Pilih Dosen') }}" + ' --</option>');

            if (fakultasId) {
                const filteredProdis = allProdis.filter(p => p.id_fakultas == fakultasId);
                filteredProdis.forEach(p => {
                    $prodi.append(`<option value="${p.id}">${p.nama_prodi}</option>`);
                });
            }
            $prodi.selectpicker('refresh');
            $dosen.selectpicker('refresh');
        });

        // When Prodi changes
        $prodi.on('change', function() {
            const prodiId = $(this).val();
            $dosen.empty().append('<option value="">-- ' + "{{ __('Pilih Dosen') }}" + ' --</option>');

            if (prodiId) {
                const filteredDosens = allDosens.filter(d => d.prodi_id == prodiId);
                filteredDosens.forEach(d => {
                    $dosen.append(`<option value="${d.id}">${d.nama_dosen} (${d.nidn || '-'})</option>`);
                });
            }
            $dosen.selectpicker('refresh');
        });
    }

    // Initialize document events
    document.addEventListener('DOMContentLoaded', function() {
        // Setup chains for both Add and Edit modals
        setupFacultyProdiDosenChain('add-dosen-fakultas', 'add-dosen-prodi', 'add-dosen');
        setupFacultyProdiDosenChain('edit-dosen-fakultas', 'edit-dosen-prodi', 'edit-dosen');

        // Pre-fill Add Modal if selectedProdi exists
        @if(isset($selectedProdi) && $selectedProdi)
            const initialProdiId = "{{ $selectedProdi->id }}";
            const initialFakultasId = "{{ $selectedProdi->id_fakultas }}";

            $('#add-dosen-fakultas').val(initialFakultasId).selectpicker('refresh').trigger('change');
            $('#add-dosen-prodi').val(initialProdiId).selectpicker('refresh').trigger('change');
        @endif
    });

    function openEditModal(id, subject_id, dosen_id, nama_kelas, tahun_akademik, semester, status) {
        document.getElementById('edit-form').action = "{{ route('classes.update', ':id') }}".replace(':id', id);
        $('#edit-subject').val(subject_id).trigger('change');

        // Populate Faculty and Prodi filters dynamically for the chosen lecturer
        const chosenDosen = allDosens.find(d => d.id === dosen_id);
        if (chosenDosen) {
            const prodiId = chosenDosen.prodi_id;
            const chosenProdi = allProdis.find(p => p.id === prodiId);
            if (chosenProdi) {
                const fakultasId = chosenProdi.id_fakultas;

                $('#edit-dosen-fakultas').val(fakultasId).trigger('change');
                $('#edit-dosen-prodi').val(prodiId).trigger('change');
                $('#edit-dosen').val(dosen_id);
            }
        } else {
            $('#edit-dosen-fakultas').val('').trigger('change');
        }

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
