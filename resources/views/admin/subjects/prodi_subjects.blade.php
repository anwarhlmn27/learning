@extends('layouts.admin')

@section('title', __('Subjects for') . ' ' . $prodi->nama_prodi)

@section('header_left')
    <h1 style="font-size: 1.25rem; font-weight: 700; margin: 0;">{{ __('Subjects') }}: {{ $prodi->nama_prodi }}</h1>
@endsection

@section('content')
<div style="margin-bottom: 1rem;">
    <a href="{{ route('subjects.index') }}" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; background: #f3f4f6; color: #1f2937; text-decoration: none; border-radius: 4px; border: 1px solid #e5e7eb;">&larr; {{ __('Back') }}</a>
</div>

<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
        <span>{{ __('List of Subjects') }} ({{ $subjects->count() }})</span>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('subjects.export-bk', $prodi->id) }}" class="btn btn-secondary" style="padding: 0.25rem 0.75rem; font-size: 0.75rem; background: #fff; color: #374151; border: 1px solid #d1d5db;">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 4px; vertical-align: middle;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                {{ __('Export BK Mapping') }}
            </a>
            <a href="{{ route('subjects.export-plo', $prodi->id) }}" class="btn btn-secondary" style="padding: 0.25rem 0.75rem; font-size: 0.75rem; background: #fff; color: #374151; border: 1px solid #d1d5db;">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 4px; vertical-align: middle;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            {{ __('Export PLO Mapping') }}
            </a>
            <a href="{{ route('subjects.create') }}?prodi_id={{ $prodi->id }}" class="btn btn-primary">{{ __('Add New Subject') }}</a>
        </div>
    </div>
    <div class="card-body" style="padding: 0;">
        <div style="overflow-x: auto;">
            <table class="table table-responsive-md">
                <thead>
                    <tr>
                        <th>{{ __('Code') }}</th>
                        <th>{{ __('Subject Name') }}</th>
                        <th>{{ __('SKS') }} (T-P-PL)</th>
                        <th>{{ __('Sem') }}</th>
                        <th>{{ __('Prerequisite') }}</th>
                        <!-- <th>{{ __('Type') }}</th> -->
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subjects as $s)
                        <tr>
                            <td style="font-weight: 600;">{{ $s->kode_subject }}</td>
                            <td>{{ $s->nama_subject }}</td>
                            <td>{{ $s->sks_t }} - {{ $s->sks_p }} - {{ $s->sks_pl }} ({{ $s->total_sks }})</td>
                            <td>{{ $s->semester }}</td>
                            <td>
                                @if($s->prerequisites->count() > 0)
                                    <div style="display: grid; grid-template-columns: repeat(2, minmax(0, max-content)); gap: 4px; max-width: max-content;">
                                        @foreach($s->prerequisites as $prereq)
                                            <span title="{{ $prereq->nama_subject }}" style="background: #e0e7ff; color: #4f46e5; padding: 2px 6px; border-radius: 4px; font-size: 0.75rem; font-weight: 600; display: inline-block; white-space: nowrap;">
                                                {{ $prereq->kode_subject }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span style="color: var(--text-muted); font-size: 0.75rem;">-</span>
                                @endif
                            </td>
                            <!-- <td>{{ $s->jenis_subject }}</td> -->
                            <td>
                                @if($s->status == 'Aktif')
                                    <span style="font-size: 0.7rem; background: #dcfce7; color: #166534; padding: 2px 6px; border-radius: 4px;">{{ $s->status }}</span>
                                @elseif($s->status == 'Revisi')
                                    <span style="font-size: 0.7rem; background: #fef08a; color: #854d0e; padding: 2px 6px; border-radius: 4px;">{{ $s->status }}</span>
                                @else
                                    <span style="font-size: 0.7rem; background: #fee2e2; color: #991b1b; padding: 2px 6px; border-radius: 4px;">{{ $s->status }}</span>
                                @endif
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                    <a href="{{ route('subjects.edit', $s->id) }}" class="btn btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">{{ __('Edit') }}</a>
                                    <form action="{{ route('subjects.destroy', $s->id) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">{{ __('Delete') }}</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; color: var(--text-muted); padding: 2rem;">{{ __('No data found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card" style="margin-top: 2rem;">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
        <span>{{ __('Mapping Mata Kuliah ke CPL') }}</span>
        <a href="{{ route('subjects.export-plo', $prodi->id) }}" class="btn btn-secondary" style="padding: 0.25rem 0.75rem; font-size: 0.75rem; background: #fff; color: #374151; border: 1px solid #d1d5db;">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 4px; vertical-align: middle;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            {{ __('Export PLO Mapping') }}
        </a>
    </div>
    <div class="card-body" style="padding: 0;">
        <div style="overflow-x: auto;">
            <table class="table table-responsive-md">
                <thead>
                    <tr>
                        <th style="width: 40px; text-align: center;">{{ __('No') }}</th>
                        <th style="width: 100px;">{{ __('Kode MK') }}</th>
                        <th>{{ __('Nama Mata Kuliah') }}</th>
                        <th style="width: 50px; text-align: center;">{{ __('SKS') }}</th>
                        @foreach($plos as $plo)
                            <th style="text-align: center; font-size: 0.7rem; min-width: 60px;" title="{{ $plo->plo_title }}">
                                {{ $plo->kode_plo }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($subjects as $index => $s)
                        <tr>
                            <td style="text-align: center;">{{ $index + 1 }}</td>
                            <td style="font-weight: 600;">{{ $s->kode_subject }}</td>
                            <td>{{ $s->nama_subject }}</td>
                            <td style="text-align: center;">{{ $s->total_sks }}</td>
                            @foreach($plos as $plo)
                                <td style="text-align: center;">
                                    @php
                                        $mapping = $s->plos->where('id', $plo->id)->first();
                                    @endphp
                                    @if($mapping)
                                        <span class="badge" style="background: #f3f4f6; color: #374151; padding: 0.2rem 0.4rem; font-weight: bold; border-radius: 4px; border: 1px solid #d1d5db; font-size: 0.75rem;" title="{{ $mapping->pivot->mapping_level == 'I' ? 'Introduced' : ($mapping->pivot->mapping_level == 'R' ? 'Reinforced' : 'Mastered') }}">
                                            {{ $mapping->pivot->mapping_level }}
                                        </span>
                                    @else
                                        <span style="color: #e5e7eb;">-</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 4 + $plos->count() }}" style="text-align: center; color: var(--text-muted); padding: 2rem;">{{ __('No data available.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
