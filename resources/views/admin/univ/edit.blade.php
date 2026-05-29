@extends('layouts.admin')

@section('title', 'Edit University')

@section('header_left')
    <h1 style="font-size: 1.25rem; font-weight: 700; margin: 0;">Edit University</h1>
@endsection

@section('content')
<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <span>Edit University Form</span>
        <a href="{{ route('univ.index') }}" style="font-size: 0.875rem; color: var(--text-muted);">Back</a>
    </div>
    <div class="card-body">
        @if($errors->any())
            <div style="background: #fee2e2; color: #b91c1c; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; font-size: 0.875rem;">
                <strong>⚠ Please fix the following errors:</strong>
                <ul style="margin: 0.5rem 0 0 1.25rem; padding: 0;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('univ.update', $univ->id) }}" method="POST" enctype="multipart/form-data" id="univForm">
            @csrf
            @method('PUT')
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">University Code <span style="color: red;">*</span></label>
                    <input type="text" name="kode_univ" class="form-control @error('kode_univ') is-invalid @enderror" required value="{{ old('kode_univ', $univ->kode_univ) }}">
                    @error('kode_univ')
                        <small style="color: #dc2626; font-size: 0.75rem;">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">University Name <span style="color: red;">*</span></label>
                    <input type="text" name="nama_univ" class="form-control @error('nama_univ') is-invalid @enderror" required value="{{ old('nama_univ', $univ->nama_univ) }}">
                    @error('nama_univ')
                        <small style="color: #dc2626; font-size: 0.75rem;">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Rektor User (Optional)</label>
                <select name="rektor_id" class="form-control">
                    <option value="">-- Select Rektor User --</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ old('rektor_id', $univ->rektor_id) == $u->id ? 'selected' : '' }}>
                            {{ $u->name ?? $u->email }}
                        </option>
                    @endforeach
                </select>
                <small style="color: var(--text-muted);">Assign an existing user with 'rektor' role.</small>
            </div>

            <div class="form-group">
                <label class="form-label">Full Address <span style="color: red;">*</span></label>
                <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="3" required>{{ old('address', $univ->address) }}</textarea>
                @error('address')
                    <small style="color: #dc2626; font-size: 0.75rem;">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Leader's Signature</label>
                @if($univ->sign)
                    <div style="margin-bottom: 1rem;">
                        <p style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.25rem;">Current signature:</p>
                        <img src="{{ asset('storage/' . $univ->sign) }}" alt="Signature" style="max-height: 100px; border: 1px solid #e5e7eb; padding: 0.25rem; border-radius: 0.25rem;">
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
                    <input type="file" name="sign_file" class="form-control @error('sign_file') is-invalid @enderror" accept="image/*">
                    <small style="color: var(--text-muted);">Format: PNG, JPG (Max 2MB). Leave empty if you don't want to change.</small>
                    @error('sign_file')
                        <br><small style="color: #dc2626; font-size: 0.75rem;">{{ $message }}</small>
                    @enderror
                </div>

                <div id="sign_draw_container" style="display: none;">
                    <div style="border: 1px solid #d1d5db; border-radius: 0.375rem; background: #fff; width: 100%; height: 200px; position: relative;">
                        <canvas id="signature-pad" style="width: 100%; height: 100%; cursor: crosshair;"></canvas>
                    </div>
                    <div style="margin-top: 0.5rem; display: flex; gap: 0.5rem;">
                        <button type="button" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;" onclick="clearSignature()">Clear</button>
                    </div>
                    <input type="hidden" name="sign" id="sign_base64">
                    @error('sign')
                        <small style="color: #dc2626; font-size: 0.75rem;">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">Institutional Email <span style="color: red;">*</span></label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" required value="{{ old('email', $univ->email) }}">
                    @error('email')
                        <small style="color: #dc2626; font-size: 0.75rem;">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Website (URL) <span style="color: red;">*</span></label>
                    <input type="url" name="website" class="form-control @error('website') is-invalid @enderror" required value="{{ old('website', $univ->website) }}">
                    @error('website')
                        <small style="color: #dc2626; font-size: 0.75rem;">{{ $message }}</small>
                    @enderror
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

        document.getElementById('univForm').onsubmit = function() {
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
