@extends('layouts.lms')

@section('header_title', __('Kelas Arsip'))

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <div>
        <h2 style="margin: 0 0 0.25rem; font-size: 1.5rem; color: var(--text-main);">🗃️ {{ __('Kelas Arsip') }}</h2>
        <p style="margin: 0; font-size: 0.875rem; color: var(--text-muted);">{{ __('Kelas yang sudah selesai perkuliahan dan diarsipkan. Konten bersifat read-only.') }}</p>
    </div>
    <a href="{{ route('classes.index') }}" class="btn btn-outline" style="text-decoration: none; border-radius: 9999px;">
        ← {{ __('My Classes') }}
    </a>
</div>



{{-- Info banner --}}
<div style="background: linear-gradient(135deg, #fef3c7, #fde68a); border: 1px solid #f59e0b; border-radius: 10px; padding: 1rem 1.25rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
    <span style="font-size: 1.4rem;">🗃️</span>
    <p style="margin: 0; font-size: 0.875rem; color: #92400e;">
        {{ __('Kelas yang diarsipkan tidak dapat lagi menerima pengumpulan tugas atau perubahan konten. Data tersimpan permanen dan dapat dijadikan referensi. BAAK, Kaprodi, Dekan, atau Admin dapat mengaktifkan kembali kapan saja.') }}
    </p>
</div>

{{-- Search --}}
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-body">
        <form action="{{ route('classes.archived') }}" method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 1; min-width: 250px;">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">{{ __('Cari Kelas') }}</label>
                <input type="text" name="search" placeholder="{{ __('Nama kelas atau mata kuliah...') }}" value="{{ request('search') }}" style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 6px;">
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-outline-primary btn-sm">{{ __('Cari') }}</button>
                <a href="{{ route('classes.archived') }}" class="btn btn-outline-secondary btn-sm" style="text-decoration: none; display: inline-block; text-align: center;">{{ __('Reset') }}</a>
            </div>
        </form>
    </div>
</div>

