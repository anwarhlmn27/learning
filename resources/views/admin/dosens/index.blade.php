@extends('layouts.lms')

@section('header_title', 'Data Dosen')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <h2 style="margin: 0; font-size: 1.5rem; color: #111827;">Data Dosen</h2>
    <div style="display: flex; gap: 0.5rem;">
        <a href="{{ route('dosen.template') }}" class="btn" style="background: #f3f4f6; color: #374151; text-decoration: none; display: inline-flex; align-items: center; gap: 0.25rem;">
            <i>📥</i> Download Template
        </a>
        <button class="btn btn-primary" onclick="document.getElementById('modal-add').style.display = 'flex'">
            <i>➕</i> Add Dosen
        </button>
        <button class="btn btn-success" onclick="document.getElementById('modal-import').style.display = 'flex'">
            <i>📄</i> Import CSV
        </button>
    </div>
</div>

@if(session('error'))
    <div class="alert" style="background-color: #fef2f2; color: #991b1b; border: 1px solid #fecaca; margin-bottom: 1rem;">{{ session('error') }}</div>
@endif

@if(session('success'))
    <div class="alert" style="background-color: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; margin-bottom: 1rem;">{{ session('success') }}</div>
@endif

<!-- Filter Form -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-body">
        <form action="{{ route('dosen.index') }}" method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 1; min-width: 200px;">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Name, NIDN, or Email" value="{{ request('search') }}">
            </div>
            <div style="width: 200px;">
                <label class="form-label">Prodi</label>
                <select name="prodi" class="form-control">
                    <option value="">All Prodi</option>
                    @foreach($prodis as $prodi)
                        <option value="{{ $prodi->id }}" {{ request('prodi') == $prodi->id ? 'selected' : '' }}>{{ $prodi->nama_prodi }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('dosen.index') }}" class="btn" style="background: #f3f4f6; text-decoration: none; color: inherit;">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding: 0;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="padding: 1rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb; text-align: left;">NIDN</th>
                    <th style="padding: 1rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb; text-align: left;">Nama Dosen</th>
                    <th style="padding: 1rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb; text-align: left;">Gelar</th>
                    <th style="padding: 1rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb; text-align: left;">Email</th>
                    <th style="padding: 1rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb; text-align: left;">Prodi</th>
                    <th style="padding: 1rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb; text-align: left;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dosens as $dosen)
                <tr>
                    <td style="padding: 1rem; border-bottom: 1px solid #e5e7eb;">{{ $dosen->nidn }}</td>
                    <td style="padding: 1rem; border-bottom: 1px solid #e5e7eb;">{{ $dosen->nama_dosen }}</td>
                    <td style="padding: 1rem; border-bottom: 1px solid #e5e7eb;">{{ $dosen->gelar ?? '-' }}</td>
                    <td style="padding: 1rem; border-bottom: 1px solid #e5e7eb;">{{ $dosen->user->email ?? '-' }}</td>
                    <td style="padding: 1rem; border-bottom: 1px solid #e5e7eb;">{{ optional($dosen->prodi)->nama_prodi ?? '-' }}</td>
                    <td style="padding: 1rem; border-bottom: 1px solid #e5e7eb;">
                        <button class="btn" style="background: #eef2ff; color: #4f46e5; border: none; padding: 0.25rem 0.5rem; font-size: 0.75rem;" onclick="openEditModal('{{ $dosen->id }}', '{{ $dosen->nidn }}', '{{ $dosen->nama_dosen }}', '{{ $dosen->gelar }}', '{{ $dosen->user->email ?? '' }}', '{{ $dosen->prodi_id }}')">Edit</button>
                        <form action="{{ route('dosen.destroy', $dosen) }}" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus dosen ini? Akun login yang terkait juga akan dihapus.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="padding: 1rem; text-align: center; color: #6b7280;">Tidak ada data dosen.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <div style="padding: 1rem;">
            {{ $dosens->links() }}
        </div>
    </div>
</div>

