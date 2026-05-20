@extends('layouts.admin')

@section('title', 'Manage Kategori Bahan Kajian - ' . $prodi->nama_prodi)

@section('header_left')
    <h1 style="font-size: 1.25rem; font-weight: 700; margin: 0;">Kategori BK: {{ $prodi->nama_prodi }}</h1>
@endsection

@section('content')
<div style="margin-bottom: 2rem;">
    <a href="{{ route('bahan_kajian.manage', $prodi->id) }}" style="color: var(--text-muted); font-size: 0.875rem;">← Back to Bahan Kajian</a>
</div>

@if($errors->any())
    <div class="alert alert-danger" style="background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
        <ul style="margin: 0; padding-left: 1.5rem;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif



<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem;">
    {{-- Form Section --}}
    <div>
        <div class="card">
            <div class="card-header">
                <span id="formTitle">Add New Category</span>
            </div>
            <div class="card-body">
                <form id="kategoriForm" action="{{ route('bahan_kajian.kategori.store', $prodi->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    
                    <div class="form-group">
                        <label class="form-label">Category Name</label>
                        <input type="text" name="nm_kategori" id="field_nm_kategori" class="form-control" required placeholder="e.g. Core Computing">
                    </div>
                    
                    <div style="margin-top: 1.5rem; display: flex; gap: 0.5rem;">
                        <button type="submit" class="btn btn-primary" style="flex: 1;">Save Category</button>
                        <button type="button" id="btnCancel" onclick="resetForm()" class="btn" style="display: none; background: #f3f4f6;">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- List Section --}}
    <div>
        <div class="card">
            <div class="card-header">
                <span>Existing Categories</span>
            </div>
            <div class="card-body" style="padding: 0;">
                <table>
                    <thead>
                        <tr>
                            <th>Category Name</th>
                            <th style="width: 150px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($prodi->kategoriBks as $kat)
                            <tr>
                                <td style="font-weight: 600;">{{ $kat->nm_kategori }}</td>
                                <td>
                                    <div style="display: flex; gap: 0.5rem;">
                                        <button onclick='editKategori(@json($kat))' class="btn btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Edit</button>
                                        <form action="{{ route('bahan_kajian.kategori.destroy', $kat->id) }}" method="POST" onsubmit="return confirm('Delete this category? This may fail if it is being used.')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" style="text-align: center; color: var(--text-muted); padding: 2rem;">No categories yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function editKategori(kat) {
        document.getElementById('formTitle').textContent = 'Edit Category';
        let updateUrl = "{{ route('bahan_kajian.kategori.update', ':id') }}";
        document.getElementById('kategoriForm').action = updateUrl.replace(':id', kat.id);
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('field_nm_kategori').value = kat.nm_kategori;
        document.getElementById('btnCancel').style.display = 'inline-block';
    }

    function resetForm() {
        document.getElementById('formTitle').textContent = 'Add New Category';
        document.getElementById('kategoriForm').action = "{{ route('bahan_kajian.kategori.store', $prodi->id) }}";
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('field_nm_kategori').value = '';
        document.getElementById('btnCancel').style.display = 'none';
    }
</script>
@endsection
