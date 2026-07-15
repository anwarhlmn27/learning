@extends('layouts.admin')

@section('title', __('Assessment Types Management'))



@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>{{ __('Assessment Types') }}</h4>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('Settings') }}</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0);">{{ __('Assessment Types') }}</a></li>
        </ol>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span>{{ __('Assessment Types Master Data') }}</span>
    </div>
    <div class="card-body">
        <form action="{{ route('assessment_types.store') }}" method="POST" style="margin-bottom: 2rem;">
            @csrf
            <div style="display: flex; gap: 1rem; align-items: flex-end;">
                <div style="flex: 1;">
                    <label class="form-label">{{ __('Type Name (e.g. Quiz, Project, UAS)') }} <span style="color: red;">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                    @error('name')
                        <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <button type="submit" class="btn btn-primary" style="height: 38px;">+ {{ __('Add Item') }}</button>
                </div>
            </div>
        </form>

        <div style="overflow-x: auto;">
            <table class="table table-responsive-md">
                <thead>
                    <tr>
                        <th style="width: 5%;">{{ __('No') }}</th>
                        <th>{{ __('Type Name') }}</th>
                        <th style="width: 20%;">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($types as $index => $type)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <form action="{{ route('assessment_types.update', $type->id) }}" method="POST" style="display: flex; gap: 0.5rem; margin: 0;">
                                    @csrf
                                    @method('PUT')
                                    <input type="text" name="name" value="{{ $type->name }}" class="form-control" style="padding: 0.25rem 0.5rem;" required>
                                    <button type="submit" class="btn btn-success" style="padding: 0.25rem 0.75rem; font-size: 0.75rem;">{{ __('Update Data') }}</button>
                                </form>
                            </td>
                            <td>
                                <form action="{{ route('assessment_types.destroy', $type->id) }}" method="POST" class="swal-confirm-form" data-swal-msg="{{ __('Are you sure you want to delete this?') }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" style="padding: 0.25rem 0.75rem; font-size: 0.75rem;">{{ __('Delete') }}</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                                {{ __('No data found.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
