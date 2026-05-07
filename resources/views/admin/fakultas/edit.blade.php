@extends('layouts.admin')

@section('title', 'Edit Faculty')

@section('header_left')
    <h1 style="font-size: 1.25rem; font-weight: 700; margin: 0;">Edit Faculty</h1>
@endsection

@section('content')
<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <span>Edit Faculty Form</span>
        <a href="{{ route('fakultas.index') }}" style="font-size: 0.875rem; color: var(--text-muted);">Back</a>
    </div>
    <div class="card-body">
        <form action="{{ route('fakultas.update', $fakultas->id) }}" method="POST" enctype="multipart/form-data" id="fakultasForm">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label class="form-label">University</label>
                <select name="id_univs" class="form-control" required>
                    <option value="">-- Select University --</option>
                    @foreach($univs as $u)
                        <option value="{{ $u->id }}" {{ (old('id_univs', $fakultas->id_univs) == $u->id) ? 'selected' : '' }}>{{ $u->nama_univ }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">Faculty Code</label>
                    <input type="text" name="kode_fakultas" class="form-control" required value="{{ old('kode_fakultas', $fakultas->kode_fakultas) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Abbreviation</label>
                    <input type="text" name="short_name" class="form-control" required value="{{ old('short_name', $fakultas->short_name) }}">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Faculty Name</label>
                <input type="text" name="nama_fakultas" class="form-control" required value="{{ old('nama_fakultas', $fakultas->nama_fakultas) }}">
            </div>

            <div class="form-group">
                <label class="form-label">Dekan User (Optional)</label>
                <select name="dekan_id" class="form-control">
                    <option value="">-- Select Dekan User --</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ old('dekan_id', $fakultas->dekan_id) == $u->id ? 'selected' : '' }}>
                            {{ $u->name ?? $u->email }}
                        </option>
                    @endforeach
                </select>
                <small style="color: var(--text-muted);">Assign an existing user with 'dekan' role to manage this faculty.</small>
            </div>

            <div class="form-group">
                <label class="form-label">Leader Name (Dean)</label>
                <input type="text" name="nama_pimpinan" class="form-control" required value="{{ old('nama_pimpinan', $fakultas->nama_pimpinan) }}">
            </div>

            <div class="form-group">
                <label class="form-label">Dean's Signature</label>
                @if($fakultas->sign)
                    <div style="margin-bottom: 1rem;">
                        <p style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.25rem;">Current signature:</p>
                        <img src="{{ asset('storage/' . $fakultas->sign) }}" alt="Signature" style="max-height: 100px; border: 1px solid #e5e7eb; padding: 0.25rem; border-radius: 0.25rem;">
                    </div>
                @endif

                <div style="margin-bottom: 0.5rem; display: flex; gap: 1rem;">
                    <label style="font-size: 0.875rem; cursor: pointer;">
                        <input type="radio" name="sign_type" value="upload" checked onclick="toggleSignType('upload')"> Upload New Image
                    </label>
                    <label style="font-size: 0.875rem; cursor: pointer;">
                        <input type="radio" name="sign_type" value="draw" onclick="toggleSignType('draw')"> New Digital Signature
                    </label>
                </div>

                <div id="sign_upload_container">
                    <input type="file" name="sign_file" class="form-control" accept="image/*">
                    <small style="color: var(--text-muted);">Format: PNG, JPG (Max 2MB). Leave empty if you don't want to change.</small>
                </div>

                <div id="sign_draw_container" style="display: none;">
                    <div style="border: 1px solid #d1d5db; border-radius: 0.375rem; background: #fff; width: 100%; height: 200px; position: relative;">
                        <canvas id="signature-pad" style="width: 100%; height: 100%; cursor: crosshair;"></canvas>
                    </div>
                    <div style="margin-top: 0.5rem; display: flex; gap: 0.5rem;">
                        <button type="button" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;" onclick="clearSignature()">Clear</button>
                    </div>
                    <input type="hidden" name="sign" id="sign_base64">
                </div>
            </div>

            <div style="margin-top: 1rem; border-top: 1px solid #e5e7eb; padding-top: 1.5rem; display: flex; justify-content: flex-end;">
                <button type="submit" class="btn btn-primary">Update Data</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
    let signaturePad;
    
    document.addEventListener('DOMContentLoaded', function() {
        const canvas = document.getElementById('signature-pad');
        signaturePad = new SignaturePad(canvas);

        function resizeCanvas() {
            const ratio =  Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
            signaturePad.clear();
        }

        window.addEventListener("resize", resizeCanvas);
        window.resizeCanvas = resizeCanvas;
        resizeCanvas();
        
        document.getElementById('fakultasForm').onsubmit = function() {
            if (document.querySelector('input[name="sign_type"]:checked').value === 'draw') {
                if (!signaturePad.isEmpty()) {
                    document.getElementById('sign_base64').value = signaturePad.toDataURL();
                }
            }
        };
    });

    function toggleSignType(type) {
        if (type === 'upload') {
            document.getElementById('sign_upload_container').style.display = 'block';
            document.getElementById('sign_draw_container').style.display = 'none';
        } else {
            document.getElementById('sign_upload_container').style.display = 'none';
            document.getElementById('sign_draw_container').style.display = 'block';
            if (window.resizeCanvas) window.resizeCanvas();
        }
    }

    function clearSignature() {
        signaturePad.clear();
        document.getElementById('sign_base64').value = '';
    }
</script>
@endsection
