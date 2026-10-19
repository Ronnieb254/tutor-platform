@extends('layouts.app')

@section('title', 'Create Examiner Clinic')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Create New Examiner Clinic</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('examiner.clinics.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="subject_id" class="form-label">Subject <span class="text-danger">*</span></label>
                            <select name="subject_id" id="subject_id" class="form-select @error('subject_id') is-invalid @enderror" required>
                                <option value="">-- Select Subject --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('subject_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('subject_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Paper Type <span class="text-danger">*</span></label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input @error('paper_type') is-invalid @enderror" type="radio" name="paper_type" id="pp1" value="PP1" {{ old('paper_type') == 'PP1' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="pp1">PP1</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input @error('paper_type') is-invalid @enderror" type="radio" name="paper_type" id="pp2" value="PP2" {{ old('paper_type') == 'PP2' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="pp2">PP2</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input @error('paper_type') is-invalid @enderror" type="radio" name="paper_type" id="pp3" value="PP3" {{ old('paper_type') == 'PP3' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="pp3">PP3</label>
                                </div>
                            </div>
                            @error('paper_type')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label">Clinic Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" placeholder="e.g., Mathematics PP1 - Algebra Revision" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" rows="4" class="form-control @error('description') is-invalid @enderror" 
                                      placeholder="Describe what topics will be covered, exam strategies, etc.">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="price" class="form-label">Price (KSh) <span class="text-danger">*</span></label>
                                <input type="number" name="price" id="price" class="form-control @error('price') is-invalid @enderror" 
                                       value="{{ old('price', 300) }}" min="0" step="0.01" required>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="max_students" class="form-label">Max Students <span class="text-danger">*</span></label>
                                <input type="number" name="max_students" id="max_students" class="form-control @error('max_students') is-invalid @enderror" 
                                       value="{{ old('max_students', 30) }}" min="1" max="50" required>
                                @error('max_students')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Maximum 50 students per session</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="scheduled_at" class="form-label">Scheduled Date & Time <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="scheduled_at" id="scheduled_at" class="form-control @error('scheduled_at') is-invalid @enderror" 
                                   value="{{ old('scheduled_at') }}" required>
                            @error('scheduled_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> 
                            <small>
                                When this clinic reaches max capacity, a new session will automatically be created one week later 
                                with the same settings.
                            </small>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> Create Clinic
                            </button>
                            <a href="{{ route('examiner.dashboard') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
