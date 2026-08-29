<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\SessionRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionRatingController extends Controller
{
    public function store(Request $request, ClassRoom $class, $session_number)
    {
        $user = Auth::user();

        // 1. Pastikan pengguna adalah mahasiswa dan memiliki data profil student
        if (!$user || !$user->student) {
            return back()->with('error', 'Hanya mahasiswa yang dapat memberikan rating.');
        }

        $student = $user->student;

        // 2. Verifikasi apakah mahasiswa terdaftar di kelas ini
        $isEnrolled = $class->enrollments()->where('student_id', $student->id)->exists();
        if (!$isEnrolled) {
            return back()->with('error', 'Anda tidak terdaftar di kelas ini.');
        }

        // 3. Validasi input rating
        $request->validate([
            'dosen_id' => 'required|exists:dosens,id',
            'rating'   => 'required|integer|min:1|max:5',
            'comments' => 'nullable|string|max:1000',
        ]);

        // 4. Pastikan dosen yang dinilai terdaftar di kelas ini
        $isDosenInClass = $class->users()
            ->whereHas('dosen', fn($q) => $q->where('id', $request->dosen_id))
            ->exists();

        if (!$isDosenInClass) {
            return back()->with('error', 'Dosen yang dipilih tidak mengajar di kelas ini.');
        }

        // 5. Pastikan nomor sesi valid (1-14)
        $session_number = (int) $session_number;
        if ($session_number < 1 || $session_number > 14) {
            return back()->with('error', 'Nomor sesi tidak valid.');
        }

        // 6. Simpan atau perbarui rating (updateOrCreate untuk mencegah duplikasi)
        SessionRating::updateOrCreate(
            [
                'student_id'     => $student->id,
                'class_room_id'  => $class->id,
                'session_number' => $session_number,
                'dosen_id'       => $request->dosen_id,
            ],
            [
                'rating'   => $request->rating,
                'comments' => $request->comments,
            ]
        );

        return back()->with('success', 'Rating pertemuan berhasil dikirim.');
    }

    public function destroy(ClassRoom $class, SessionRating $rating)
    {
        if (!Auth::user()->can('edit-classes') && !Auth::user()->hasRole(['admin', 'kaprodi', 'baak'])) {
            return back()->with('error', 'Anda tidak memiliki akses untuk menghapus rating.');
        }

        if ($rating->class_room_id !== $class->id) {
            return back()->with('error', 'Rating tidak ditemukan di kelas ini.');
        }

        $rating->delete();
        return back()->with('success', 'Rating pertemuan berhasil dihapus.');
    }
}