<!-- Modal Add Dosen -->
<div id="modal-add" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 500px; margin: 1rem; max-height: 90vh; overflow-y: auto;">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; background: white; z-index: 10;">
            <h3 style="margin: 0; font-size: 1.1rem;">Add Data Dosen</h3>
            <button onclick="document.getElementById('modal-add').style.display = 'none'" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #6b7280;">&times;</button>
        </div>
        <div class="card-body">
            <form action="{{ route('dosen.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">NIDN</label>
                    <input type="text" name="nidn" class="form-control" required value="{{ old('nidn') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Nama Lengkap Dosen</label>
                    <input type="text" name="nama_dosen" class="form-control" required value="{{ old('nama_dosen') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Gelar (Opsional)</label>
                    <input type="text" name="gelar" class="form-control" value="{{ old('gelar') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Program Studi</label>
                    <select name="prodi_id" class="form-control" required>
                        <option value="">Pilih Prodi</option>
                        @foreach($prodis as $prodi)
                            <option value="{{ $prodi->id }}" {{ old('prodi_id') == $prodi->id ? 'selected' : '' }}>{{ $prodi->nama_prodi }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Password <span style="font-size: 0.75rem; color: #6b7280; font-weight: normal;">(Kosongkan untuk default: LmsHorizon$01)</span></label>
                    <input type="password" name="password" class="form-control">
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1.5rem;">
                    <button type="button" class="btn" style="background: #f3f4f6; color: #374151;" onclick="document.getElementById('modal-add').style.display = 'none'">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Dosen -->
<div id="modal-edit" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 500px; margin: 1rem; max-height: 90vh; overflow-y: auto;">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; background: white; z-index: 10;">
            <h3 style="margin: 0; font-size: 1.1rem;">Edit Data Dosen</h3>
            <button onclick="document.getElementById('modal-edit').style.display = 'none'" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #6b7280;">&times;</button>
        </div>
        <div class="card-body">
            <form id="edit-form" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label class="form-label">NIDN</label>
                    <input type="text" name="nidn" id="edit-nidn" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Nama Lengkap Dosen</label>
                    <input type="text" name="nama_dosen" id="edit-nama" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Gelar (Opsional)</label>
                    <input type="text" name="gelar" id="edit-gelar" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" id="edit-email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Program Studi</label>
                    <select name="prodi_id" id="edit-prodi" class="form-control" required>
                        <option value="">Pilih Prodi</option>
                        @foreach($prodis as $prodi)
                            <option value="{{ $prodi->id }}">{{ $prodi->nama_prodi }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1.5rem;">
                    <button type="button" class="btn" style="background: #f3f4f6; color: #374151;" onclick="document.getElementById('modal-edit').style.display = 'none'">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Import CSV -->
<div id="modal-import" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 500px; margin: 1rem;">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1.1rem;">Import Dosen via CSV</h3>
            <button onclick="document.getElementById('modal-import').style.display = 'none'" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #6b7280;">&times;</button>
        </div>
        <div class="card-body">
            <div style="background: #eff6ff; color: #1e40af; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; font-size: 0.875rem;">
                <strong>Format CSV Required:</strong><br>
                Column 1: <code>email</code><br>
                Column 2: <code>kode_prodi</code><br>
                Column 3: <code>nidn</code><br>
                Column 4: <code>nama_dosen</code><br>
                Column 5: <code>gelar</code><br>
                <em>Default password will be applied to all imported users: <strong>LmsHorizon$01</strong></em>
            </div>
            <form action="{{ route('dosen.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label class="form-label">Upload CSV File</label>
                    <input type="file" name="file" accept=".csv" class="form-control" required style="padding: 0.5rem;">
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1.5rem;">
                    <button type="button" class="btn" style="background: #f3f4f6; color: #374151;" onclick="document.getElementById('modal-import').style.display = 'none'">Cancel</button>
                    <button type="submit" class="btn btn-success">Import Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openEditModal(id, nidn, nama, gelar, email, prodi_id) {
        document.getElementById('edit-form').action = '/dosen/' + id;
        document.getElementById('edit-nidn').value = nidn;
        document.getElementById('edit-nama').value = nama;
        document.getElementById('edit-gelar').value = gelar;
        document.getElementById('edit-email').value = email;
        document.getElementById('edit-prodi').value = prodi_id;
        document.getElementById('modal-edit').style.display = 'flex';
    }
</script>
@endsection
