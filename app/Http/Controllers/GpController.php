<?php

namespace App\Http\Controllers;

use App\Models\Gp;
use App\Models\GpAttachment;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Exception;

class GpController extends Controller
{
    public function index()
    {
        $prodis = Prodi::withCount('gps')->with('fakultas')->get();
        return view('admin.gp.index', compact('prodis'));
    }

    public function manage(Prodi $prodi)
    {
        $prodi->load(['gps', 'gpAttachments']);
        return view('admin.gp.manage', compact('prodi'));
    }

    // Store a new Graduate Profile expertise item
    public function storeProfile(Request $request, Prodi $prodi)
    {
        $request->validate([
            'kode_profil' => 'required|string|unique:gps,kode_profil',
            'nm_profil' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'career_pathway' => 'required|string',
            'kompetensi' => 'required|string',
            'sumber_acuan' => 'required|string',
            'stakeholders' => 'required|string',
            'status' => 'required|in:Draft,Aktif,Revisi,Tidak Aktif',
        ]);

        try {
            Gp::create([
                'id_prodi' => $prodi->id,
                'kode_profil' => $request->kode_profil,
                'nm_profil' => $request->nm_profil,
                'deskripsi' => $request->deskripsi,
                'career_pathway' => $request->career_pathway,
                'kompetensi' => $request->kompetensi,
                'sumber_acuan' => $request->sumber_acuan,
                'stakeholders' => $request->stakeholders,
                'status' => $request->status,
            ]);

            return redirect()->back()->with('success', 'Graduate Profile item added successfully.');
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to add profile: ' . $e->getMessage()])->withInput();
        }
    }

    // Update a Graduate Profile expertise item
    public function updateProfile(Request $request, Gp $gp)
    {
        $request->validate([
            'kode_profil' => 'required|string|unique:gps,kode_profil,' . $gp->id,
            'nm_profil' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'career_pathway' => 'required|string',
            'kompetensi' => 'required|string',
            'sumber_acuan' => 'required|string',
            'stakeholders' => 'required|string',
            'status' => 'required|in:Draft,Aktif,Revisi,Tidak Aktif',
        ]);

        try {
            $gp->update($request->only([
                'kode_profil', 'nm_profil', 'deskripsi', 'career_pathway', 
                'kompetensi', 'sumber_acuan', 'stakeholders', 'status'
            ]));
            return redirect()->back()->with('success', 'Graduate Profile item updated successfully.');
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to update profile: ' . $e->getMessage()]);
        }
    }

    // Delete a Graduate Profile expertise item
    public function destroyProfile(Gp $gp)
    {
        try {
            $gp->delete();
            return redirect()->back()->with('success', 'Graduate Profile item deleted successfully.');
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['error' => $this->handleException($e, 'Failed to delete profile.')]);
        }
    }

    // Store a new supporting document attachment
    public function storeAttachment(Request $request, Prodi $prodi)
    {
        $request->validate([
            'nm_dokumen' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf|max:2048', // 2MB limit
        ], [
            'file.mimes' => 'Document must be a PDF file.',
            'file.max' => 'Document size must be less than 2MB.',
        ]);

        try {
            if ($request->hasFile('file')) {
                $path = $request->file('file')->store('docs/gp', 'public');
                
                GpAttachment::create([
                    'id_prodi' => $prodi->id,
                    'nm_dokumen' => $request->nm_dokumen,
                    'file_path' => $path,
                ]);

                return redirect()->back()->with('success', 'Attachment uploaded successfully.');
            }
            throw new Exception('No file uploaded.');
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to upload attachment: ' . $e->getMessage()]);
        }
    }

    // Delete a supporting document attachment
    public function destroyAttachment(GpAttachment $attachment)
    {
        try {
            if ($attachment->file_path) {
                Storage::disk('public')->delete($attachment->file_path);
            }
            $attachment->delete();
            return redirect()->back()->with('success', 'Attachment deleted successfully.');
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['error' => $this->handleException($e, 'Failed to delete attachment.')]);
        }
    }
}
