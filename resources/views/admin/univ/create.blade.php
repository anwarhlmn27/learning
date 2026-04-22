@extends('layouts.admin')

@section('title', 'Add University')

@section('header_left')
    <h1 style="font-size: 1.25rem; font-weight: 700; margin: 0;">Add University</h1>
@endsection

@section('content')
<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <span>University Form</span>
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

        <form action="{{ route('univ.store') }}" method="POST" enctype="multipart/form-data" id="univForm">
            @csrf
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">University Code</label>
                    <input type="text" name="kode_univ" class="form-control @error('kode_univ') is-invalid @enderror" placeholder="Example: UNP" required value="{{ old('kode_univ') }}">
                    @error('kode_univ')
                        <small style="color: #dc2626; font-size: 0.75rem;">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">University Name</label>
                    <input type="text" name="nama_univ" class="form-control @error('nama_univ') is-invalid @enderror" placeholder="Full Name" required value="{{ old('nama_univ') }}">
                    @error('nama_univ')
                        <small style="color: #dc2626; font-size: 0.75rem;">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">Leader Name (Rector)</label>
                    <input type="text" name="nama_pimpinan" class="form-control @error('nama_pimpinan') is-invalid @enderror" required value="{{ old('nama_pimpinan') }}">
                    @error('nama_pimpinan')
                        <small style="color: #dc2626; font-size: 0.75rem;">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Full Address</label>
                <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="3" required>{{ old('address') }}</textarea>
                @error('address')
                    <small style="color: #dc2626; font-size: 0.75rem;">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Rector's Signature</label>
                <div style="margin-bottom: 0.5rem; display: flex; gap: 1rem;">
                    <label style="font-size: 0.875rem; cursor: pointer;">
                        <input type="radio" name="sign_type" value="upload" checked onclick="toggleSignType('upload')"> Upload Image
                    </label>
                    <label style="font-size: 0.875rem; cursor: pointer;">
                        <input type="radio" name="sign_type" value="draw" onclick="toggleSignType('draw')"> Digital Signature
                    </label>
                </div>

                <div id="sign_upload_container">
                    <input type="file" name="sign_file" class="form-control @error('sign_file') is-invalid @enderror" accept="image/*">
                    <small style="color: var(--text-muted);">Format: PNG, JPG (Max 2MB)</small>
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
                    <label class="form-label">Institutional Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" required value="{{ old('email') }}">
                    @error('email')
                        <small style="color: #dc2626; font-size: 0.75rem;">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Website (URL)</label>
                    <input type="url" name="website" class="form-control @error('website') is-invalid @enderror" placeholder="https://..." required value="{{ old('website') }}">
                    @error('website')
                        <small style="color: #dc2626; font-size: 0.75rem;">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div style="margin-top: 1rem; border-top: 1px solid #e5e7eb; padding-top: 1.5rem; display: flex; justify-content: flex-end;">
                <button type="submit" class="btn btn-primary">Save Data</button>
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
            // Resize canvas when shown to ensure it's not 0 width/height
            if (window.resizeCanvas) window.resizeCanvas();
        }
    }

    function clearSignature() {
        signaturePad.clear();
        document.getElementById('sign_base64').value = '';
    }
</script>
@endsection
