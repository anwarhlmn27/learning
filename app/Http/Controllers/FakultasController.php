<?php

namespace App\Http\Controllers;

use App\Models\Fakultas;
use App\Models\Univ;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FakultasController extends Controller
{
    public function index()
    {
        $fakultas = Fakultas::with('univ')->get();
        return view('admin.fakultas.index', compact('fakultas'));
    }

    public function create()
    {
        $univs = Univ::all();
        return view('admin.fakultas.create', compact('univs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_univs' => 'required|exists:univs,id',
            'kode_fakultas' => 'required|string|max:50',
            'nama_fakultas' => 'required|string|max:255',
            'short_name' => 'required|string|max:50',
            'nama_pimpinan' => 'required|string|max:255',
            'sign' => 'nullable|string',
            'sign_file' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        $data = $validated;
        unset($data['sign_file']);

        if ($request->hasFile('sign_file')) {
            $path = $request->file('sign_file')->store('signatures', 'public');
            $data['sign'] = $path;
        } elseif ($request->filled('sign')) {
            $signatureData = $request->input('sign');
            if (strpos($signatureData, 'data:image/png;base64,') === 0) {
                $image = str_replace('data:image/png;base64,', '', $signatureData);
                $image = str_replace(' ', '+', $image);
                $imageName = 'sig_fak_' . time() . '.png';
                Storage::disk('public')->put('signatures/' . $imageName, base64_decode($image));
                $data['sign'] = 'signatures/' . $imageName;
            }
        }

        Fakultas::create($data);

        return redirect()->route('fakultas.index')->with('success', 'Faculty data added successfully.');
    }

    public function show(Fakultas $fakultas)
    {
        return view('admin.fakultas.show', compact('fakultas'));
    }

    public function edit(Fakultas $fakulta)
    {
        $univs = Univ::all();
        return view('admin.fakultas.edit', ['fakultas' => $fakulta, 'univs' => $univs]);
    }

    public function update(Request $request, Fakultas $fakulta)
    {
        $validated = $request->validate([
            'id_univs' => 'required|exists:univs,id',
            'kode_fakultas' => 'required|string|max:50',
            'nama_fakultas' => 'required|string|max:255',
            'short_name' => 'required|string|max:50',
            'nama_pimpinan' => 'required|string|max:255',
            'sign' => 'nullable|string',
            'sign_file' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        $data = $validated;
        unset($data['sign_file']);

        if ($request->hasFile('sign_file')) {
            if ($fakulta->sign) Storage::disk('public')->delete($fakulta->sign);
            $path = $request->file('sign_file')->store('signatures', 'public');
            $data['sign'] = $path;
        } elseif ($request->filled('sign') && strpos($request->input('sign'), 'data:image/png;base64,') === 0) {
            if ($fakulta->sign) Storage::disk('public')->delete($fakulta->sign);
            $signatureData = $request->input('sign');
            $image = str_replace('data:image/png;base64,', '', $signatureData);
            $image = str_replace(' ', '+', $image);
            $imageName = 'sig_fak_' . time() . '.png';
            Storage::disk('public')->put('signatures/' . $imageName, base64_decode($image));
            $data['sign'] = 'signatures/' . $imageName;
        } else {
            unset($data['sign']);
        }

        $fakulta->update($data);

        return redirect()->route('fakultas.index')->with('success', 'Faculty data updated successfully.');
    }

    public function destroy(Fakultas $fakulta)
    {
        if ($fakulta->sign) {
            Storage::disk('public')->delete($fakulta->sign);
        }
        $fakulta->delete();
        return redirect()->route('fakultas.index')->with('success', 'Faculty data deleted successfully.');
    }
}