{{-- Class Cards --}}
<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(310px, 1fr)); gap: 1.5rem;">
    @forelse($classRooms as $class)
    <div class="card" style="margin-bottom: 0; display: flex; flex-direction: column; height: 100%; border: 1px solid #fde68a; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <div class="card-header" style="background: linear-gradient(135deg, #fef9c3, #fef3c7); display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.25rem; border-bottom: 1px solid #fde68a;">
            <span style="font-weight: 700; color: #78350f;">{{ $class->nama_kelas }}</span>
            <div style="display: flex; align-items: center; gap: 0.4rem;">
                @if(optional(optional($class->subject)->prodi)->nama_prodi)
                    <span style="font-size: 0.7rem; background: #e0e7ff; color: #3730a3; padding: 0.2rem 0.5rem; border-radius: 4px; font-weight: 600;">
                        {{ $class->subject->prodi->nama_prodi }}
                    </span>
                @endif
                <span style="font-size: 0.72rem; background: #fef3c7; color: #92400e; padding: 0.2rem 0.6rem; border-radius: 9999px; border: 1px solid #fcd34d; font-weight: 700;">
                    🗃️ {{ __('Arsip') }}
                </span>
            </div>
        </div>
        <div class="card-body" style="display: flex; flex-direction: column; justify-content: space-between; flex: 1; padding: 1.25rem 1.25rem 1.5rem 1.25rem;">
            <div>
                <h3 style="margin: 0 0 0.4rem; font-size: 1.05rem; font-weight: 700; color: #1e293b;">{{ optional($class->subject)->nama_subject ?? __('Unknown Subject') }}</h3>
                <p style="margin: 0 0 0.25rem; font-size: 0.85rem; color: var(--text-muted);">
                    {{ $class->tahun_akademik }} &bull; {{ $class->semester }}
                </p>
                @php $firstDosen = $class->dosens()->first(); @endphp
                @if($firstDosen)
                <p style="margin: 0 0 1rem; font-size: 0.85rem; color: var(--text-muted);">
                    👨‍🏫 {{ $firstDosen->dosen->nama_dosen ?? $firstDosen->name }}
                </p>
                @else
                <p style="margin: 0 0 1rem; font-size: 0.85rem; color: var(--text-muted);">👨‍🏫 –</p>
                @endif

                {{-- Stats --}}
                <div style="display: flex; gap: 0.75rem; margin-bottom: 1.25rem;">
                    <div style="background: #f8fafc; padding: 0.5rem 0.75rem; border-radius: 6px; font-size: 0.8rem; text-align: center; flex: 1; border: 1px solid #f1f5f9;">
                        <strong style="display: block; color: var(--text-main); font-size: 0.95rem;">{{ $class->enrollments()->count() }}</strong>
                        <span style="color: var(--text-muted); font-size: 0.75rem;">{{ __('Mahasiswa') }}</span>
                    </div>
                    <div style="background: #f8fafc; padding: 0.5rem 0.75rem; border-radius: 6px; font-size: 0.8rem; text-align: center; flex: 1; border: 1px solid #f1f5f9;">
                        <strong style="display: block; color: var(--text-main); font-size: 0.95rem;">{{ $class->topics()->count() }}</strong>
                        <span style="color: var(--text-muted); font-size: 0.75rem;">{{ __('Sesi') }}</span>
                    </div>
                    <div style="background: #f8fafc; padding: 0.5rem 0.75rem; border-radius: 6px; font-size: 0.8rem; text-align: center; flex: 1; border: 1px solid #f1f5f9;">
                        <strong style="display: block; color: var(--text-main); font-size: 0.95rem;">{{ $class->assignments()->count() }}</strong>
                        <span style="color: var(--text-muted); font-size: 0.75rem;">{{ __('Tugas') }}</span>
                    </div>
                </div>
            </div>

            <div style="display: flex; gap: 0.35rem; margin-top: auto; align-items: center;">
                <a href="{{ route('classes.show', $class) }}" class="btn btn-primary btn-xs" style="flex: 1; text-align: center; display: inline-flex; align-items: center; justify-content: center; height: 32px; font-weight: 600; font-size: 0.78rem; border-radius: 6px; gap: 0.25rem; text-decoration: none; padding: 0 0.5rem;">
                    📂 {{ __('Lihat Detail') }}
                </a>
                @if(Auth::user()->hasRole(['admin', 'kaprodi', 'baak', 'dosen']) || Auth::user()->can('create-classes'))
                <button type="button" class="btn btn-outline-primary btn-xs" data-bs-toggle="modal" data-bs-target="#modalCloneClass_{{ $class->id }}" style="height: 32px; padding: 0 0.55rem; display: inline-flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.78rem; border-radius: 6px; gap: 0.25rem;" title="{{ __('Kloning / Salin Seluruh Materi ke Kelas Baru') }}">
                    <span>📋</span> <span>{{ __('Kloning') }}</span>
                </button>
                @endif
                @if(Auth::user()->hasRole(['admin', 'rektor', 'dekan', 'kaprodi', 'baak']))
                <form action="{{ route('classes.archive', $class) }}" method="POST" style="margin: 0; display: inline-flex;" class="swal-confirm-form" data-swal-msg="{{ __('Aktifkan kembali kelas ini?') }}">
                    @csrf
                    <button type="submit" class="btn btn-xs" style="height: 32px; padding: 0 0.55rem; display: inline-flex; align-items: center; justify-content: center; background: #16a34a; color: white; border: none; border-radius: 6px; font-weight: 600; font-size: 0.78rem; gap: 0.25rem;" title="{{ __('Aktifkan Kembali') }}">
                        <span>✅</span> <span>{{ __('Aktifkan') }}</span>
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Clone Class -->
    <div class="modal fade" id="modalCloneClass_{{ $class->id }}" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content" style="border-radius: 12px; overflow: hidden; border: none; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2);">
                <div class="modal-header bg-primary text-white px-4 py-3">
                    <h5 class="modal-title font-w700 text-white" style="font-size: 1.05rem; display: flex; align-items: center; gap: 0.5rem;">
                        <span>📋</span> {{ __('Kloning Materi Kelas ke Kelas Baru') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('classes.clone_to_new', $class) }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="alert alert-info d-flex align-items-center gap-2 mb-3" style="font-size: 0.85rem; border-radius: 8px;">
                            <span>ℹ️</span>
                            <div>
                                {{ __('Sistem akan menyalin 14 Sesi, Modul Materi, Template Tugas, dan Kuis dari kelas ini ke kelas baru. Data mahasiswa lama dan nilai tidak akan disalin.') }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-w600 text-dark">{{ __('Mata Kuliah Sumber') }}</label>
                            <input type="text" class="form-control" value="{{ optional($class->subject)->nama_subject }} ({{ $class->nama_kelas }})" disabled style="background: #f1f5f9; border-radius: 6px;">
                        </div>

                        <div class="mb-3">
                            <label for="nama_kelas_{{ $class->id }}" class="form-label font-w600 text-dark">{{ __('Nama Kelas Baru') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_kelas_{{ $class->id }}" name="nama_kelas" value="{{ $class->nama_kelas }} (Baru)" required style="border-radius: 6px;">
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label for="tahun_akademik_{{ $class->id }}" class="form-label font-w600 text-dark">{{ __('Tahun Akademik') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="tahun_akademik_{{ $class->id }}" name="tahun_akademik" placeholder="Contoh: 2027/2028" value="{{ date('Y') }}/{{ date('Y') + 1 }}" required style="border-radius: 6px;">
                            </div>
                            <div class="col-md-6">
                                <label for="semester_{{ $class->id }}" class="form-label font-w600 text-dark">{{ __('Semester') }} <span class="text-danger">*</span></label>
                                <select class="form-select" id="semester_{{ $class->id }}" name="semester" required style="border-radius: 6px;">
                                    <option value="Ganjil" {{ $class->semester === 'Ganjil' ? 'selected' : '' }}>{{ __('Ganjil') }}</option>
                                    <option value="Genap" {{ $class->semester === 'Genap' ? 'selected' : '' }}>{{ __('Genap') }}</option>
                                    <option value="Antara" {{ $class->semester === 'Antara' ? 'selected' : '' }}>{{ __('Antara') }}</option>
                                </select>
                            </div>
                        </div>

                        @if(Auth::user()->hasRole(['admin', 'kaprodi', 'baak']) && isset($dosens) && $dosens->isNotEmpty())
                        <div class="mb-2">
                            <label for="dosen_id_{{ $class->id }}" class="form-label font-w600 text-dark">{{ __('Dosen Pengampu (Opsional)') }}</label>
                            <select class="form-select" id="dosen_id_{{ $class->id }}" name="dosen_id" style="border-radius: 6px;">
                                <option value="">-- {{ __('Tetap Sama dengan Kelas Sumber') }} --</option>
                                @foreach($dosens as $d)
                                    <option value="{{ $d->id }}">{{ $d->nama_dosen }} ({{ optional($d->prodi)->nama_prodi ?? 'Umum' }})</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                    </div>
                    <div class="modal-footer px-4 py-3 bg-light" style="border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
                        <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal" style="border-radius: 6px;">{{ __('Batal') }}</button>
                        <button type="submit" class="btn btn-primary btn-sm px-4 font-w600" style="border-radius: 6px;">
                            📋 {{ __('Buat & Kloning Kelas') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div style="grid-column: 1 / -1; padding: 3rem; text-align: center; background: white; border-radius: 10px; border: 2px dashed #e5e7eb;">
        <span style="font-size: 3rem;">🗃️</span>
        <h3 style="margin: 1rem 0 0.5rem; color: var(--text-main);">{{ __('Belum Ada Kelas Arsip') }}</h3>
        <p style="color: var(--text-muted); margin: 0 0 1.5rem;">{{ __('Kelas yang sudah selesai dan diarsipkan akan muncul di sini.') }}</p>
        <a href="{{ route('classes.index') }}" class="btn btn-outline-primary" style="text-decoration: none;">← {{ __('Kembali ke My Classes') }}</a>
    </div>
    @endforelse
</div>

<div style="margin-top: 1.5rem;">
    {{ $classRooms->links() }}
</div>

@endsection
