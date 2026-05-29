<?php

namespace App\Http\Controllers;

use App\Models\Prodi;
use App\Models\Fakultas;
use Illuminate\Http\Request;

class ProdiController extends Controller
{
    public function index()
    {
        $prodis = Prodi::with('fakultas.univ')->get();
        return view('admin.prodi.index', compact('prodis'));
    }

    public function create()
    {
        $fakultas = Fakultas::with('univ')->get();
        $users = \App\Models\User::whereHas('roles', function($q) {
            $q->where('name', 'kaprodi');
        })->get();
        return view('admin.prodi.create', compact('fakultas', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_fakultas' => 'required|exists:fakultas,id',
            'kode_prodi' => 'required|numeric',
            'nama_prodi' => 'required|string|max:255',
            'short_name' => 'required|string|max:50',

            'kaprodi_id' => 'nullable|exists:users,id',
            'sign' => 'nullable|string',
            'sign_file' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        $data = $validated;
        unset($data['sign_file']);

        if ($request->hasFile('sign_file')) {
            $path = $request->file('sign_file')->store('signatures', 'public');
            $data['sign'] = $path;
        } elseif ($request->filled('sign') && strpos($request->input('sign'), 'data:image/png;base64,') === 0) {
            $signatureData = $request->input('sign');
            $image = str_replace('data:image/png;base64,', '', $signatureData);
            $image = str_replace(' ', '+', $image);
            $imageName = 'sig_prodi_' . time() . '.png';
            \Storage::disk('public')->put('signatures/' . $imageName, base64_decode($image));
            $data['sign'] = 'signatures/' . $imageName;
        }

        Prodi::create($data);

        return redirect()->route('prodi.index')->with('success', 'Study Program data added successfully.');
    }

    public function show(Prodi $prodi)
    {
        return view('admin.prodi.show', compact('prodi'));
    }

    public function edit(Prodi $prodi)
    {
        $fakultas = Fakultas::with('univ')->get();
        $users = \App\Models\User::whereHas('roles', function($q) {
            $q->where('name', 'kaprodi');
        })->get();
        return view('admin.prodi.edit', compact('prodi', 'fakultas', 'users'));
    }

    public function update(Request $request, Prodi $prodi)
    {
        $validated = $request->validate([
            'id_fakultas' => 'required|exists:fakultas,id',
            'kode_prodi' => 'required|numeric',
            'nama_prodi' => 'required|string|max:255',
            'short_name' => 'required|string|max:50',

            'kaprodi_id' => 'nullable|exists:users,id',
            'sign' => 'nullable|string',
            'sign_file' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        $data = $validated;
        unset($data['sign_file']);

        if ($request->hasFile('sign_file')) {
            if ($prodi->sign) \Storage::disk('public')->delete($prodi->sign);
            $path = $request->file('sign_file')->store('signatures', 'public');
            $data['sign'] = $path;
        } elseif ($request->filled('sign') && strpos($request->input('sign'), 'data:image/png;base64,') === 0) {
            if ($prodi->sign) \Storage::disk('public')->delete($prodi->sign);
            $signatureData = $request->input('sign');
            $image = str_replace('data:image/png;base64,', '', $signatureData);
            $image = str_replace(' ', '+', $image);
            $imageName = 'sig_prodi_' . time() . '.png';
            \Storage::disk('public')->put('signatures/' . $imageName, base64_decode($image));
            $data['sign'] = 'signatures/' . $imageName;
        } else {
            unset($data['sign']);
        }

        $prodi->update($data);

        return redirect()->route('prodi.index')->with('success', 'Study Program data updated successfully.');
    }

    public function destroy(Prodi $prodi)
    {
        try {
            $signPath = $prodi->sign;
            $prodi->delete();
            if ($signPath) {
                \Storage::disk('public')->delete($signPath);
            }
            return redirect()->route('prodi.index')->with('success', 'Study Program data deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('prodi.index')->withErrors(['error' => $this->handleException($e, 'Failed to delete Study Program data.')]);
        }
    }
}
