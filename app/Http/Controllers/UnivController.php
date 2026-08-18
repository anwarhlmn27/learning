<?php

namespace App\Http\Controllers;

use App\Models\Univ;
use Illuminate\Http\Request;

class UnivController extends Controller
{
    public function index()
    {
        $univs = Univ::all();
        return view('admin.univ.index', compact('univs'));
    }

    public function create()
    {
        $users = \App\Models\User::whereHas('roles', function($q) {
            $q->where('name', 'rektor');
        })->get();
        return view('admin.univ.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_univ' => 'required|string|max:50',
            'nama_univ' => 'required|string|max:255',

            'rektor_id' => 'nullable|exists:users,id',
            'sign_file' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'address' => 'required|string',
            'email' => 'required|email|max:255',
            'website' => 'required|url|max:255',
        ]);

        $data = $validated;
        unset($data['sign_file']);

        if ($request->hasFile('sign_file')) {
            $path = $request->file('sign_file')->store('signatures', 'public');
            $data['sign'] = $path;
        } elseif ($request->filled('sign')) {
            // Handle Base64 from Signature Pad
            $signatureData = $request->input('sign');
            if (strpos($signatureData, 'data:image/png;base64,') === 0) {
                $image = str_replace('data:image/png;base64,', '', $signatureData);
                $image = str_replace(' ', '+', $image);
                $imageName = 'sig_' . time() . '.png';
                \Storage::disk('public')->put('signatures/' . $imageName, base64_decode($image));
                $data['sign'] = 'signatures/' . $imageName;
            }
        }

        Univ::create($data);

        return redirect()->route('univ.index')->with('success', 'University data added successfully.');
    }

    public function edit(Univ $univ)
    {
        $users = \App\Models\User::whereHas('roles', function($q) {
            $q->where('name', 'rektor');
        })->get();
        return view('admin.univ.edit', compact('univ', 'users'));
    }

    public function update(Request $request, Univ $univ)
    {
        $validated = $request->validate([
            'kode_univ' => 'required|string|max:50',
            'nama_univ' => 'required|string|max:255',

            'rektor_id' => 'nullable|exists:users,id',
            'sign' => 'nullable|string',
            'sign_file' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'address' => 'required|string',
            'email' => 'required|email|max:255',
            'website' => 'required|url|max:255',
        ]);

        $data = $validated;
        unset($data['sign_file']);

        if ($request->hasFile('sign_file')) {
            if ($univ->sign) \Storage::disk('public')->delete($univ->sign);
            $path = $request->file('sign_file')->store('signatures', 'public');
            $data['sign'] = $path;
        } elseif ($request->filled('sign') && strpos($request->input('sign'), 'data:image/png;base64,') === 0) {
            if ($univ->sign) \Storage::disk('public')->delete($univ->sign);
            $signatureData = $request->input('sign');
            $image = str_replace('data:image/png;base64,', '', $signatureData);
            $image = str_replace(' ', '+', $image);
            $imageName = 'sig_' . time() . '.png';
            \Storage::disk('public')->put('signatures/' . $imageName, base64_decode($image));
            $data['sign'] = 'signatures/' . $imageName;
        } else {
            // Keep existing sign if no new one provided
            unset($data['sign']);
        }

        $univ->update($data);

        return redirect()->route('univ.index')->with('success', 'University data updated successfully.');
    }

    public function destroy(Univ $univ)
    {
        try {
            if ($univ->sign) {
                \Storage::disk('public')->delete($univ->sign);
            }
            $univ->delete();
            return redirect()->route('univ.index')->with('success', 'University data deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('univ.index')->withErrors(['error' => $this->handleException($e, 'Failed to delete University data.')]);
        }
    }
}
