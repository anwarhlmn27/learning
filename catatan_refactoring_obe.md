# Catatan Refactoring Database OBE (Outcome-Based Education)
**Jadwal Eksekusi: Semester Depan**

Dokumen ini berisi rangkuman diskusi dan rencana perombakan (refactoring) struktur database untuk menyesuaikan dengan prinsip OBE yang ketat (mendukung *versioning* kurikulum).

## 1. Masalah pada Desain Saat Ini
Saat ini entitas Profil Lulusan (`gps`), Capaian Pembelajaran (`plos`), Bahan Kajian (`bahan_kajians`), dan Mata Kuliah (`subjects`) terikat langsung ke `id_prodi`. 
*   **Dampak:** Sulit mengelola versi kurikulum (misal Kurikulum 2020 vs Kurikulum 2024) karena GP dan PLO akan bercampur di satu prodi atau tertimpa.
*   **Alur (UI/UX) Terbalik:** Memicu *mindset content-based* di mana kurikulum dibentuk dari mata kuliah, padahal seharusnya kurikulum mendikte mata kuliah apa yang dibutuhkan.

## 2. Hirarki OBE yang Benar (Backward Design)
Urutan berpikir dan relasi yang ideal:
1. **Prodi** (Memiliki Visi Misi, merumuskan Kurikulum)
2. **Kurikulum** (Wadah untuk periode tertentu, cth: Kurikulum MBKM 2024)
3. **GP (Graduate Profile)** (Didefinisikan KHUSUS untuk kurikulum tersebut)
4. **PLO (Program Learning Outcome)** (Didefinisikan KHUSUS untuk kurikulum tersebut, dipetakan ke GP)
5. **Bahan Kajian** (Didefinisikan KHUSUS untuk kurikulum tersebut, diturunkan dari PLO)
6. **Mata Kuliah** (Dibuat di dalam kurikulum tersebut. Bertugas membungkus Bahan Kajian dan mengukur capaian PLO).

## 3. Rencana Perubahan Tabel (Migration)
Tabel-tabel berikut perlu di-alter (diubah relasinya):

*   **`gps` (Profil Lulusan):** Hapus `id_prodi`, tambahkan `id_kurikulum`.
*   **`plos` (Capaian Pembelajaran):** Hapus `id_prodi`, tambahkan `id_kurikulum`.
*   **`bahan_kajians`:** Hapus `id_prodi`, tambahkan `id_kurikulum`.
*   **`subjects` (Mata Kuliah):** Hapus `id_prodi`, tambahkan `id_kurikulum`.
*   **`kurikulum_subjects` (Tabel Pivot):** Bisa dihapus (di-*drop*), karena tabel `subjects` sudah langsung merelasikan dirinya ke `kurikulum`.

## 4. Keamanan Data RPS dan LMS
Perubahan struktur di atas **TIDAK AKAN** merusak data RPS dan LMS.
*   Tabel `clos` (Capaian Mata Kuliah) terikat pada `subject_id`.
*   Tabel `rps` terikat pada `subject_id` dan `kurikulum_id`.
Karena ID Mata Kuliah (`subject_id`) dan ID Kurikulum (`kurikulum_id`) tidak berubah/dihapus, seluruh relasi RPS, CLO, dan data pendukung LMS akan tetap aman dan utuh.

#GP
profile lulusan dengan kode PL sama tidak bisa walaupun beda prodi