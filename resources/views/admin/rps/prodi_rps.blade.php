@extends('layouts.admin')

@section('title', 'RPS - ' . $prodi->nama_prodi)

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Restore calendar picker icon for date inputs with custom SVG */
    #tanggal_penyusunan::-webkit-calendar-picker-indicator {
        background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%236b7280' class='bi bi-calendar' viewBox='0 0 16 16'><path d='M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z'/></svg>") !important;
        background-repeat: no-repeat !important;
        background-position: center right !important;
        background-size: contain !important;
        display: inline-block !important;
        width: 16px !important;
        height: 16px !important;
        position: static !important;
        opacity: 1 !important;
        cursor: pointer !important;
    }

    /* Custom style for Select2 to match the theme */
    .select2-container .select2-selection--single {
        height: 41px !important;
        border: 1px solid #e5e7eb !important;
        border-radius: 0.375rem !important;
        display: flex !important;
        align-items: center !important;
        background-color: #fff !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: normal !important;
        color: #374151 !important;
        padding-left: 0.75rem !important;
        font-size: 0.875rem !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 41px !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
        right: 8px !important;
    }
    .select2-container {
        width: 100% !important;
        display: block !important;
        margin-top: 0.375rem !important;
    }
    .select2-dropdown {
        z-index: 99999 !important;
    }
</style>
@endsection

@section('header_left')
    <h1 style="font-size: 1.25rem; font-weight: 700; margin: 0;">RPS Management: {{ $prodi->nama_prodi }}</h1>
@endsection

@section('content')
<div style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
    <a href="{{ route('admin.rps.index') }}" class="btn btn-warning btn-sm">← Back to Prodi List</a>
</div>

