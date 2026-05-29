@extends('layouts.app')

@section('content')

<div class="container py-5">

    <h2 class="mb-4 fw-bold">
        Security Settings
    </h2>

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))

    <div class="alert alert-success">
        {{ session('success') }}
    </div>

    @endif

    {{-- ERROR MESSAGE --}}
    @if($errors->any())

    <div class="alert alert-danger">

        @foreach($errors->all() as $error)

        <div>{{ $error }}</div>

        @endforeach

    </div>

    @endif

    {{-- ACTIVE DEVICE COUNT --}}
    <div class="alert alert-primary shadow-sm">

        Active Devices:
        <strong>{{ $totalDevices }}</strong>

    </div>

    {{-- LOGOUT ALL DEVICES --}}
    <div class="card shadow-sm mb-4 border-0">

        <div class="card-body">

            <h4 class="mb-3">
                Logout From All Devices
            </h4>

            <p class="text-muted">
                This will logout your account
                from all browsers and devices.
            </p>

            <form method="POST"
                action="{{ route('logout.all') }}">

                @csrf

                <div class="mb-3">

                    <label class="form-label">
                        Confirm Password
                    </label>

                    <input type="password"
                        name="password"
                        class="form-control"
                        placeholder="Enter current password"
                        required>

                </div>

                <button type="submit"
                    class="btn btn-danger">

                    Logout Other Devices

                </button>

            </form>

        </div>

    </div>

    {{-- ACTIVE DEVICE SESSIONS --}}
    <div class="card shadow-sm border-0 mb-4">

        <div class="card-body">

            <h4 class="mb-4">
                Active Login Sessions
            </h4>

            <div class="table-responsive">

                <table class="table table-bordered align-middle">

                    <thead class="table-light">

                        <tr>

                            <th>Browser</th>

                            <th>Platform</th>

                            <th>IP Address</th>

                            <th>Last Activity</th>

                            <th>Status</th>

                            <th>Activity Status</th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($sessions as $session)

                        <tr>

                            <td>
                                {{ $session->browser }}
                            </td>

                            <td>
                                {{ $session->platform }}
                            </td>

                            <td>
                                {{ $session->ip_address }}
                            </td>

                            <td>

                                {{ \Carbon\Carbon::createFromTimestamp(
                                    $session->last_activity
                                )->diffForHumans() }}

                            </td>

                            <td>

                                @if($session->is_current_device)

                                <span class="badge bg-success">
                                    Current Device
                                </span>

                                @else

                                <span class="badge bg-secondary">
                                    Other Device
                                </span>

                                @endif

                            </td>

                            <td>

                                @if($session->last_seen == 'Online Now')

                                <span class="badge bg-success">
                                    {{ $session->last_seen }}
                                </span>

                                @else

                                <span class="badge bg-warning text-dark">
                                    {{ $session->last_seen }}
                                </span>

                                @endif

                            </td>

                        </tr>

                        @empty

                        <tr>

                            <td colspan="6"
                                class="text-center text-muted">

                                No active sessions found.

                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

    {{-- LOGIN ACTIVITY HISTORY --}}
    <div class="card shadow-sm border-0">

        <div class="card-body">

            <h4 class="mb-4">
                Recent Login Activity
            </h4>

            <div class="table-responsive">

                <table class="table table-bordered align-middle">

                    <thead class="table-light">

                        <tr>

                            <th>Browser</th>

                            <th>Platform</th>

                            <th>IP Address</th>

                            <th>Login Time</th>

                            <th>Logout Time</th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($activities as $activity)

                        <tr>

                            <td>
                                {{ $activity->browser }}
                            </td>

                            <td>
                                {{ $activity->platform }}
                            </td>

                            <td>
                                {{ $activity->ip_address }}
                            </td>

                            <td>
                                {{ $activity->login_at }}
                            </td>

                            <td>

                                @if($activity->logout_at)

                                {{ $activity->logout_at }}

                                @else

                                <span class="badge bg-success">
                                    Active Session
                                </span>

                                @endif

                            </td>

                        </tr>

                        @empty

                        <tr>

                            <td colspan="5"
                                class="text-center text-muted">

                                No login history found.

                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

@endsection