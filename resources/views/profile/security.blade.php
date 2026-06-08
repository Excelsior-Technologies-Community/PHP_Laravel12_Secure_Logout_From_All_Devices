@extends('layouts.app')

@section('content')

    <style>
        body {
            background: #f1f5f9;
        }

        .security-title {
            font-size: 32px;
            font-weight: 700;
            color: #0f172a;
        }

        .stat-card {
            border: none;
            border-radius: 18px;
            transition: all .3s ease;
            background: #fff;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            font-size: 35px;
        }

        .shadow-soft {
            box-shadow: 0 8px 25px rgba(0, 0, 0, .08);
        }

        .custom-card {
            border: none;
            border-radius: 18px;
            overflow: hidden;
        }

        .custom-header {
            background: linear-gradient(135deg, #2563eb, #1e40af);
            color: #fff;
            font-weight: 600;
            padding: 15px 20px;
        }

        .table thead {
            background: #0f172a;
            color: white;
        }

        .table-hover tbody tr:hover {
            background: #eff6ff;
        }

        .form-control,
        .form-select {
            border-radius: 10px;
        }

        .btn {
            border-radius: 10px;
        }

        .pagination .page-link {
            margin: 0 4px;
            border-radius: 8px;
        }

        .pagination .active .page-link {
            background: #2563eb;
            border-color: #2563eb;
        }
    </style>

    <div class="container py-5">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <h2 class="security-title">
                🔐 Security Dashboard
            </h2>

            <span class="badge bg-success px-3 py-2">
                Protected Account
            </span>

        </div>

        {{-- SUCCESS --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">

                {{ session('success') }}

                <button type="button" class="btn-close" data-bs-dismiss="alert">
                </button>

            </div>
        @endif

        {{-- ERRORS --}}
        @if($errors->any())

            <div class="alert alert-danger">

                @foreach($errors->all() as $error)

                    <div>{{ $error }}</div>

                @endforeach

            </div>

        @endif

        {{-- DASHBOARD CARDS --}}
        <div class="row g-4 mb-4">

            <div class="col-md-3">

                <div class="card stat-card shadow-soft text-center">

                    <div class="card-body">

                        <div class="stat-icon text-primary mb-2">
                            💻
                        </div>

                        <h2 class="fw-bold text-primary">
                            {{ $totalDevices }}
                        </h2>

                        <p class="mb-0">
                            Total Devices
                        </p>

                    </div>

                </div>

            </div>

            <div class="col-md-3">

                <div class="card stat-card shadow-soft text-center">

                    <div class="card-body">

                        <div class="stat-icon text-success mb-2">
                            🖥️
                        </div>

                        <h2 class="fw-bold text-success">
                            {{ $currentDevices }}
                        </h2>

                        <p class="mb-0">
                            Current Device
                        </p>

                    </div>

                </div>

            </div>

            <div class="col-md-3">

                <div class="card stat-card shadow-soft text-center">

                    <div class="card-body">

                        <div class="stat-icon text-warning mb-2">
                            📱
                        </div>

                        <h2 class="fw-bold text-warning">
                            {{ $otherDevices }}
                        </h2>

                        <p class="mb-0">
                            Other Devices
                        </p>

                    </div>

                </div>

            </div>

            <div class="col-md-3">

                <div class="card stat-card shadow-soft text-center">

                    <div class="card-body">

                        <div class="stat-icon text-danger mb-2">
                            🕒
                        </div>

                        <h2 class="fw-bold text-danger">
                            {{ $historyCount }}
                        </h2>

                        <p class="mb-0">
                            Login Records
                        </p>

                    </div>

                </div>

            </div>

        </div>

        {{-- LOGOUT DEVICES --}}
        <div class="card custom-card shadow-soft mb-4">

            <div class="custom-header">
                Logout From All Devices
            </div>

            <div class="card-body">

                <p class="text-muted">
                    Logout your account from all browsers and devices except your current session.
                </p>

                <form method="POST" action="{{ route('logout.all') }}">

                    @csrf

                    <div class="mb-3">

                        <label class="form-label">
                            Confirm Password
                        </label>

                        <input type="password" name="password" class="form-control" placeholder="Enter current password"
                            required>

                    </div>

                    <button class="btn btn-danger">

                        Logout Other Devices

                    </button>

                </form>

            </div>

        </div>

        {{-- ACTIVE SESSIONS --}}
        <div class="card custom-card shadow-soft mb-4">

            <div class="custom-header">
                Active Login Sessions
            </div>

            <div class="card-body">

                <form method="GET" action="{{ route('security.page') }}" class="row g-3 mb-4">

                    <div class="col-md-5">

                        <input type="text" name="search" class="form-control" placeholder="Search Browser, Platform or IP"
                            value="{{ request('search') }}">

                    </div>

                    <div class="col-md-4">

                        <select name="device_filter" class="form-select">

                            <option value="">
                                All Devices
                            </option>

                            <option value="current" {{ request('device_filter') == 'current' ? 'selected' : '' }}>
                                Current Device
                            </option>

                            <option value="other" {{ request('device_filter') == 'other' ? 'selected' : '' }}>
                                Other Devices
                            </option>

                        </select>

                    </div>

                    <div class="col-md-3">

                        <button class="btn btn-primary w-100">
                            Search
                        </button>

                    </div>

                </form>

                <div class="table-responsive">

                    <table class="table table-hover table-bordered align-middle">

                        <thead>

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

                                    <td>{{ $session->browser }}</td>

                                    <td>{{ $session->platform }}</td>

                                    <td>{{ $session->ip_address }}</td>

                                    <td>
                                        {{ \Carbon\Carbon::createFromTimestamp($session->last_activity)->diffForHumans() }}
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
                                                Online Now
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

                                    <td colspan="6" class="text-center">

                                        No active sessions found.

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

        {{-- LOGIN HISTORY --}}
        <div class="card custom-card shadow-soft">

            <div class="custom-header">
                Recent Login Activity
            </div>

            <div class="card-body">

                <div class="d-flex justify-content-end mb-3">

                    <form method="POST" action="{{ route('security.clear-history') }}">

                        @csrf
                        @method('DELETE')

                        <button class="btn btn-danger btn-sm" onclick="return confirm('Clear all login history?')">

                            Clear History

                        </button>

                    </form>

                </div>

                <div class="table-responsive">

                    <table class="table table-hover table-bordered align-middle">

                        <thead>

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

                                    <td>{{ $activity->browser }}</td>

                                    <td>{{ $activity->platform }}</td>

                                    <td>{{ $activity->ip_address }}</td>

                                    <td>{{ $activity->login_at }}</td>

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

                                    <td colspan="5" class="text-center">

                                        No login history found.

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

                {{-- NUMBER PAGINATION ONLY --}}
                <div class="d-flex justify-content-center mt-4">

                    <ul class="pagination">

                        @for($i = 1; $i <= $activities->lastPage(); $i++)

                            <li class="page-item {{ $activities->currentPage() == $i ? 'active' : '' }}">

                                <a class="page-link" href="{{ $activities->url($i) }}">

                                    {{ $i }}

                                </a>

                            </li>

                        @endfor

                    </ul>

                </div>

            </div>

        </div>

    </div>

@endsection