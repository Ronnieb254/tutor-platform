@extends('layouts.app')

@section('title', 'Clinic Details')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>
            <i class="fas fa-chalkboard-teacher"></i> {{ $clinic->name ?? $clinic->title }}
        </h2>
        <a href="{{ route('examiner.dashboard') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Clinic Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-book"></i> Subject:</strong>
                            <p class="mb-0">{{ $clinic->subject->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-file-alt"></i> Paper Type:</strong>
                            <p class="mb-0"><span class="badge bg-primary">{{ $clinic->paper_type }}</span></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-calendar"></i> Date:</strong>
                            <p class="mb-0">{{ $clinic->scheduled_at->format('l, F j, Y') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-clock"></i> Time:</strong>
                            <p class="mb-0">{{ $clinic->scheduled_at->format('h:i A') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-money-bill-wave"></i> Price:</strong>
                            <p class="mb-0">KSh {{ number_format($clinic->price, 2) }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-users"></i> Enrollment:</strong>
                            <p class="mb-0">{{ $clinic->enrollments->count() }} / {{ $clinic->max_students }} students</p>
                        </div>
                    </div>

                    @if($clinic->description)
                        <hr>
                        <strong><i class="fas fa-info-circle"></i> Description:</strong>
                        <p class="mb-0">{{ $clinic->description }}</p>
                    @endif

                    <hr>
                    <div class="d-grid">
                        @if($clinic->status === 'scheduled' || $clinic->status === 'live')
                            <a href="{{ route('examiner.clinics.join', $clinic->id) }}" class="btn btn-success btn-lg">
                                <i class="fas fa-video"></i> Join Session as Moderator
                            </a>
                        @else
                            <button class="btn btn-secondary btn-lg" disabled>
                                <i class="fas fa-video"></i> Session {{ ucfirst($clinic->status) }}
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Quick Stats</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Enrolled</span>
                            <strong>{{ $clinic->enrollments->count() }}</strong>
                        </div>
                        <div class="progress" style="height: 20px;">
                            @php
                                $percentage = $clinic->max_students > 0 ? ($clinic->enrollments->count() / $clinic->max_students * 100) : 0;
                            @endphp
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percentage }}%">
                                {{ number_format($percentage, 0) }}%
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="mb-2">
                        <i class="fas fa-user-check"></i> Attended: 
                        <strong>{{ $clinic->enrollments->where('attended', true)->count() }}</strong>
                    </div>
                    <div class="mb-2">
                        <i class="fas fa-coins"></i> Revenue: 
                        <strong>KSh {{ number_format($clinic->enrollments->count() * $clinic->price, 2) }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="fas fa-users"></i> Enrolled Students ({{ $clinic->enrollments->count() }})</h5>
        </div>
        <div class="card-body">
            @if($clinic->enrollments->isEmpty())
                <div class="text-center py-4">
                    <i class="fas fa-user-slash fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No students enrolled yet.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Student Name</th>
                                <th>Email</th>
                                <th>Enrolled</th>
                                <th>Attended</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($clinic->enrollments as $index => $enrollment)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $enrollment->user->name }}</td>
                                    <td>{{ $enrollment->user->email }}</td>
                                    <td>{{ $enrollment->created_at->format('M d, Y h:i A') }}</td>
                                    <td>
                                        @if($enrollment->attended)
                                            <span class="badge bg-success">Yes</span>
                                        @else
                                            <span class="badge bg-secondary">No</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