@if(session('success'))
    <div class="alert alert-success" style="background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger" style="background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
        @foreach($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<!-- Filter Form -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-body">
        <form action="{{ route('admin.rps.prodi', $prodi->id) }}" method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 1; min-width: 250px;">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">{{ __('Search') }}</label>
                <input type="text" name="search" placeholder="{{ __('Search by Subject, Code, Nomor RPS or Kurikulum...') }}" value="{{ request('search') }}" style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 4px;">
            </div>
            <div style="display: flex; gap: 0.5rem; align-items: flex-end;">
                <button type="submit" class="btn btn-primary">{{ __('Search') }}</button>
                <a href="{{ route('admin.rps.prodi', $prodi->id) }}" class="btn" style="background: #f3f4f6; text-decoration: none; color: inherit; padding: 0.5rem 1rem; border-radius: 4px; display: inline-flex; align-items: center; justify-content: center; height: 38px;">{{ __('Reset') }}</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header" style="flex-wrap: wrap; gap: 1rem;">
        <span>RPS List in {{ $prodi->nama_prodi }}</span>
        <button onclick="openCreateModal()" class="btn btn-primary">Add RPS</button>
    </div>
    <div class="card-body" style="padding: 0;">
        <div style="overflow-x: auto;">
            <table id="rpsTable" class="table table-responsive-md">
                <thead>
                    <tr>
                        <th>{{ __('Subject') }}</th>
                        <th>{{ __('Kurikulum') }}</th>
                        <th>{{ __('Versi') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Created At') }}</th>
                        <th>{{ __('Actions') }}</th>
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
                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" style="padding: 0.35rem 0.75rem; font-size: 0.8125rem;">
                                        Aksi <i class="fa fa-chevron-down ms-1" style="font-size: 0.7rem;"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" style="box-shadow: 0 8px 24px rgba(0,0,0,0.12); border-radius: 8px; border: 1px solid #f0f0f0;">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.rps.sessions', $rp->id) }}" style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; padding: 0.5rem 1rem;">
                                                <i class="la la-tasks" style="font-size: 1.1rem; color: var(--primary);"></i> Manage Sessions
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="javascript:void(0);" onclick="openCloneModal('{{ $rp->id }}')" style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; padding: 0.5rem 1rem;">
                                                <i class="la la-copy" style="font-size: 1.1rem; color: #0284c7;"></i> Clone ke Prodi
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="javascript:void(0);" onclick="openEditModal('{{ $rp->id }}')" style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; padding: 0.5rem 1rem;">
                                                <i class="la la-edit" style="font-size: 1.1rem; color: #f59e0b;"></i> Edit
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.rps.export_pdf', $rp->id) }}" target="_blank" style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; padding: 0.5rem 1rem;">
                                                <i class="la la-file-pdf" style="font-size: 1.1rem; color: #6b7280;"></i> Export PDF
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider" style="margin: 0.25rem 0;"></li>
                                        <li>
                                            <form action="{{ route('admin.rps.destroy', $rp->id) }}" method="POST" class="swal-confirm-form" data-swal-msg="Are you sure you want to delete this RPS?">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger" style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; padding: 0.5rem 1rem; width: 100%; text-align: left; background: none; border: none;">
                                                    <i class="la la-trash" style="font-size: 1.1rem; color: #ef4444;"></i> Delete
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 2rem;">No RPS found in this Prodi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Create/Edit -->
<div id="rpsModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 500px; margin: 1rem; max-height: 90vh; overflow-y: auto;">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; background: white; z-index: 10;">
            <h3 id="modalTitle" style="margin: 0; font-size: 1.125rem;">Add RPS</h3>
            <button onclick="closeModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--text-muted);">&times;</button>
        </div>
        <div class="card-body">
            <form id="rpsForm" method="POST" action="{{ route('admin.rps.store') }}">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">{{ __('Subject') }} <span style="color: red;">*</span></label>
                    <select name="subject_id" id="subject_id" required style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem;">
                        <option value="">Select Subject</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->kode_subject }} - {{ $subject->nama_subject }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">{{ __('Nomor RPS') }} <span style="color: red;">*</span></label>
                    <input type="text" name="nomor_rps" id="nomor_rps" placeholder="{{ __('e.g. RPS-INF-2024-001') }}" required
                           style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem;">
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">{{ __('Kurikulum') }} <span style="color: red;">*</span></label>
                    <select name="kurikulum_id" id="kurikulum_id" required style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem;">
                        <option value="">Select Kurikulum</option>
                        @foreach($kurikulums as $kurikulum)
                            <option value="{{ $kurikulum->id }}">{{ $kurikulum->nm_kurikulum }} ({{ $kurikulum->tahun_akademik }})</option>
                        @endforeach
                    </select>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">{{ __('Tanggal Penyusunan') }} <span style="color: red;">*</span></label>
                        <input type="date" name="tanggal_penyusunan" id="tanggal_penyusunan" required style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">{{ __('Media Pembelajaran') }} <span style="color: red;">*</span></label>
                        <input type="text" name="media_pembelajaran" id="media_pembelajaran" required style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">{{ __('Pengembang RPS') }} <span style="color: red;">*</span></label>
                        <input type="text" name="pengembang_rps" id="pengembang_rps" required style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">{{ __('Dosen Pengampu') }} <span style="color: red;">*</span></label>
                        <input type="text" name="dosen_pengampu" id="dosen_pengampu" required style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem;">
                    </div>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">{{ __('Referensi') }} </label>
                    <textarea name="referensi" id="referensi" rows="3" style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem;"></textarea>
                </div>
                
                <div style="margin-bottom: 1rem;" id="statusGroup">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">{{ __('Status') }} <span style="color: red;">*</span></label>
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

<!-- Modal Clone to Prodi -->
<div id="cloneProdiModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 500px; margin: 1rem; max-height: 90vh; overflow-y: auto; border-radius: 12px; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; background: white; z-index: 10; border-bottom: 1px solid #eee; padding: 1.25rem 1.5rem;">
            <h3 style="margin: 0; font-size: 1.125rem; font-weight: 700; color: #111827;">Clone RPS ke Prodi Lain</h3>
            <button onclick="closeCloneModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--text-muted);">&times;</button>
        </div>
        <div class="card-body" style="padding: 1.5rem;">
            <form id="cloneProdiForm" method="POST" action="">
                @csrf
                <div style="margin-bottom: 1.25rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.875rem;">Prodi Tujuan <span style="color: red;">*</span></label>
                    <select name="target_prodi_id" id="target_prodi_id" required onchange="onTargetProdiChange()" class="form-control" style="width: 100%;">
                        <option value="">-- Pilih Prodi Tujuan --</option>
                        @foreach($allProdis as $p)
                            <option value="{{ $p->id }}">{{ $p->nama_prodi }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="margin-bottom: 1.25rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.875rem;">Mata Kuliah Tujuan <span style="color: red;">*</span></label>
                    <select name="target_subject_id" id="target_subject_id" required class="form-control" style="width: 100%;">
                        <option value="">-- Pilih Mata Kuliah --</option>
                    </select>
                </div>

                <div style="margin-bottom: 1.75rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.875rem;">Kurikulum Tujuan <span style="color: red;">*</span></label>
                    <select name="target_kurikulum_id" id="target_kurikulum_id" required class="form-control" style="width: 100%;">
                        <option value="">-- Pilih Kurikulum --</option>
                    </select>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                    <button type="button" onclick="closeCloneModal()" class="btn btn-secondary" style="padding: 0.5rem 1.25rem;">Batal</button>
                    <button type="submit" class="btn btn-primary" style="padding: 0.5rem 1.25rem;">Clone RPS</button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    const allProdisData = @json($allProdis);
    const allKurikulumsData = @json($kurikulums);

    function openCloneModal(rpsId) {
        let cloneUrl = "{{ route('admin.rps.clone_to_prodi', ':id') }}";
        document.getElementById('cloneProdiForm').action = cloneUrl.replace(':id', rpsId);
        
        $('#target_prodi_id').val('').trigger('change.select2');
        $('#target_subject_id').html('<option value="">-- Pilih Mata Kuliah --</option>').trigger('change.select2');
        $('#target_kurikulum_id').html('<option value="">-- Pilih Kurikulum --</option>').trigger('change.select2');
        
        document.getElementById('cloneProdiModal').style.display = 'flex';
    }

    function closeCloneModal() {
        document.getElementById('cloneProdiModal').style.display = 'none';
    }

    function onTargetProdiChange() {
        const prodiId = $('#target_prodi_id').val();
        const subjectSelect = $('#target_subject_id');
        const kurikulumSelect = $('#target_kurikulum_id');

        subjectSelect.html('<option value="">-- Pilih Mata Kuliah --</option>');
        kurikulumSelect.html('<option value="">-- Pilih Kurikulum --</option>');

        if (prodiId) {
            const selectedProdi = allProdisData.find(p => p.id === prodiId);
            if (selectedProdi) {
                // Populate Subjects (Filter out subjects that ALREADY have an RPS)
                if (selectedProdi.subjects && selectedProdi.subjects.length > 0) {
                    const availableSubjects = selectedProdi.subjects.filter(subj => !subj.rps || subj.rps.length === 0);

                    if (availableSubjects.length > 0) {
                        availableSubjects.forEach(subj => {
                            subjectSelect.append(new Option(`${subj.kode_subject} - ${subj.nama_subject}`, subj.id));
                        });
                    } else {
                        subjectSelect.append(new Option('-- Semua Mata Kuliah di Prodi ini sudah memiliki RPS --', '', false, false));
                    }
                } else {
                    subjectSelect.append(new Option('-- Belum ada Mata Kuliah di Prodi ini --', '', false, false));
                }

                // Populate Kurikulums
                const kurList = (selectedProdi.kurikulums && selectedProdi.kurikulums.length > 0) 
                    ? selectedProdi.kurikulums 
                    : allKurikulumsData;

                kurList.forEach(kur => {
                    kurikulumSelect.append(new Option(`${kur.nm_kurikulum} (${kur.tahun_akademik})`, kur.id));
                });
            }
        }

        subjectSelect.trigger('change.select2');
        kurikulumSelect.trigger('change.select2');
    }

    const existingRpsSubjects = {};
    @foreach($rps as $rp)
        if ("{{ $rp->subject_id }}" && !existingRpsSubjects["{{ $rp->subject_id }}"]) {
            existingRpsSubjects["{{ $rp->subject_id }}"] = {
                latest_version: {{ $rp->versi }},
                subject_name: @json($rp->subject ? $rp->subject->nama_subject : '')
            };
        }
    @endforeach

    $(document).ready(function() {
        if (typeof $.fn.selectpicker === 'function') {
            $('#subject_id, #target_prodi_id, #target_subject_id, #target_kurikulum_id').selectpicker('destroy');
        }
        $('#subject_id').select2({
            dropdownParent: $('#rpsModal'),
            width: '100%',
            placeholder: 'Select Subject'
        });

        $('#target_prodi_id').select2({
            dropdownParent: $('#cloneProdiModal'),
            width: '100%',
            placeholder: '-- Pilih Prodi Tujuan --'
        });

        $('#target_subject_id').select2({
            dropdownParent: $('#cloneProdiModal'),
            width: '100%',
            placeholder: '-- Pilih Mata Kuliah --'
        });

        $('#target_kurikulum_id').select2({
            dropdownParent: $('#cloneProdiModal'),
            width: '100%',
            placeholder: '-- Pilih Kurikulum --'
        });

        let skipRpsCheck = false;
        $('#rpsForm').on('submit', function(e) {
            if (skipRpsCheck) return true;
            
            const formMethod = $('#formMethod').val();
            if (formMethod === 'POST') {
                const selectedSubjectId = $('#subject_id').val();
                if (existingRpsSubjects[selectedSubjectId]) {
                    e.preventDefault();
                    const subjectInfo = existingRpsSubjects[selectedSubjectId];
                    const confirmMsg = `RPS matakuliah "${subjectInfo.subject_name}" ini sudah ada, apakah mau dibuat baru dengan versi yang berbeda?`;
                    
                    Swal.fire({
                        title: 'Konfirmasi',
                        text: confirmMsg,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            skipRpsCheck = true;
                            $('#rpsForm').submit();
                        }
                    });
                    return false;
                }
            }
        });
    });

    function openCreateModal() {
        document.getElementById('modalTitle').innerText = 'Add RPS';
        document.getElementById('rpsForm').action = "{{ route('admin.rps.store') }}";
        document.getElementById('formMethod').value = 'POST';
        
        $('#subject_id').val('').trigger('change');
        document.getElementById('kurikulum_id').value = '';
        document.getElementById('nomor_rps').value = '';
        document.getElementById('tanggal_penyusunan').value = '';
        document.getElementById('referensi').value = '';
        document.getElementById('media_pembelajaran').value = '';
        document.getElementById('pengembang_rps').value = '';
        document.getElementById('dosen_pengampu').value = '';
        document.getElementById('status').value = 'Draft';
        document.getElementById('statusGroup').style.display = 'none'; // Sembunyikan status di Create
        
        if (window.jQuery && typeof jQuery.fn.selectpicker === 'function') {
            $('#kurikulum_id, #status').selectpicker('refresh');
        }
        
        document.getElementById('rpsModal').style.display = 'flex';
    }

    function openEditModal(id) {
        document.getElementById('modalTitle').innerText = 'Edit RPS';
        let updateUrl = "{{ route('admin.rps.update', ':id') }}";
        document.getElementById('rpsForm').action = updateUrl.replace(':id', id);
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('statusGroup').style.display = 'block'; // Tampilkan status di Edit
        
        let editUrl = "{{ route('admin.rps.edit', ':id') }}";
        fetch(editUrl.replace(':id', id))
            .then(response => response.json())
            .then(data => {
                $('#subject_id').val(data.rps.subject_id).trigger('change');
                document.getElementById('kurikulum_id').value = data.rps.kurikulum_id;
                document.getElementById('nomor_rps').value = data.rps.nomor_rps || '';
                
                let dateVal = data.rps.tanggal_penyusunan || '';
                if (dateVal && dateVal.includes(' ')) {
                    dateVal = dateVal.split(' ')[0];
                } else if (dateVal && dateVal.includes('T')) {
                    dateVal = dateVal.split('T')[0];
                }
                document.getElementById('tanggal_penyusunan').value = dateVal;
                
                document.getElementById('referensi').value = data.rps.referensi || '';
                document.getElementById('media_pembelajaran').value = data.rps.media_pembelajaran || '';
                document.getElementById('pengembang_rps').value = data.rps.pengembang_rps || '';
                document.getElementById('dosen_pengampu').value = data.rps.dosen_pengampu || '';
                document.getElementById('status').value = data.rps.status;
                
                if (window.jQuery && typeof jQuery.fn.selectpicker === 'function') {
                    $('#kurikulum_id, #status').selectpicker('refresh');
                }
                
                document.getElementById('rpsModal').style.display = 'flex';
            });
    }

    function closeModal() {
        document.getElementById('rpsModal').style.display = 'none';
    }
</script>
@endsection

@endsection
