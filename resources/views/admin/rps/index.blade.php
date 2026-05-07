@extends('layouts.admin')

@section('title', 'Rencana Pembelajaran Semester (RPS)')

@section('header_left')
    <h1 style="font-size: 1.25rem; font-weight: 700; margin: 0;">RPS Management</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-header" style="flex-wrap: wrap; gap: 1rem;">
        <span>RPS List</span>
        <button onclick="openCreateModal()" class="btn btn-primary">Add RPS</button>
    </div>
    <div class="card-body" style="padding: 0;">
        <div style="overflow-x: auto;">
            <table id="rpsTable">
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Kurikulum</th>
                        <th>Versi</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rps as $rp)
                        <tr>
                            <td>
                                <div>{{ $rp->subject ? $rp->subject->nama_subject : '-' }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $rp->subject ? $rp->subject->kode_subject : '-' }}</div>
                            </td>
                            <td>{{ $rp->kurikulum ? $rp->kurikulum->nm_kurikulum : '-' }}</td>
                            <td>{{ $rp->versi }}</td>
                            <td>
                                @if($rp->status == 'Draft')
                                    <span style="padding: 0.25rem 0.5rem; background: #fef08a; color: #854d0e; border-radius: 9999px; font-size: 0.75rem;">Draft</span>
                                @elseif($rp->status == 'Aktif')
                                    <span style="padding: 0.25rem 0.5rem; background: #bbf7d0; color: #166534; border-radius: 9999px; font-size: 0.75rem;">Aktif</span>
                                @else
                                    <span style="padding: 0.25rem 0.5rem; background: #e5e7eb; color: #374151; border-radius: 9999px; font-size: 0.75rem;">Arsip</span>
                                @endif
                            </td>
                            <td>{{ $rp->created_at->format('d M Y') }}</td>
                            <td>
                                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                    <a href="{{ route('admin.rps.sessions', $rp->id) }}" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; background: #f3f4f6; color: #1f2937;">Manage Sessions</a>
                                    <button onclick="openEditModal('{{ $rp->id }}')" class="btn btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Edit</button>
                                    <a href="{{ route('admin.rps.export_pdf', $rp->id) }}" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; background: #e5e7eb; color: #1f2937;">PDF</a>
                                    <!-- <form action="{{ route('admin.rps.new_version', $rp->id) }}" method="POST" onsubmit="return confirm('Buat versi baru dari RPS ini? Versi lama akan diarsipkan.')">
                                        @csrf
                                        <button type="submit" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; background: #bae6fd; color: #0369a1;">New Version</button>
                                    </form> -->
                                    <!-- <button onclick="openCopyModal('{{ $rp->id }}')" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; background: #ddd6fe; color: #5b21b6;">Copy to Kurikulum</button> -->
                                    <form action="{{ route('admin.rps.destroy', $rp->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: var(--text-muted);">No RPS found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Create/Edit -->
