@extends('layouts.app')

@section('content')
<div class="container mt-5">

    <h3 class="mb-4">Security Settings</h3>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Error Message --}}
    @if($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="card">
        <div class="card-body">

            <h5 class="card-title mb-3">
                Logout from all devices
            </h5>

            <form method="POST" action="{{ route('logout.all') }}">
                @csrf

                <div class="mb-3">
                    <label for="password" class="form-label">
                        Confirm your password
                    </label>

                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="form-control"
                        placeholder="Enter your current password"
                        required
                    >
                </div>

                <button type="submit" class="btn btn-danger">
                    Logout From All Devices
                </button>
            </form>

        </div>
    </div>

</div>
@endsection
