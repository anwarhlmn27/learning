@extends('layouts.lms')

@section('header_title', 'My Classes')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <h2 style="margin: 0; font-size: 1.5rem; color: var(--text-main);">Class Enrollment</h2>
    @if(Auth::user()->hasRole(['admin', 'kaprodi']))
    <button class="btn" onclick="document.getElementById('modal-add').style.display = 'flex'">
        <i>➕</i> Add New Class
    </button>
    @endif
</div>

@if(session('error'))
    <div style="background-color: #fef2f2; color: #991b1b; padding: 1rem; border: 1px solid #fecaca; border-radius: var(--radius); margin-bottom: 1.5rem;">{{ session('error') }}</div>
@endif

@if(session('success'))
    <div style="background-color: #f0fdf4; color: #166534; padding: 1rem; border: 1px solid #bbf7d0; border-radius: var(--radius); margin-bottom: 1.5rem;">{{ session('success') }}</div>
@endif

<!-- Filter Form -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-body">
        <form action="{{ route('classes.index') }}" method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 1; min-width: 250px;">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">Search</label>
                <input type="text" name="search" placeholder="Search by class name, subject, dosen..." value="{{ request('search') }}" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 4px;">
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn">Filter</button>
                <a href="{{ route('classes.index') }}" class="btn btn-outline" style="text-decoration: none; display: inline-block; text-align: center;">Reset</a>
            </div>
        </form>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem;">
    @forelse($classRooms as $class)
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header" style="background-color: #f8fafc; display: flex; justify-content: space-between;">
            <span style="font-weight: 700; color: var(--primary);">{{ $class->nama_kelas }}</span>
            <span style="font-size: 0.75rem; background: {{ $class->is_active ? '#dcfce7' : '#f3f4f6' }}; color: {{ $class->is_active ? '#166534' : '#6b7280' }}; padding: 0.25rem 0.5rem; border-radius: 9999px;">
                {{ $class->is_active ? 'Active' : 'Inactive' }}
            </span>
        </div>
        <div class="card-body">
            <h3 style="margin: 0 0 0.5rem 0; font-size: 1.1rem;">{{ optional($class->subject)->nama_subject ?? 'Unknown Subject' }}</h3>
            <p style="margin: 0 0 1rem 0; font-size: 0.875rem; color: var(--text-muted);">
                <i>👨‍🏫</i> {{ optional($class->dosen)->nama_dosen ?? 'Unknown Lecturer' }}
            </p>
            
            <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem; font-size: 0.875rem;">
                <div style="background: #f1f5f9; padding: 0.5rem; border-radius: 4px; flex: 1; text-align: center;">
                    <strong style="display: block; color: var(--text-main);">Tahun Akademik</strong>
                    <span style="color: var(--text-muted);">{{ $class->tahun_akademik }}</span>
                </div>
                <div style="background: #f1f5f9; padding: 0.5rem; border-radius: 4px; flex: 1; text-align: center;">
                    <strong style="display: block; color: var(--text-main);">Semester</strong>
                    <span style="color: var(--text-muted);">{{ $class->semester }}</span>
                </div>
            </div>

            <div style="display: flex; gap: 0.5rem;">
                <a href="{{ route('classes.show', $class) }}" class="btn" style="flex: 1; text-align: center;">
                    Manage Enrollment
                </a>
                
                @if(Auth::user()->hasRole(['admin', 'kaprodi']))
                <button class="btn btn-outline" style="padding: 0.5rem;" onclick="openEditModal('{{ $class->id }}', '{{ $class->subject_id }}', '{{ $class->dosen_id }}', '{{ $class->nama_kelas }}', '{{ $class->tahun_akademik }}', '{{ $class->semester }}', {{ $class->is_active }})" title="Edit Class">
                    ✏️
                </button>
                <form action="{{ route('classes.destroy', $class) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kelas ini beserta seluruh data enrollments di dalamnya?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline" style="padding: 0.5rem; color: #dc2626; border-color: #fecaca;" title="Delete Class">
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
        <h3 style="margin: 1rem 0 0.5rem 0; color: var(--text-main);">Belum Ada Kelas</h3>
        <p style="color: var(--text-muted); margin: 0;">Tidak ada data kelas yang dapat ditampilkan.</p>
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
            <h3 style="margin: 0; font-size: 1.1rem;">Add New Class</h3>
            <button onclick="document.getElementById('modal-add').style.display = 'none'" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #6b7280;">&times;</button>
        </div>
        <div class="card-body">
            <form action="{{ route('classes.store') }}" method="POST">
                @csrf
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">Mata Kuliah <span style="color: red;">*</span></label>
                    <select name="subject_id" required style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 4px;">
                        <option value="">-- Pilih Mata Kuliah --</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>{{ $subject->kode_subject }} - {{ $subject->nama_subject }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">Dosen Pengampu <span style="color: red;">*</span></label>
                    <select name="dosen_id" required style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 4px;">
                        <option value="">-- Pilih Dosen --</option>
                        @foreach($dosens as $dosen)
                            <option value="{{ $dosen->id }}" {{ old('dosen_id') == $dosen->id ? 'selected' : '' }}>{{ $dosen->nama_dosen }} ({{ $dosen->nidn }})</option>
                        @endforeach
                    </select>
                </div>
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">Nama Kelas (e.g. Kelas A) <span style="color: red;">*</span></label>
                    <input type="text" name="nama_kelas" required value="{{ old('nama_kelas') }}" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 4px;">
                </div>
                <div style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                    <div style="flex: 1;">
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">Tahun Akademik <span style="color: red;">*</span></label>
                        <input type="text" name="tahun_akademik" placeholder="e.g. 2023/2024" required value="{{ old('tahun_akademik') }}" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 4px;">
                    </div>
                    <div style="flex: 1;">
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">Semester <span style="color: red;">*</span></label>
                        <select name="semester" required style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 4px;">
                            <option value="Ganjil" {{ old('semester') == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                            <option value="Genap" {{ old('semester') == 'Genap' ? 'selected' : '' }}>Genap</option>
                            <option value="Antara" {{ old('semester') == 'Antara' ? 'selected' : '' }}>Antara</option>
                        </select>
                    </div>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1.5rem;">
                    <button type="button" class="btn btn-outline" onclick="document.getElementById('modal-add').style.display = 'none'">Cancel</button>
                    <button type="submit" class="btn">Save Class</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Class -->
<div id="modal-edit" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 500px; margin: 1rem; max-height: 90vh; overflow-y: auto;">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; background: white; z-index: 10;">
            <h3 style="margin: 0; font-size: 1.1rem;">Edit Class</h3>
            <button onclick="document.getElementById('modal-edit').style.display = 'none'" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #6b7280;">&times;</button>
        </div>
        <div class="card-body">
            <form id="edit-form" method="POST">
                @csrf
                @method('PUT')
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">Mata Kuliah <span style="color: red;">*</span></label>
                    <select name="subject_id" id="edit-subject" required style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 4px;">
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->kode_subject }} - {{ $subject->nama_subject }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">Dosen Pengampu <span style="color: red;">*</span></label>
                    <select name="dosen_id" id="edit-dosen" required style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 4px;">
                        @foreach($dosens as $dosen)
                            <option value="{{ $dosen->id }}">{{ $dosen->nama_dosen }} ({{ $dosen->nidn }})</option>
                        @endforeach
                    </select>
                </div>
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">Nama Kelas (e.g. Kelas A) <span style="color: red;">*</span></label>
                    <input type="text" name="nama_kelas" id="edit-nama" required style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 4px;">
                </div>
                <div style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                    <div style="flex: 1;">
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">Tahun Akademik <span style="color: red;">*</span></label>
                        <input type="text" name="tahun_akademik" id="edit-tahun" required style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 4px;">
                    </div>
                    <div style="flex: 1;">
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">Semester <span style="color: red;">*</span></label>
                        <select name="semester" id="edit-semester" required style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 4px;">
                            <option value="Ganjil">Ganjil</option>
                            <option value="Genap">Genap</option>
                            <option value="Antara">Antara</option>
                        </select>
                    </div>
                </div>
                <div style="margin-bottom: 1rem;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; font-weight: 500; cursor: pointer;">
                        <input type="checkbox" name="is_active" id="edit-active" value="1"> Kelas Aktif
                    </label>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1.5rem;">
                    <button type="button" class="btn btn-outline" onclick="document.getElementById('modal-edit').style.display = 'none'">Cancel</button>
                    <button type="submit" class="btn">Update Class</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openEditModal(id, subject_id, dosen_id, nama_kelas, tahun_akademik, semester, is_active) {
        document.getElementById('edit-form').action = '/classes/' + id;
        document.getElementById('edit-subject').value = subject_id;
        document.getElementById('edit-dosen').value = dosen_id;
        document.getElementById('edit-nama').value = nama_kelas;
        document.getElementById('edit-tahun').value = tahun_akademik;
        document.getElementById('edit-semester').value = semester;
        document.getElementById('edit-active').checked = is_active;
        document.getElementById('modal-edit').style.display = 'flex';
    }
</script>
@endif
@endsection
