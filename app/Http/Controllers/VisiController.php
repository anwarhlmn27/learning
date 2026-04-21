<?php

namespace App\Http\Controllers;

use App\Models\Visi;
use App\Models\Univ;
use App\Models\Fakultas;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VisiController extends Controller
{
    public function index()
    {
        $visis = Visi::with('visible')->get();
        return view('admin.visi.index', compact('visis'));
    }

    public function create()
    {
        $univs = Univ::all();
        $fakultas = Fakultas::with('univ')->get();
        $prodis = Prodi::with('fakultas.univ')->get();
        
        return view('admin.visi.create', compact('univs', 'fakultas', 'prodis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'entity_type' => 'required|in:Univ,Fakultas,Prodi',
            'entity_id' => 'required',
            'visi' => 'required',
            'misi' => 'required',
            'doc_penyusunan' => 'nullable|file|mimes:pdf|max:2048',
            'doc_pengesahan' => 'nullable|file|mimes:pdf|max:2048',
            'doc_sosialisasi' => 'nullable|file|mimes:pdf|max:2048',
            'doc_hasil_survey' => 'nullable|file|mimes:pdf|max:2048',
        ],
    [
        'entity_type.required' => 'Please select an entity type.',
        'entity_id.required' => 'Please select an entity.',
        'visi.required' => 'Vision is required.',
        'misi.required' => 'Mission is required.',
        'doc_penyusunan.max' => 'Doc Penyusunan must be less than 2MB.',
        'doc_pengesahan.max' => 'Doc Pengesahan must be less than 2MB.',
        'doc_sosialisasi.max' => 'Doc Sosialisasi must be less than 2MB.',
        'doc_hasil_survey.max' => 'Doc Hasil Survey must be less than 2MB.',
        'doc_penyusunan.mimes' => 'Doc Penyusunan must be a PDF file.',
        'doc_pengesahan.mimes' => 'Doc Pengesahan must be a PDF file.',
        'doc_sosialisasi.mimes' => 'Doc Sosialisasi must be a PDF file.',
        'doc_hasil_survey.mimes' => 'Doc Hasil Survey must be a PDF file.',
    ]);

        $data = $request->except(['entity_type', 'entity_id', 'doc_penyusunan', 'doc_pengesahan', 'doc_sosialisasi', 'doc_hasil_survey']);
        
        $data['visible_id'] = $request->entity_id;
        $data['visible_type'] = "App\\Models\\" . $request->entity_type;

        $existing = Visi::where('visible_id', $data['visible_id'])
                        ->where('visible_type', $data['visible_type'])
                        ->first();
        
        if ($existing) {
            return redirect()->back()->withErrors(['entity_id' => 'This entity already has a Vision & Mission recorded.'])->withInput();
        }

        $fileFields = ['doc_penyusunan', 'doc_pengesahan', 'doc_sosialisasi', 'doc_hasil_survey'];
        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $data[$field] = $request->file($field)->store('docs/visi', 'public');
            }
        }

        Visi::create($data);

        return redirect()->route('visi.index')->with('success', 'Vision & Mission added successfully.');
    }

    public function edit(Visi $visi)
    {
        $univs = Univ::all();
        $fakultas = Fakultas::with('univ')->get();
        $prodis = Prodi::with('fakultas.univ')->get();
        
        return view('admin.visi.edit', compact('visi', 'univs', 'fakultas', 'prodis'));
    }

    public function update(Request $request, Visi $visi)
    {
        $request->validate([
            'visi' => 'required',
            'misi' => 'required',
            'doc_penyusunan' => 'nullable|file|mimes:pdf|max:2048',
            'doc_pengesahan' => 'nullable|file|mimes:pdf|max:2048',
            'doc_sosialisasi' => 'nullable|file|mimes:pdf|max:2048',
            'doc_hasil_survey' => 'nullable|file|mimes:pdf|max:2048',
        ],    [
        'visi.required' => 'Vision is required.',
        'misi.required' => 'Mission is required.',
        'doc_penyusunan.max' => 'Doc Penyusunan must be less than 2MB.',
        'doc_pengesahan.max' => 'Doc Pengesahan must be less than 2MB.',
        'doc_sosialisasi.max' => 'Doc Sosialisasi must be less than 2MB.',
        'doc_hasil_survey.max' => 'Doc Hasil Survey must be less than 2MB.',
        'doc_penyusunan.mimes' => 'Doc Penyusunan must be a PDF file.',
        'doc_pengesahan.mimes' => 'Doc Pengesahan must be a PDF file.',
        'doc_sosialisasi.mimes' => 'Doc Sosialisasi must be a PDF file.',
        'doc_hasil_survey.mimes' => 'Doc Hasil Survey must be a PDF file.',
    ]);

        $data = $request->except(['doc_penyusunan', 'doc_pengesahan', 'doc_sosialisasi', 'doc_hasil_survey']);

        // Handle File Uploads
        $fileFields = ['doc_penyusunan', 'doc_pengesahan', 'doc_sosialisasi', 'doc_hasil_survey'];
        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                if ($visi->$field) {
                    Storage::disk('public')->delete($visi->$field);
                }
                $data[$field] = $request->file($field)->store('docs/visi', 'public');
            }
        }

        $visi->update($data);

        return redirect()->route('visi.index')->with('success', 'Vision & Mission updated successfully.');
    }

    public function destroy(Visi $visi)
    {
        $fileFields = ['doc_penyusunan', 'doc_pengesahan', 'doc_sosialisasi', 'doc_hasil_survey'];
        foreach ($fileFields as $field) {
            if ($visi->$field) {
                Storage::disk('public')->delete($visi->$field);
            }
        }
        
        $visi->delete();
        return redirect()->route('visi.index')->with('success', 'Vision & Mission deleted successfully.');
    }
}
