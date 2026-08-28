@extends('layouts.admin')

@section('title', 'PLO Details')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>{{ __('PLO Details: ' . $plo->kode_plo) }}</h4>
            <p class="mb-0">{{ $plo->plo_title }}</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('Academic & OBE') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('plo.manage', $plo->id_prodi) }}">{{ __('PLO') }}</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0);">{{ __('Details') }}</a></li>
        </ol>
    </div>
</div>



<div class="row">
    <!-- PLO Information -->
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">PLO Information</h4>
                <a href="{{ route('plo.manage', $plo->id_prodi) }}" class="btn btn-sm btn-secondary">Back to Manage PLO</a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3"><strong>PLO Code:</strong> <br> {{ $plo->kode_plo }}</div>
                    <div class="col-md-9"><strong>Title:</strong> <br> {{ $plo->plo_title }}</div>
                    <div class="col-md-12 mt-3"><strong>Formulation (Rumusan):</strong> <br> {{ $plo->rumusan_plo }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Terms Section -->
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Key Terms & Its Definition</h4>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addTermModal">
                    <i class="fas fa-plus"></i> Add Key Term
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Description / Definition</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($plo->terms as $index => $term)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $term->description }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#editTermModal{{ $term->id }}">Edit</button>
                                        <form action="{{ route('plo.terms.destroy', $term->id) }}" method="POST" class="d-inline swal-confirm-form" data-swal-msg="Delete this term?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>

                                <!-- Edit Term Modal -->
                                <div class="modal fade" id="editTermModal{{ $term->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Key Term</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('plo.terms.update', $term->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <div class="form-group mb-3">
                                                        <label>Description / Definition <span class="text-danger">*</span></label>
                                                        <textarea name="description" class="form-control" rows="4" required>{{ $term->description }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary">Save changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">No Key Terms found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Indicators Section -->
    <div class="col-xl-12 mt-3">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Performance Indicators</h4>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addIndicatorModal">
                    <i class="fas fa-plus"></i> Add PI
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Indicator Description</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($plo->indicators as $indicator)
                                <tr>
                                    <td><strong>{{ $indicator->indicator_code }}</strong></td>
                                    <td>{{ $indicator->indicator_description }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#editIndicatorModal{{ $indicator->id }}">Edit</button>
                                        <form action="{{ route('plo.indicators.destroy', $indicator->id) }}" method="POST" class="d-inline swal-confirm-form" data-swal-msg="Delete this indicator?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>

                                <!-- Edit Indicator Modal -->
                                <div class="modal fade" id="editIndicatorModal{{ $indicator->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Performance Indicator</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('plo.indicators.update', $indicator->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <div class="form-group mb-3">
                                                        <label>Indicator Code (e.g., PLO1:1) <span class="text-danger">*</span></label>
                                                        <input type="text" name="indicator_code" class="form-control" value="{{ $indicator->indicator_code }}" required>
                                                    </div>
                                                    <div class="form-group mb-3">
                                                        <label>Description <span class="text-danger">*</span></label>
                                                        <textarea name="indicator_description" class="form-control" rows="4" required>{{ $indicator->indicator_description }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary">Save changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">No Performance Indicators found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Term Modal -->
<div class="modal fade" id="addTermModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Key Term</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('plo.terms.store', $plo->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label>Description / Definition <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" rows="4" placeholder="Enter term definition..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Key Term</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Indicator Modal -->
<div class="modal fade" id="addIndicatorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Performance Indicator</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('plo.indicators.store', $plo->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label>Indicator Code <span class="text-danger">*</span></label>
                        <input type="text" name="indicator_code" class="form-control" placeholder="e.g. PLO1:1" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Description <span class="text-danger">*</span></label>
                        <textarea name="indicator_description" class="form-control" rows="4" placeholder="Enter indicator description..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Indicator</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