<div id="rpsModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 500px; margin: 1rem;">
        <div class="card-header">
            <h3 id="modalTitle" style="margin: 0; font-size: 1.125rem;">Add RPS</h3>
            <button onclick="closeModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--text-muted);">&times;</button>
        </div>
        <div class="card-body">
            <form id="rpsForm" method="POST" action="{{ route('admin.rps.store') }}">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Subject <span style="color: red;">*</span></label>
                    <select name="subject_id" id="subject_id" required style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem;">
                        <option value="">Select Subject</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->kode_subject }} - {{ $subject->nama_subject }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Nomor RPS</label>
                    <input type="text" name="nomor_rps" id="nomor_rps" placeholder="e.g. RPS-INF-2024-001"
                           style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem;">
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Kurikulum <span style="color: red;">*</span></label>
                    <select name="kurikulum_id" id="kurikulum_id" required style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem;">
                        <option value="">Select Kurikulum</option>
                        @foreach($kurikulums as $kurikulum)
                            <option value="{{ $kurikulum->id }}">{{ $kurikulum->nm_kurikulum }} ({{ $kurikulum->tahun_akademik }})</option>
                        @endforeach
                    </select>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Tanggal Penyusunan</label>
                        <input type="date" name="tanggal_penyusunan" id="tanggal_penyusunan" style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Media Pembelajaran</label>
                        <input type="text" name="media_pembelajaran" id="media_pembelajaran" style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Pengembang RPS</label>
                        <input type="text" name="pengembang_rps" id="pengembang_rps" style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Dosen Pengampu</label>
                        <input type="text" name="dosen_pengampu" id="dosen_pengampu" style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem;">
                    </div>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Referensi</label>
                    <textarea name="referensi" id="referensi" rows="3" style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem;"></textarea>
                </div>
                
                <div style="margin-bottom: 1rem;" id="statusGroup">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Status <span style="color: red;">*</span></label>
                    <select name="status" id="status" required style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem;">
                        <option value="Draft">Draft</option>
                        <option value="Aktif">Aktif</option>
                        <option value="Arsip">Arsip</option>
                    </select>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                    <button type="button" onclick="closeModal()" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save RPS</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Copy RPS -->
<div id="copyModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 500px; margin: 1rem;">
        <div class="card-header">
            <h3 style="margin: 0; font-size: 1.125rem;">Copy RPS ke Kurikulum Baru</h3>
            <button onclick="closeCopyModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--text-muted);">&times;</button>
        </div>
        <div class="card-body">
            <form id="copyForm" method="POST" action="">
                @csrf
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Pilih Kurikulum Tujuan <span style="color: red;">*</span></label>
                    <select name="kurikulum_id" required style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem;">
                        <option value="">Select Kurikulum</option>
                        @foreach($kurikulums as $kurikulum)
                            <option value="{{ $kurikulum->id }}">{{ $kurikulum->nm_kurikulum }} ({{ $kurikulum->tahun_akademik }})</option>
                        @endforeach
                    </select>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                    <button type="button" onclick="closeCopyModal()" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">Copy RPS</button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
    function openCreateModal() {
        document.getElementById('modalTitle').innerText = 'Add RPS';
        document.getElementById('rpsForm').action = "{{ route('admin.rps.store') }}";
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('subject_id').value = '';
        document.getElementById('kurikulum_id').value = '';
        document.getElementById('nomor_rps').value = '';
        document.getElementById('tanggal_penyusunan').value = '';
        document.getElementById('referensi').value = '';
        document.getElementById('media_pembelajaran').value = '';
        document.getElementById('pengembang_rps').value = '';
        document.getElementById('dosen_pengampu').value = '';
        document.getElementById('status').value = 'Draft';
        document.getElementById('statusGroup').style.display = 'none'; // Sembunyikan status di Create
        document.getElementById('rpsModal').style.display = 'flex';
    }

    function openEditModal(id) {
        document.getElementById('modalTitle').innerText = 'Edit RPS';
        document.getElementById('rpsForm').action = `/admin/rps/${id}`;
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('statusGroup').style.display = 'block'; // Tampilkan status di Edit
        
        fetch(`/admin/rps/${id}/edit`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('subject_id').value = data.rps.subject_id;
                document.getElementById('kurikulum_id').value = data.rps.kurikulum_id;
                document.getElementById('nomor_rps').value = data.rps.nomor_rps || '';
                document.getElementById('tanggal_penyusunan').value = data.rps.tanggal_penyusunan || '';
                document.getElementById('referensi').value = data.rps.referensi || '';
                document.getElementById('media_pembelajaran').value = data.rps.media_pembelajaran || '';
                document.getElementById('pengembang_rps').value = data.rps.pengembang_rps || '';
                document.getElementById('dosen_pengampu').value = data.rps.dosen_pengampu || '';
                document.getElementById('status').value = data.rps.status;
                document.getElementById('rpsModal').style.display = 'flex';
            });
    }

    function closeModal() {
        document.getElementById('rpsModal').style.display = 'none';
    }

    function openCopyModal(id) {
        document.getElementById('copyForm').action = `/admin/rps/${id}/copy`;
        document.getElementById('copyModal').style.display = 'flex';
    }

    function closeCopyModal() {
        document.getElementById('copyModal').style.display = 'none';
    }
</script>
@endsection
@endsection
