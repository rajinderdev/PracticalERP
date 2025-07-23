@extends('admin.layout')

@section('content')
<div class="main-content flex-grow-1">
    <!-- Welcome Message -->
    <div class="welcome-message p-4 bg-light rounded mb-4">
        <h2 class="display-5 fw-bold">Hello,</h2>
        <p class="lead">Welcome, Dashboard is the place to know and do all that are relevant. Book appointments, create sale, lab orders or navigate to other particular pages for detailed views.</p>
    </div>

    <!-- Task Cards -->
    <div class="row mb-4">
        <div class="col">
            <div class="card text-center">
                <div class="card-body">
                    <div class="circle bg-light mb-2">0</div>
                    <div class="card-title">Incomplete</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card text-center">
                <div class="card-body">
                    <div class="circle bg-success mb-2 text-white">0</div>
                    <div class="card-title">Complete</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card text-center">
                <div class="card-body">
                    <div class="circle bg-danger mb-2 text-white">0</div>
                    <div class="card-title">High</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card text-center">
                <div class="card-body">
                    <div class="circle bg-warning mb-2 text-dark">0</div>
                    <div class="card-title">Medium</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card text-center">
                <div class="card-body">
                    <div class="circle bg-light mb-2">0</div>
                    <div class="card-title">Low</div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<!-- Bootstrap Icons CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.js"></script>
@endpush