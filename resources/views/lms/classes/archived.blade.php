@extends('layouts.lms')

@section('header_title', 'Kelas Arsip')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <div>
        <h2 style="margin: 0 0 0.25rem; font-size: 1.5rem; color: var(--text-main);">🗃️ Kelas Arsip</h2>
        <p style="margin: 0; font-size: 0.875rem; color: var(--text-muted);">Kelas yang sudah selesai perkuliahan dan diarsipkan. Konten bersifat read-only.</p>
    </div>
    <a href="{{ route('classes.index') }}" class="btn btn-outline" style="text-decoration: none; border-radius: 9999px;">
        ← My Classes
    </a>
</div>

@if(session('error'))
    <div class="flash-alert" style="background-color: #fef2f2; color: #991b1b; padding: 1rem; border: 1px solid #fecaca; border-radius: 8px; margin-bottom: 1.5rem;">{{ session('error') }}</div>
@endif

@if(session('success'))
    <div class="flash-alert" style="background-color: #f0fdf4; color: #166534; padding: 1rem; border: 1px solid #bbf7d0; border-radius: 8px; margin-bottom: 1.5rem;">{{ session('success') }}</div>
@endif

{{-- Info banner --}}
<div style="background: linear-gradient(135deg, #fef3c7, #fde68a); border: 1px solid #f59e0b; border-radius: 10px; padding: 1rem 1.25rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
    <span style="font-size: 1.4rem;">🗃️</span>
    <p style="margin: 0; font-size: 0.875rem; color: #92400e;">
        Kelas yang diarsipkan tidak dapat lagi menerima pengumpulan tugas atau perubahan konten. Data tersimpan permanen dan dapat dijadikan referensi. Dosen atau Admin dapat mengaktifkan kembali kapan saja.
    </p>
</div>

{{-- Search --}}
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-body">
        <form action="{{ route('classes.archived') }}" method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 1; min-width: 250px;">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">Cari Kelas</label>
                <input type="text" name="search" placeholder="Nama kelas atau mata kuliah..." value="{{ request('search') }}" style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 6px;">
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn">Cari</button>
                <a href="{{ route('classes.archived') }}" class="btn btn-outline" style="text-decoration: none; display: inline-block; text-align: center;">Reset</a>
            </div>
        </form>
    </div>
</div>

{{-- Class Cards --}}
<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(310px, 1fr)); gap: 1.5rem;">
    @forelse($classRooms as $class)
    <div class="card" style="margin-bottom: 0; opacity: 0.92; border: 1px solid #fde68a;">
        <div class="card-header" style="background: linear-gradient(135deg, #fef9c3, #fef3c7); display: flex; justify-content: space-between; align-items: center;">
            <span style="font-weight: 700; color: #78350f;">{{ $class->nama_kelas }}</span>
            <span style="font-size: 0.72rem; background: #fef3c7; color: #92400e; padding: 0.2rem 0.6rem; border-radius: 9999px; border: 1px solid #fcd34d; font-weight: 700;">
                🗃️ Arsip
            </span>
        </div>
        <div class="card-body">
            <h3 style="margin: 0 0 0.4rem; font-size: 1rem;">{{ optional($class->subject)->nama_subject ?? 'Unknown Subject' }}</h3>
            <p style="margin: 0 0 0.25rem; font-size: 0.8rem; color: var(--text-muted);">
                {{ $class->tahun_akademik }} &bull; {{ $class->semester }}
            </p>
            @php $firstDosen = $class->dosens()->first(); @endphp
            @if($firstDosen)
            <p style="margin: 0 0 1rem; font-size: 0.8rem; color: var(--text-muted);">
                👨‍🏫 {{ $firstDosen->name }}
            </p>
            @else
            <p style="margin: 0 0 1rem; font-size: 0.8rem; color: var(--text-muted);">👨‍🏫 –</p>
            @endif

            {{-- Stats --}}
            <div style="display: flex; gap: 0.75rem; margin-bottom: 1.25rem;">
                <div style="background: #f8fafc; padding: 0.4rem 0.75rem; border-radius: 6px; font-size: 0.78rem; text-align: center; flex: 1;">
                    <strong style="display: block; color: var(--text-main);">{{ $class->enrollments()->count() }}</strong>
                    <span style="color: var(--text-muted);">Mahasiswa</span>
                </div>
                <div style="background: #f8fafc; padding: 0.4rem 0.75rem; border-radius: 6px; font-size: 0.78rem; text-align: center; flex: 1;">
                    <strong style="display: block; color: var(--text-main);">{{ $class->topics()->count() }}</strong>
                    <span style="color: var(--text-muted);">Sesi</span>
                </div>
                <div style="background: #f8fafc; padding: 0.4rem 0.75rem; border-radius: 6px; font-size: 0.78rem; text-align: center; flex: 1;">
                    <strong style="display: block; color: var(--text-main);">{{ $class->assignments()->count() }}</strong>
                    <span style="color: var(--text-muted);">Tugas</span>
                </div>
            </div>

            <div style="display: flex; gap: 0.5rem;">
                <a href="{{ route('classes.show', $class) }}" class="btn btn-outline" style="flex: 1; text-align: center; font-size: 0.875rem;">
                    📂 Lihat Detail
                </a>
                @if(Auth::user()->hasRole(['admin', 'kaprodi', 'dosen', 'baak']))
                <form action="{{ route('classes.archive', $class) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Aktifkan kembali kelas ini?')">
                    @csrf
                    <button type="submit" class="btn" style="font-size: 0.875rem; background: #22c55e; color: white;" title="Aktifkan Kembali">
                        ✅
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div style="grid-column: 1 / -1; padding: 3rem; text-align: center; background: white; border-radius: 10px; border: 2px dashed #e5e7eb;">
        <span style="font-size: 3rem;">🗃️</span>
        <h3 style="margin: 1rem 0 0.5rem; color: var(--text-main);">Belum Ada Kelas Arsip</h3>
        <p style="color: var(--text-muted); margin: 0 0 1.5rem;">Kelas yang sudah selesai dan diarsipkan akan muncul di sini.</p>
        <a href="{{ route('classes.index') }}" class="btn btn-outline" style="text-decoration: none;">← Kembali ke My Classes</a>
    </div>
    @endforelse
</div>

<div style="margin-top: 1.5rem;">
    {{ $classRooms->links() }}
</div>

@endsection
