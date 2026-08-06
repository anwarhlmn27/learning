<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\Forum;
use App\Models\ForumPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumController extends Controller
{
    public function show(ClassRoom $class, Forum $forum)
    {
        $user = Auth::user();

        // Check authorization: must be admin, kaprodi, dosen, rektor, dekan, baak OR enrolled student
        if (!$user->hasRole(['admin', 'kaprodi', 'rektor', 'dekan', 'baak'])) {
            $isStaff = $class->users()->where('user_id', $user->id)->exists();
            $isStudent = $user->student && $class->enrollments()->where('student_id', $user->student->id)->exists();

            if (!$isStaff && !$isStudent) {
                return back()->with('error', 'Anda tidak memiliki akses ke forum diskusi kelas ini.');
            }
        }

        $class->load(['subject', 'dosens']);
        $forum->load(['posts' => function ($q) {
            $q->orderBy('created_at', 'asc')->with('user');
        }]);

        // Find associated session number if exists
        $topic = \App\Models\ClassTopic::where('class_room_id', $class->id)
            ->where('type', 'forum')
            ->where('content_id', $forum->id)
            ->first();

        return view('lms.forums.show', compact('class', 'forum', 'topic'));
    }

    public function storePost(Request $request, ClassRoom $class, Forum $forum)
    {
        $user = Auth::user();

        // Check authorization
        if (!$user->hasRole(['admin', 'kaprodi', 'rektor', 'dekan', 'baak'])) {
            $isStaff = $class->users()->where('user_id', $user->id)->exists();
            $isStudent = $user->student && $class->enrollments()->where('student_id', $user->student->id)->exists();

            if (!$isStaff && !$isStudent) {
                return back()->with('error', 'Anda tidak memiliki akses untuk mengirim diskusi.');
            }
        }

        $request->validate([
            'content' => 'required|string|min:2',
        ], [
            'content.required' => 'Tuliskan tanggapan atau pertanyaan Anda terlebih dahulu.',
            'content.min' => 'Diskusi terlalu singkat, minimal 2 karakter.',
        ]);

        ForumPost::create([
            'forum_id' => $forum->id,
            'user_id'  => $user->id,
            'content'  => $request->content,
        ]);

        return back()->with('success', 'Diskusi berhasil dikirim.');
    }

    public function destroyPost(ClassRoom $class, Forum $forum, ForumPost $post)
    {
        $user = Auth::user();

        // Ensure post belongs to this forum
        if ($post->forum_id !== $forum->id) {
            abort(404);
        }

        // Only post author OR admin/kaprodi/dosen of the class can delete post
        $isAuthor = ($post->user_id === $user->id);
        $isClassStaff = $user->hasRole(['admin', 'kaprodi']) || $class->users()->where('user_id', $user->id)->exists();

        if (!$isAuthor && !$isClassStaff) {
            return back()->with('error', 'Anda tidak memiliki hak untuk menghapus postingan ini.');
        }

        $post->delete();

        return back()->with('success', 'Postingan diskusi berhasil dihapus.');
    }
}
