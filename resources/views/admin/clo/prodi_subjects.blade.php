@extends('layouts.admin')

@section('title', 'CPMK (CLO) - ' . $prodi->nama_prodi)

@section('header_left')
    <h1 style="font-size: 1.25rem; font-weight: 700; margin: 0;">CLO Management: {{ $prodi->nama_prodi }}</h1>
@endsection

@section('content')
<div style="margin-bottom: 1.5rem;">
    <a href="{{ route('clo.index') }}" style="color: var(--text-muted); text-decoration: none; font-size: 0.875rem;">← Back to Prodi List</a>
</div>

{{-- Filter Bar --}}
<div class="card" style="margin-bottom: 1.25rem;">
    <div class="card-body" style="padding: 1rem 1.25rem;">
        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: flex-end;">
            {{-- Search --}}
            <div style="flex: 1; min-width: 200px;">
                <label style="font-size: 0.75rem; font-weight: 600; color: var(--text-muted); display: block; margin-bottom: 0.25rem; text-transform: uppercase;">{{ __('Search') }} </label>
                <input type="text" id="filterSearch" onkeyup="applyFilters()"
                       placeholder="{{ __('Code or subject name…') }}"
                       style="width: 100%; padding: 0.45rem 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; outline: none; box-sizing: border-box;">
            </div>
            {{-- Semester --}}
            <div style="min-width: 150px;">
                <label style="font-size: 0.75rem; font-weight: 600; color: var(--text-muted); display: block; margin-bottom: 0.25rem; text-transform: uppercase;">{{ __('Semester') }} </label>
                <select id="filterSemester" onchange="applyFilters()"
                        style="width: 100%; padding: 0.45rem 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; background: #fff; outline: none;">
                    <option value="">All Semesters</option>
                    @for($i = 1; $i <= 8; $i++)
                        <option value="{{ $i }}">Semester {{ $i }}</option>
                    @endfor
                </select>
            </div>
            {{-- CLO Status --}}
            <div style="min-width: 160px;">
                <label style="font-size: 0.75rem; font-weight: 600; color: var(--text-muted); display: block; margin-bottom: 0.25rem; text-transform: uppercase;">{{ __('CLO Status') }} </label>
                <select id="filterCloStatus" onchange="applyFilters()"
                        style="width: 100%; padding: 0.45rem 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; background: #fff; outline: none;">
                    <option value="">All</option>
                    <option value="has">Has CLO</option>
                    <option value="none">No CLO Yet</option>
                </select>
            </div>
            {{-- Reset --}}
            <div>
                <button onclick="resetFilters()"
                        style="padding: 0.45rem 1rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; background: #f9fafb; cursor: pointer; color: var(--text-muted); font-weight: 600;">
                    ↺ Reset
                </button>
            </div>
        </div>
        <div id="filterInfo" style="margin-top: 0.6rem; font-size: 0.75rem; color: var(--text-muted); display: none;">
            Menampilkan <strong id="filterCount">0</strong> dari <strong id="filterTotal">0</strong> subject
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header" style="flex-wrap: wrap; gap: 1rem;">
        <span>Subject List in {{ $prodi->nama_prodi }}</span>
    </div>
    <div class="card-body" style="padding: 0;">
        <div style="overflow-x: auto;">
            <table class="table table-responsive-md">
                <thead>
                    <tr>
                        <th>{{ __('Subject Code') }}</th>
                        <th>{{ __('Subject Name') }}</th>
                        <th>SKS (T/P/Total)</th>
                        <th>{{ __('Semester') }}</th>
                        <th>{{ __('CLO Count') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody id="cloTbody">
                    @forelse($subjects as $s)
                        <tr class="clo-row"
                            data-code="{{ strtolower($s->kode_subject) }}"
                            data-name="{{ strtolower($s->nama_subject) }}"
                            data-semester="{{ $s->semester }}"
                            data-clocount="{{ $s->clos_count }}">
                            <td style="font-weight: 600;">{{ $s->kode_subject }}</td>
                            <td>{{ $s->nama_subject }}</td>
                            <td>{{ $s->sks_t }} / {{ $s->sks_p }} / {{ $s->total_sks }}</td>
                            <td>{{ $s->semester }}</td>
                            <td>
                                <span class="badge" style="background: {{ $s->clos_count > 0 ? 'var(--primary-light, #ede9fe)' : '#f3f4f6' }}; color: {{ $s->clos_count > 0 ? 'var(--primary)' : 'var(--text-muted)' }}; padding: 2px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600;">
                                    {{ $s->clos_count }} CLO{{ $s->clos_count != 1 ? 's' : '' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('clo.manage', $s->id) }}" class="btn btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">
                                    Manage CLO
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                                No subjects found in this Prodi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div id="noResultRow" style="display:none; text-align: center; padding: 2rem; color: var(--text-muted); font-size: 0.875rem;">
                Tidak ada subject yang cocok dengan filter.
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    const totalRows = document.querySelectorAll('.clo-row').length;
    document.getElementById('filterTotal').textContent = totalRows;

    function applyFilters() {
        const search    = document.getElementById('filterSearch').value.toLowerCase().trim();
        const semester  = document.getElementById('filterSemester').value;
        const cloStatus = document.getElementById('filterCloStatus').value;

        const rows = document.querySelectorAll('.clo-row');
        let visible = 0;

        rows.forEach(row => {
            const matchSearch  = !search    || row.dataset.code.includes(search) || row.dataset.name.includes(search);
            const matchSem     = !semester  || row.dataset.semester === semester;
            const count        = parseInt(row.dataset.clocount);
            const matchStatus  = !cloStatus
                || (cloStatus === 'has'  && count > 0)
                || (cloStatus === 'none' && count === 0);

            if (matchSearch && matchSem && matchStatus) {
                row.style.display = '';
                visible++;
            } else {
                row.style.display = 'none';
            }
        });

        const hasFilter = search || semester || cloStatus;
        document.getElementById('filterInfo').style.display = hasFilter ? 'block' : 'none';
        document.getElementById('filterCount').textContent = visible;
        document.getElementById('noResultRow').style.display = (visible === 0 && totalRows > 0) ? 'block' : 'none';
    }

    function resetFilters() {
        document.getElementById('filterSearch').value = '';
        document.getElementById('filterSemester').value = '';
        document.getElementById('filterCloStatus').value = '';
        applyFilters();
    }
</script>
@endsection

@endsection
