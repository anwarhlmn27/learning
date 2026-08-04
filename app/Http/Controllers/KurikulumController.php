<?php

namespace App\Http\Controllers;

use App\Models\Kurikulum;
use App\Models\KurikulumSubject;
use App\Models\Prodi;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;

class KurikulumController extends Controller
{
    public function index()
    {
        $kurikulums = Kurikulum::with('prodi')->withCount('subjects')->get();
        return view('admin.kurikulum.index', compact('kurikulums'));
    }

    public function create()
    {
        $prodis = Prodi::all();
        return view('admin.kurikulum.create', compact('prodis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nm_kurikulum' => 'required|string|max:255',
            'id_prodi' => 'required|exists:prodis,id',
            'tahun_akademik' => 'required|integer|min:2000|max:2100',
            'berita_acara_fgd' => 'nullable|file|mimes:pdf|max:5120',
            'daftar_hadir' => 'nullable|file|mimes:pdf|max:5120',
            'notulensi_diskusi' => 'nullable|file|mimes:pdf|max:5120',
            'laporan_penyusunan' => 'nullable|file|mimes:pdf|max:5120',
            'laporan_sosialisasi' => 'nullable|file|mimes:pdf|max:5120',
            'dokumentasi' => 'nullable|file|mimes:pdf,jpg,png|max:5120',
        ]);

        try {
            $data = $request->except(['berita_acara_fgd', 'daftar_hadir', 'notulensi_diskusi', 'laporan_penyusunan', 'laporan_sosialisasi', 'dokumentasi']);
            
            $fileFields = ['berita_acara_fgd', 'daftar_hadir', 'notulensi_diskusi', 'laporan_penyusunan', 'laporan_sosialisasi', 'dokumentasi'];
            foreach ($fileFields as $field) {
                if ($request->hasFile($field)) {
                    $data[$field] = $request->file($field)->store('docs/kurikulum', 'public');
                }
            }

            Kurikulum::create($data);
            return redirect()->route('kurikulum.index')->with('success', 'Curriculum created successfully.');
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to create curriculum: ' . $e->getMessage()])->withInput();
        }
    }

    public function edit(Kurikulum $kurikulum)
    {
        $prodis = Prodi::all();
        return view('admin.kurikulum.edit', compact('kurikulum', 'prodis'));
    }

    public function update(Request $request, Kurikulum $kurikulum)
    {
        $request->validate([
            'nm_kurikulum' => 'required|string|max:255',
            'id_prodi' => 'required|exists:prodis,id',
            'tahun_akademik' => 'required|integer|min:2000|max:2100',
            'berita_acara_fgd' => 'nullable|file|mimes:pdf|max:5120',
            'daftar_hadir' => 'nullable|file|mimes:pdf|max:5120',
            'notulensi_diskusi' => 'nullable|file|mimes:pdf|max:5120',
            'laporan_penyusunan' => 'nullable|file|mimes:pdf|max:5120',
            'laporan_sosialisasi' => 'nullable|file|mimes:pdf|max:5120',
            'dokumentasi' => 'nullable|file|mimes:pdf,jpg,png|max:5120',
        ]);

        try {
            $data = $request->except(['berita_acara_fgd', 'daftar_hadir', 'notulensi_diskusi', 'laporan_penyusunan', 'laporan_sosialisasi', 'dokumentasi']);
            
            $fileFields = ['berita_acara_fgd', 'daftar_hadir', 'notulensi_diskusi', 'laporan_penyusunan', 'laporan_sosialisasi', 'dokumentasi'];
            foreach ($fileFields as $field) {
                if ($request->hasFile($field)) {
                    if ($kurikulum->$field) {
                        Storage::disk('public')->delete($kurikulum->$field);
                    }
                    $data[$field] = $request->file($field)->store('docs/kurikulum', 'public');
                }
            }

            $kurikulum->update($data);
            return redirect()->route('kurikulum.index')->with('success', 'Curriculum updated successfully.');
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to update curriculum: ' . $e->getMessage()])->withInput();
        }
    }

    public function manage(Kurikulum $kurikulum)
    {
        $kurikulum->load('subjects.subject');
        $availableSubjects = Subject::where('id_prodi', $kurikulum->id_prodi)
                                    ->whereNotIn('id', $kurikulum->subjects->pluck('id_subject'))
                                    ->get();
        return view('admin.kurikulum.manage', compact('kurikulum', 'availableSubjects'));
    }

    public function addSubject(Request $request, Kurikulum $kurikulum)
    {
        $request->validate([
            'id_subject' => 'required|exists:subjects,id',
            'semester' => 'required|integer|min:1|max:8',
        ]);

        try {
            KurikulumSubject::create([
                'id_kurikulum' => $kurikulum->id,
                'id_subject' => $request->id_subject,
                'semester' => $request->semester,
            ]);
            return redirect()->back()->with('success', 'Subject added to curriculum.');
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to add subject: ' . $e->getMessage()]);
        }
    }

    public function removeSubject(KurikulumSubject $kurikulumSubject)
    {
        try {
            $kurikulumSubject->delete();
            return redirect()->back()->with('success', 'Subject removed from curriculum.');
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['error' => $this->handleException($e, 'Failed to remove subject.')]);
        }
    }

    public function destroy(Kurikulum $kurikulum)
    {
        try {
            $fileFields = ['berita_acara_fgd', 'daftar_hadir', 'notulensi_diskusi', 'laporan_penyusunan', 'laporan_sosialisasi', 'dokumentasi'];
            foreach ($fileFields as $field) {
                if ($kurikulum->$field) {
                    Storage::disk('public')->delete($kurikulum->$field);
                }
            }
            $kurikulum->delete();
            return redirect()->route('kurikulum.index')->with('success', 'Curriculum deleted successfully.');
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['error' => $this->handleException($e, 'Failed to delete curriculum.')]);
        }
    }

    public function exportPdf(Kurikulum $kurikulum)
    {
        $kurikulum->load(['subjects.subject.prerequisites', 'prodi']);
        $pdf = Pdf::loadView('admin.kurikulum.export_pdf', compact('kurikulum'));
        $pdf->setPaper('A4', 'portrait');
        return $pdf->download('kurikulum_' . strtolower(str_replace(' ', '_', $kurikulum->prodi->nama_prodi)) . '.pdf');
    }
}
