@extends('layouts.app')

@section('title', 'Apply as Examiner')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Become an Examiner</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h5><i class="fas fa-info-circle"></i> What is an Examiner?</h5>
                        <p class="mb-0">
                            As an examiner, you can create and host group revision clinics for students preparing for exams.
                            Each clinic focuses on a specific subject and paper type (PP1, PP2, PP3), with a maximum of 30 students per session.
                        </p>
                    </div>

                    <h5 class="mt-4">Requirements:</h5>
                    <ul class="list-group list-group-flush mb-4">
                        <li class="list-group-item"><i class="fas fa-check text-success"></i> Verified tutor status</li>
                        <li class="list-group-item"><i class="fas fa-check text-success"></i> Active teaching profile</li>
                        <li class="list-group-item"><i class="fas fa-check text-success"></i> Good standing with platform</li>
                    </ul>

                    <h5 class="mt-4">Benefits:</h5>
                    <ul class="list-group list-group-flush mb-4">
                        <li class="list-group-item"><i class="fas fa-star text-warning"></i> Create unlimited clinics</li>
                        <li class="list-group-item"><i class="fas fa-star text-warning"></i> Set your own pricing</li>
                        <li class="list-group-item"><i class="fas fa-star text-warning"></i> Reach up to 30 students per session</li>
                        <li class="list-group-item"><i class="fas fa-star text-warning"></i> Automated session management</li>
                        <li class="list-group-item"><i class="fas fa-star text-warning"></i> Session recording capabilities</li>
                    </ul>

                    <form action="{{ route('examiner.apply') }}" method="POST">
                        @csrf
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="terms" required>
                            <label class="form-check-label" for="terms">
                                I agree to the examiner terms and conditions, including maintaining professional conduct
                                and delivering quality educational content.
                            </label>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane"></i> Apply Now
                            </button>
                            <a href="{{ route('tutor.dashboard') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Dashboard
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
