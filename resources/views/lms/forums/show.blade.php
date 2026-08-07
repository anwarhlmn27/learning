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
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
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
                {{ $totalPostsCount ?? $forum->posts->count() }}
            </span>
        </h3>
    </div>

    <div style="display: flex; flex-direction: column; gap: 1.25rem; margin-bottom: 3rem;">
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
            <div class="card" style="margin-bottom: 0; border-radius: var(--radius-lg); border: 1px solid var(--border-color); background: var(--card-bg, #ffffff); box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                <div class="card-body" style="padding: 1.25rem;">
                    <!-- Post Header -->
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.85rem;">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <!-- Avatar -->
                            <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--primary, #4f46e5); color: white; font-weight: 700; display: flex; align-items: center; justify-content: center; font-size: 1rem;">
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
                            <form action="{{ route('classes.forums.destroy_post', [$class, $forum, $post]) }}" method="POST" style="margin: 0;" class="swal-confirm-form" data-swal-msg="Hapus tanggapan diskusi ini beserta alasannya?">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background: none; border: none; padding: 0.25rem 0.5rem; color: #dc2626; cursor: pointer; font-size: 0.85rem; border-radius: 4px;" title="Hapus Tanggapan">
                                    🗑️ Hapus
                                </button>
                            </form>
                        @endif
                    </div>

                    <!-- Post Body / Content -->
                    <div style="font-size: 0.925rem; line-height: 1.6; color: var(--text-primary); white-space: pre-line; word-break: break-word; padding-left: 3.1rem; margin-bottom: 0.85rem;">
                        {!! nl2br(e($post->content)) !!}
                    </div>

                    <!-- Post Action Bar (Reply button) -->
                    <div style="padding-left: 3.1rem; display: flex; align-items: center; gap: 1rem;">
                        <button type="button" onclick="toggleReplyForm('reply-form-{{ $post->id }}')" 
                            style="background: #f1f5f9; border: 1px solid var(--border-color); color: var(--text-primary); padding: 0.3rem 0.75rem; border-radius: var(--radius-md); font-size: 0.825rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 0.35rem; transition: all 0.2s;"
                            onmouseover="this.style.background='#e2e8f0';" onmouseout="this.style.background='#f1f5f9';">
                            ↩️ Balas
                        </button>
                        @if($post->replies->count() > 0)
                            <span style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600;">
                                💬 {{ $post->replies->count() }} Balasan
                            </span>
                        @endif
                    </div>

                    <!-- Collapsible Reply Form -->
                    <div id="reply-form-{{ $post->id }}" style="display: none; margin-left: 3.1rem; margin-top: 1rem; padding: 1rem; background: #f8fafc; border-radius: var(--radius-md); border: 1px solid #e2e8f0;">
                        <form action="{{ route('classes.forums.store_post', [$class, $forum]) }}" method="POST">
                            @csrf
                            <input type="hidden" name="parent_id" value="{{ $post->id }}">
                            <div style="margin-bottom: 0.75rem;">
                                <label style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-bottom: 0.35rem;">
                                    Membalas ke: <strong>{{ $post->user->name ?? 'Pengguna' }}</strong>
                                </label>
                                <textarea name="content" rows="3" required
                                    placeholder="Tuliskan balasan Anda..."
                                    style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.875rem; line-height: 1.5; resize: vertical; outline: none; background: #ffffff;"></textarea>
                            </div>
                            <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                                <button type="button" onclick="toggleReplyForm('reply-form-{{ $post->id }}')" 
                                    style="padding: 0.4rem 0.85rem; font-size: 0.825rem; background: transparent; border: 1px solid var(--border-color); border-radius: var(--radius-md); color: var(--text-muted); cursor: pointer;">
                                    Batal
                                </button>
                                <button type="submit" class="btn" style="padding: 0.4rem 1rem; font-size: 0.825rem; font-weight: 600; background: var(--primary); color: white; border-radius: var(--radius-md); display: inline-flex; align-items: center; gap: 0.35rem;">
                                    💬 Kirim Balasan
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Replies Stream (Nested) -->
                    @if($post->replies->count() > 0)
                        <div style="margin-left: 3.1rem; margin-top: 1rem; border-left: 3px solid #e2e8f0; padding-left: 1rem; display: flex; flex-direction: column; gap: 0.85rem;">
                            @foreach($post->replies as $reply)
                                @php
                                    $isReplyAuthor = ($reply->user_id === Auth::id());
                                    $replyUserRole = $reply->user->roles->first()?->name ?? 'user';
                                    $replyRoleBadge = match(strtolower($replyUserRole)) {
                                        'dosen' => 'background: #dbeafe; color: #1e40af;',
                                        'admin', 'kaprodi' => 'background: #fef3c7; color: #92400e;',
                                        'mahasiswa' => 'background: #f3e8ff; color: #6b21a8;',
                                        default => 'background: #f1f5f9; color: #475569;'
                                    };
                                @endphp
                                <div style="background: #f8fafc; border: 1px solid #edf2f7; border-radius: var(--radius-md); padding: 0.85rem 1rem;">
                                    <!-- Reply Header -->
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                        <div style="display: flex; align-items: center; gap: 0.6rem;">
                                            <div style="width: 30px; height: 30px; border-radius: 50%; background: #6366f1; color: white; font-weight: 700; display: flex; align-items: center; justify-content: center; font-size: 0.8rem;">
                                                {{ strtoupper(substr($reply->user->name ?? 'U', 0, 1)) }}
                                            </div>
                                            <div>
                                                <div style="display: flex; align-items: center; gap: 0.4rem;">
                                                    <strong style="font-size: 0.875rem; color: var(--text-primary);">{{ $reply->user->name ?? 'Pengguna' }}</strong>
                                                    <span style="{{ $replyRoleBadge }} font-size: 0.65rem; font-weight: 700; padding: 0.08rem 0.35rem; border-radius: 4px; text-transform: uppercase;">
                                                        {{ $replyUserRole }}
                                                    </span>
                                                </div>
                                                <span style="font-size: 0.7rem; color: var(--text-muted);">
                                                    {{ $reply->created_at->diffForHumans() }} &bull; {{ $reply->created_at->format('d M Y - H:i') }}
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Reply Delete -->
                                        @if($isReplyAuthor || Auth::user()->hasRole(['admin', 'kaprodi']) || $class->users()->where('user_id', Auth::id())->exists())
                                            <form action="{{ route('classes.forums.destroy_post', [$class, $forum, $reply]) }}" method="POST" style="margin: 0;" class="swal-confirm-form" data-swal-msg="Hapus balasan ini?">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" style="background: none; border: none; padding: 0.15rem 0.35rem; color: #dc2626; cursor: pointer; font-size: 0.8rem;" title="Hapus Balasan">
                                                    🗑️
                                                </button>
                                            </form>
                                        @endif
                                    </div>

                                    <!-- Reply Body -->
                                    <div style="font-size: 0.875rem; line-height: 1.5; color: var(--text-primary); white-space: pre-line; word-break: break-word; padding-left: 2.3rem;">
                                        {!! nl2br(e($reply->content)) !!}
                                    </div>

                                    <!-- Reply Action -->
                                    <div style="padding-left: 2.3rem; margin-top: 0.35rem;">
                                        <button type="button" onclick="toggleReplyForm('reply-form-{{ $post->id }}', '{{ e($reply->user->name ?? '') }}')" 
                                            style="background: transparent; border: none; color: var(--primary); padding: 0; font-size: 0.775rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 0.25rem;">
                                            ↩️ Balas
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

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

<script>
function toggleReplyForm(formId, mentionUser = '') {
    const form = document.getElementById(formId);
    if (!form) return;
    
    const isHidden = form.style.display === 'none' || form.style.display === '';
    form.style.display = isHidden ? 'block' : 'none';
    
    if (isHidden) {
        const textarea = form.querySelector('textarea');
        if (textarea) {
            if (mentionUser && !textarea.value.includes('@' + mentionUser)) {
                textarea.value = '@' + mentionUser + ' ';
            }
            textarea.focus();
        }
    }
}
</script>
@endsection
