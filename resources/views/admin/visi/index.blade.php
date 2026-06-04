@extends('layouts.admin')

@section('title', 'Vision & Mission')

@section('header_left')
    <h1 style="font-size: 1.25rem; font-weight: 700; margin: 0;">Vision & Mission</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <span>Vision & Mission List</span>
        <a href="{{ route('visi.create') }}" class="btn btn-primary">Add Vision & Mission</a>
    </div>
    <div class="card-body" style="padding: 0;">
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>{{ __('Entity Type') }}</th>
                        <th>{{ __('Entity Name') }}</th>
                        <th>{{ __('Vision') }}</th>
                        <th>{{ __('Mission') }}</th>
                        <th>{{ __('Documents') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($visis as $v)
                        <tr>
                            <td>
                                @php
                                    $type = str_replace('App\\Models\\', '', $v->visible_type);
                                @endphp
                                <span class="badge" style="background: var(--primary-light); color: var(--primary);">{{ $type }}</span>
                            </td>
                            <td style="font-weight: 600;">
                                @if($type == 'Univ')
                                    {{ $v->visible->nama_univ ?? 'Deleted Univ' }}
                                @elseif($type == 'Fakultas')
                                    {{ $v->visible->nama_fakultas ?? 'Deleted Faculty' }}
                                @elseif($type == 'Prodi')
                                    {{ $v->visible->nama_prodi ?? 'Deleted Study Program' }}
                                @endif
                            </td>
                            <td>{{ Str::limit($v->visi, 50) }}</td>
                            <td>
                                @php
                                    $misiList = $v->details->where('type', 'misi')->sortBy('urutan');
                                    $firstMisi = $misiList->first();
                                @endphp
                                @if($firstMisi)
                                    {{ Str::limit($firstMisi->konten, 50) }} 
                                    @if($misiList->count() > 1)
                                        <span style="font-size: 0.75rem; color: var(--text-muted);"> (+{{ $misiList->count() - 1 }} more)</span>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.25rem; flex-wrap: wrap;">
                                    @if($v->doc_penyusunan) <span title="Penyusunan">📄</span> @endif
                                    @if($v->doc_pengesahan) <span title="Pengesahan">📄</span> @endif
                                    @if($v->doc_sosialisasi) <span title="Sosialisasi">📄</span> @endif
                                    @if($v->doc_hasil_survey) <span title="Survey">📄</span> @endif
                                </div>
                            </td>
                            <td style="display: flex; gap: 0.5rem;">
                                <a href="{{ route('visi.edit', $v->id) }}" class="btn btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Edit</a>
                                <form action="{{ route('visi.destroy', $v->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--text-muted);">No data found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
