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
    <button class="btn" onclick="document.getElementById('modal-add').style.display = 'flex'">
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
            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn">{{ __('Filter') }}</button>
                <a href="{{ route('classes.index') }}" class="btn btn-outline" style="text-decoration: none; display: inline-block; text-align: center;">{{ __('Reset') }}</a>
            </div>
        </form>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem;">
    @forelse($classRooms as $class)
    <div class="card" style="margin-bottom: 0;">
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
        <div class="card-body">
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

            <div style="display: flex; gap: 0.5rem;">
                <a href="{{ route('classes.show', $class) }}" class="btn" style="flex: 1; text-align: center;">
                    {{ __('Manage Enrollment') }}
                </a>
                
                @if(Auth::user()->hasRole(['admin', 'kaprodi']))
                <button class="btn btn-outline" style="padding: 0.5rem;" onclick="openEditModal('{{ $class->id }}', '{{ $class->subject_id }}', '{{ $classPrimaryDosenMap[$class->id] ?? '' }}', '{{ addslashes($class->nama_kelas) }}', '{{ $class->tahun_akademik }}', '{{ $class->semester }}', '{{ $class->status }}')" title="{{ __('Edit Class') }}">
                    ✏️
                </button>
                <form action="{{ route('classes.destroy', $class) }}" method="POST" style="margin: 0;" onsubmit="return confirm('{{ __('Hapus kelas ini? Kelas aktif yang memiliki kegiatan tidak dapat dihapus. Arsipkan dulu jika perlu.') }}')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline" style="padding: 0.5rem; color: #dc2626; border-color: #fecaca;" title="{{ __('Delete Class') }}">
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
<div id="modal-add" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 500px; margin: 1rem; max-height: 90vh; overflow-y: auto;">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; background: white; z-index: 10;">
            <h3 style="margin: 0; font-size: 1.1rem;">{{ __('Add New Class') }}</h3>
            <button onclick="document.getElementById('modal-add').style.display = 'none'" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #6b7280;">&times;</button>
        </div>
        <div class="card-body">
            <form action="{{ route('classes.store') }}" method="POST">
                @csrf
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">{{ __('Mata Kuliah') }} <span style="color: red;">*</span></label>
                    <select name="subject_id" id="add-subject" required style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 4px;">
                        <option value="">-- {{ __('Pilih Mata Kuliah') }} --</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>{{ $subject->kode_subject }} - {{ $subject->nama_subject }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">{{ __('Dosen Pengampu') }} <span style="color: red;">*</span></label>
                    <select name="dosen_id" id="add-dosen" required style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 4px;">
                        <option value="">-- {{ __('Pilih Dosen') }} --</option>
                        @foreach($dosens as $dosen)
                            <option value="{{ $dosen->id }}" {{ old('dosen_id') == $dosen->id ? 'selected' : '' }}>{{ $dosen->nama_dosen }} ({{ $dosen->nidn }})</option>
                        @endforeach
                    </select>
                </div>
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">{{ __('Nama Kelas (e.g. Kelas A)') }} <span style="color: red;">*</span></label>
                    <input type="text" name="nama_kelas" required value="{{ old('nama_kelas') }}" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 4px;">
                </div>
                <div style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                    <div style="flex: 1;">
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">{{ __('Tahun Akademik') }} <span style="color: red;">*</span></label>
                        <input type="text" name="tahun_akademik" placeholder="e.g. 2023/2024" required value="{{ old('tahun_akademik') }}" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 4px;">
                    </div>
                    <div style="flex: 1;">
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">{{ __('Semester') }} <span style="color: red;">*</span></label>
                        <select name="semester" required style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 4px;">
                            <option value="Ganjil" {{ old('semester') == 'Ganjil' ? 'selected' : '' }}>{{ __('Ganjil') }}</option>
                            <option value="Genap" {{ old('semester') == 'Genap' ? 'selected' : '' }}>{{ __('Genap') }}</option>
                            <option value="Antara" {{ old('semester') == 'Antara' ? 'selected' : '' }}>{{ __('Antara') }}</option>
                        </select>
                    </div>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1.5rem;">
                    <button type="button" class="btn btn-outline" onclick="document.getElementById('modal-add').style.display = 'none'">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn">{{ __('Save Class') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Class -->
<div id="modal-edit" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 500px; margin: 1rem; max-height: 90vh; overflow-y: auto;">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; background: white; z-index: 10;">
            <h3 style="margin: 0; font-size: 1.1rem;">{{ __('Edit Class') }}</h3>
            <button onclick="document.getElementById('modal-edit').style.display = 'none'" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #6b7280;">&times;</button>
        </div>
        <div class="card-body">
            <form id="edit-form" method="POST">
                @csrf
                @method('PUT')
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">{{ __('Mata Kuliah') }} <span style="color: red;">*</span></label>
                    <select name="subject_id" id="edit-subject" required style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 4px;">
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->kode_subject }} - {{ $subject->nama_subject }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">{{ __('Dosen Pengampu') }} <span style="color: red;">*</span></label>
                    <select name="dosen_id" id="edit-dosen" required style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 4px;">
                        @foreach($dosens as $dosen)
                            <option value="{{ $dosen->id }}">{{ $dosen->nama_dosen }} ({{ $dosen->nidn }})</option>
                        @endforeach
                    </select>
                </div>
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">{{ __('Nama Kelas (e.g. Kelas A)') }} <span style="color: red;">*</span></label>
                    <input type="text" name="nama_kelas" id="edit-nama" required style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 4px;">
                </div>
                <div style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                    <div style="flex: 1;">
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">{{ __('Tahun Akademik') }} <span style="color: red;">*</span></label>
                        <input type="text" name="tahun_akademik" id="edit-tahun" required style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 4px;">
                    </div>
                    <div style="flex: 1;">
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">{{ __('Semester') }} <span style="color: red;">*</span></label>
                        <select name="semester" id="edit-semester" required style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 4px;">
                            <option value="Ganjil">{{ __('Ganjil') }}</option>
                            <option value="Genap">{{ __('Genap') }}</option>
                            <option value="Antara">{{ __('Antara') }}</option>
                        </select>
                    </div>
                </div>
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">{{ __('Status Kelas') }} <span style="color: red;">*</span></label>
                    <select name="status" id="edit-status" required style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 4px;">
                        <option value="active">{{ __('Aktif') }}</option>
                        <option value="archived">{{ __('Arsip (Read-only)') }}</option>
                    </select>
                    <p style="font-size: 0.75rem; color: #92400e; margin-top: 0.4rem; padding: 0.4rem 0.6rem; background: #fef9c3; border-radius: 4px;">⚠️ {!! __('Jika diubah ke <strong>Arsip</strong>, semua konten kelas menjadi read-only.') !!}</p>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1.5rem;">
                    <button type="button" class="btn btn-outline" onclick="document.getElementById('modal-edit').style.display = 'none'">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn">{{ __('Update Class') }}</button>
                </div>
            </form>
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
            dropdownParent: $('#modal-add'),
            width: '100%',
            placeholder: '-- Pilih Mata Kuliah --'
        });
        $('#add-dosen').select2({
            dropdownParent: $('#modal-add'),
            width: '100%',
            placeholder: '-- Pilih Dosen --'
        });
        
        $('#edit-subject').select2({
            dropdownParent: $('#modal-edit'),
            width: '100%'
        });
        $('#edit-dosen').select2({
            dropdownParent: $('#modal-edit'),
            width: '100%'
        });
    });

    function openEditModal(id, subject_id, dosen_id, nama_kelas, tahun_akademik, semester, status) {
        document.getElementById('edit-form').action = '/classes/' + id;
        $('#edit-subject').val(subject_id).trigger('change');
        $('#edit-dosen').val(dosen_id).trigger('change');
        document.getElementById('edit-nama').value = nama_kelas;
        document.getElementById('edit-tahun').value = tahun_akademik;
        document.getElementById('edit-semester').value = semester;
        document.getElementById('edit-status').value = status;
        document.getElementById('modal-edit').style.display = 'flex';
    }
</script>
@endif
@endsection
