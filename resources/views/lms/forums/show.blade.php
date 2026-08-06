@extends('layouts.lms')

@section('header_title', $forum->title)

@section('content')
<div style="max-width: 900px; margin: 0 auto;">

    <!-- Top Navigation & Class Info Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <a href="{{ route('classes.show', $class) }}" class="btn btn-outline" style="padding: 0.4rem 0.85rem; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 0.4rem;">
                <i>←</i> Kembali ke Kelas
            </a>
            <div>
                <h2 style="margin: 0; font-size: 1.35rem; font-weight: 700; color: var(--text-primary);">{{ $forum->title }}</h2>
                <span style="font-size: 0.8rem; color: var(--text-muted);">
                    {{ $class->nama_kelas }} &mdash; {{ optional($class->subject)->nama_subject }}
                    @if($topic)
                        &bull; <span class="badge" style="background: #e0e7ff; color: #4f46e5; font-size: 0.75rem; padding: 0.2rem 0.5rem;">Sesi {{ $topic->session_number }}</span>
                    @endif
                </span>
            </div>
        </div>
    </div>

    <!-- Forum Context / Instructions Card -->
    <div class="card" style="margin-bottom: 1.5rem; border-radius: var(--radius-lg); border: 1px solid var(--border-color); background: var(--card-bg, #ffffff); box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <div class="card-body" style="padding: 1.5rem;">
            <div style="display: flex; align-items: flex-start; gap: 1rem;">
                <div style="width: 42px; height: 42px; border-radius: 10px; background: #f5f3ff; color: #8b5cf6; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; flex-shrink: 0;">
                    💬
                </div>
                <div style="flex-grow: 1;">
                    <h3 style="margin: 0 0 0.5rem 0; font-size: 1.15rem; font-weight: 700; color: var(--text-primary);">Panduan & Topik Diskusi</h3>
                    <div style="background: #f8fafc; padding: 1rem 1.25rem; border-radius: var(--radius-md); border-left: 4px solid #8b5cf6; font-size: 0.95rem; line-height: 1.6; color: var(--text-primary);">
                        {!! nl2br(e($forum->context)) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add New Post / Reply Form Card -->
    <div class="card" style="margin-bottom: 2rem; border-radius: var(--radius-lg); border: 1px solid var(--border-color); background: var(--card-bg, #ffffff); box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <div class="card-header" style="background: transparent; border-bottom: 1px solid var(--border-color); padding: 1rem 1.5rem;">
            <h4 style="margin: 0; font-size: 1rem; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;">
                ✏️ Tulis Tanggapan / Diskusi Baru
            </h4>
        </div>
        <div class="card-body" style="padding: 1.5rem;">
            <form action="{{ route('classes.forums.store_post', [$class, $forum]) }}" method="POST">
                @csrf
                <div style="margin-bottom: 1rem;">
                    <textarea name="content" rows="4" required 
                        placeholder="Tuliskan komentar, tanggapan, atau pertanyaan Anda tentang materi sesi ini..."
                        style="width: 100%; padding: 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.925rem; line-height: 1.5; resize: vertical; outline: none; transition: border-color 0.2s;"></textarea>
                    @error('content')
                        <span style="color: #dc2626; font-size: 0.8rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                    @enderror
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 0.8rem; color: var(--text-muted);">
                        Login sebagai: <strong>{{ Auth::user()->name }}</strong> ({{ ucfirst(Auth::user()->roles->first()?->name ?? 'User') }})
                    </span>
                    <button type="submit" class="btn" style="padding: 0.55rem 1.25rem; font-size: 0.9rem; font-weight: 600; background: var(--primary); color: white; border-radius: var(--radius-md); display: inline-flex; align-items: center; gap: 0.4rem;">
                        💬 Kirim Diskusi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Discussion Stream / Feed -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <h3 style="margin: 0; font-size: 1.1rem; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;">
            <span>💬 Forum Diskusi</span>
            <span style="background: #e0e7ff; color: var(--primary); font-size: 0.8rem; font-weight: 700; padding: 0.15rem 0.55rem; border-radius: 12px;">
                {{ $forum->posts->count() }}
            </span>
        </h3>
    </div>

    <div style="display: flex; flex-direction: column; gap: 1rem; margin-bottom: 3rem;">
        @forelse($forum->posts as $post)
            @php
                $isAuthor = ($post->user_id === Auth::id());
                $userRole = $post->user->roles->first()?->name ?? 'user';
                $roleBadgeColor = match(strtolower($userRole)) {
                    'dosen' => 'background: #dbeafe; color: #1e40af;',
                    'admin', 'kaprodi' => 'background: #fef3c7; color: #92400e;',
                    'mahasiswa' => 'background: #f3e8ff; color: #6b21a8;',
                    default => 'background: #f1f5f9; color: #475569;'
                };
            @endphp
            <div class="card" style="margin-bottom: 0; border-radius: var(--radius-lg); border: 1px solid var(--border-color); background: var(--card-bg, #ffffff); box-shadow: 0 1px 2px rgba(0,0,0,0.03);">
                <div class="card-body" style="padding: 1.25rem;">
                    <!-- Post Header -->
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.85rem;">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <!-- Avatar -->
                            <div style="width: 38px; height: 38px; border-radius: 50%; background: var(--primary, #4f46e5); color: white; font-weight: 700; display: flex; align-items: center; justify-content: center; font-size: 0.95rem;">
                                {{ strtoupper(substr($post->user->name ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <strong style="font-size: 0.95rem; color: var(--text-primary);">{{ $post->user->name ?? 'Pengguna' }}</strong>
                                    <span style="{{ $roleBadgeColor }} font-size: 0.7rem; font-weight: 700; padding: 0.1rem 0.45rem; border-radius: 4px; text-transform: uppercase;">
                                        {{ $userRole }}
                                    </span>
                                </div>
                                <span style="font-size: 0.75rem; color: var(--text-muted);">
                                    {{ $post->created_at->diffForHumans() }} &bull; {{ $post->created_at->format('d M Y - H:i') }}
                                </span>
                            </div>
                        </div>

                        <!-- Action / Delete -->
                        @if($isAuthor || Auth::user()->hasRole(['admin', 'kaprodi']) || $class->users()->where('user_id', Auth::id())->exists())
                            <form action="{{ route('classes.forums.destroy_post', [$class, $forum, $post]) }}" method="POST" style="margin: 0;" class="swal-confirm-form" data-swal-msg="Hapus tanggapan diskusi ini?">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background: none; border: none; padding: 0.25rem 0.5rem; color: #dc2626; cursor: pointer; font-size: 0.85rem; border-radius: 4px;" title="Hapus Tanggapan">
                                    🗑️ Hapus
                                </button>
                            </form>
                        @endif
                    </div>

                    <!-- Post Body / Content -->
                    <div style="font-size: 0.925rem; line-height: 1.6; color: var(--text-primary); white-space: pre-line; word-break: break-word; padding-left: 3.1rem;">
                        {!! nl2br(e($post->content)) !!}
                    </div>
                </div>
            </div>
        @empty
            <div style="text-align: center; padding: 3rem 1rem; background: #f8fafc; border-radius: var(--radius-lg); border: 2px dashed var(--border-color);">
                <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">💬</div>
                <h4 style="margin: 0 0 0.25rem 0; font-size: 1.05rem; font-weight: 700; color: var(--text-primary);">Belum Ada Diskusi</h4>
                <p style="margin: 0; font-size: 0.85rem; color: var(--text-muted);">Jadilah yang pertama mengirimkan pertanyaan atau komentar untuk sesi perkuliahan ini!</p>
            </div>
        @endforelse
    </div>

</div>
@endsection
