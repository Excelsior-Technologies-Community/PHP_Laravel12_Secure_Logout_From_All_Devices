@extends('layouts.app')

@section('content')

    <style>
        body { background: #f1f5f9; }
        .security-title { font-size: 32px; font-weight: 700; color: #0f172a; }
        .stat-card { border: none; border-radius: 18px; transition: all .3s ease; background: #fff; }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-icon { font-size: 35px; }
        .shadow-soft { box-shadow: 0 8px 25px rgba(0,0,0,.08); }
        .custom-card { border: none; border-radius: 18px; overflow: hidden; }
        .custom-header { background: linear-gradient(135deg, #2563eb, #1e40af); color: #fff; font-weight: 600; padding: 15px 20px; }
        .custom-header-danger { background: linear-gradient(135deg, #dc2626, #991b1b); color: #fff; font-weight: 600; padding: 15px 20px; }
        .custom-header-success { background: linear-gradient(135deg, #16a34a, #15803d); color: #fff; font-weight: 600; padding: 15px 20px; }
        .custom-header-warning { background: linear-gradient(135deg, #d97706, #b45309); color: #fff; font-weight: 600; padding: 15px 20px; }
        .table thead { background: #0f172a; color: white; }
        .table-hover tbody tr:hover { background: #eff6ff; }
        .form-control, .form-select { border-radius: 10px; }
        .btn { border-radius: 10px; }
        .pagination .page-link { margin: 0 4px; border-radius: 8px; }
        .pagination .active .page-link { background: #2563eb; border-color: #2563eb; }
        .trusted-badge { background: #dcfce7; color: #16a34a; border: 1px solid #86efac; }
        .untrusted-badge { background: #fef2f2; color: #dc2626; border: 1px solid #fca5a5; }
        .suspicious-row { background: #fff7ed !important; }
        #live-count { font-size: 2rem; font-weight: 700; }
        .pulse { animation: pulse 2s infinite; }
        @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.5} }
    </style>

    <div class="container py-5">

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="security-title">🔐 Security Dashboard</h2>
            <div class="d-flex gap-2 align-items-center">
                @if($suspiciousCount > 0)
                    <span class="badge bg-danger px-3 py-2">⚠️ {{ $suspiciousCount }} Suspicious Login(s)</span>
                @endif
                <span class="badge bg-success px-3 py-2">Protected Account</span>
            </div>
        </div>

        {{-- SUCCESS --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
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

        {{-- STAT CARDS --}}
        <div class="row g-4 mb-4">

            <div class="col-md-2">
                <div class="card stat-card shadow-soft text-center">
                    <div class="card-body">
                        <div class="stat-icon text-primary mb-2">💻</div>
                        <h2 class="fw-bold text-primary" id="live-count">{{ $totalDevices }}</h2>
                        <p class="mb-0 small">Total Devices</p>
                        <span class="badge bg-primary pulse mt-1" style="font-size:10px">LIVE</span>
                    </div>
                </div>
            </div>

            <div class="col-md-2">
                <div class="card stat-card shadow-soft text-center">
                    <div class="card-body">
                        <div class="stat-icon text-success mb-2">🖥️</div>
                        <h2 class="fw-bold text-success">{{ $currentDevices }}</h2>
                        <p class="mb-0 small">Current Device</p>
                    </div>
                </div>
            </div>

            <div class="col-md-2">
                <div class="card stat-card shadow-soft text-center">
                    <div class="card-body">
                        <div class="stat-icon text-warning mb-2">📱</div>
                        <h2 class="fw-bold text-warning">{{ $otherDevices }}</h2>
                        <p class="mb-0 small">Other Devices</p>
                    </div>
                </div>
            </div>

            <div class="col-md-2">
                <div class="card stat-card shadow-soft text-center">
                    <div class="card-body">
                        <div class="stat-icon text-info mb-2">🛡️</div>
                        <h2 class="fw-bold text-info">{{ $trustedCount }}</h2>
                        <p class="mb-0 small">Trusted Devices</p>
                    </div>
                </div>
            </div>

            <div class="col-md-2">
                <div class="card stat-card shadow-soft text-center">
                    <div class="card-body">
                        <div class="stat-icon text-danger mb-2">🕒</div>
                        <h2 class="fw-bold text-danger">{{ $historyCount }}</h2>
                        <p class="mb-0 small">Login Records</p>
                    </div>
                </div>
            </div>

            <div class="col-md-2">
                <div class="card stat-card shadow-soft text-center">
                    <div class="card-body">
                        <div class="stat-icon text-orange mb-2">⚠️</div>
                        <h2 class="fw-bold" style="color:#ea580c">{{ $suspiciousCount }}</h2>
                        <p class="mb-0 small">Suspicious Logins</p>
                    </div>
                </div>
            </div>

        </div>

        {{-- LOGOUT ALL DEVICES --}}
        <div class="card custom-card shadow-soft mb-4">
            <div class="custom-header-danger">🚪 Logout From All Devices</div>
            <div class="card-body">
                <p class="text-muted">Logout your account from all browsers and devices except your current session.</p>
                <form method="POST" action="{{ route('logout.all') }}">
                    @csrf
                    <div class="row g-3 align-items-end">
                        <div class="col-md-5">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Enter current password" required>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-danger w-100">Logout Other Devices</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- ACTIVE SESSIONS --}}
        <div class="card custom-card shadow-soft mb-4">
            <div class="custom-header">📡 Active Login Sessions</div>
            <div class="card-body">

                {{-- SEARCH & FILTER --}}
                <form method="GET" action="{{ route('security.page') }}" class="row g-3 mb-4">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Search Browser, Platform or IP" value="{{ request('search') }}">
                    </div>
                    <div class="col-md-4">
                        <select name="device_filter" class="form-select">
                            <option value="">All Devices</option>
                            <option value="current" {{ request('device_filter') == 'current' ? 'selected' : '' }}>Current Device</option>
                            <option value="other" {{ request('device_filter') == 'other' ? 'selected' : '' }}>Other Devices</option>
                            <option value="trusted" {{ request('device_filter') == 'trusted' ? 'selected' : '' }}>Trusted Devices</option>
                            <option value="untrusted" {{ request('device_filter') == 'untrusted' ? 'selected' : '' }}>Untrusted Devices</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100">Search</button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('security.page') }}" class="btn btn-outline-secondary w-100">Reset</a>
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
                                <th>Activity</th>
                                <th>Trust</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sessions as $session)
                                <tr>
                                    <td>{{ $session->browser }}</td>
                                    <td>{{ $session->platform }}</td>
                                    <td><code>{{ $session->ip_address }}</code></td>
                                    <td>{{ \Carbon\Carbon::createFromTimestamp($session->last_activity)->diffForHumans() }}</td>
                                    <td>
                                        @if($session->is_current_device)
                                            <span class="badge bg-success">Current Device</span>
                                        @else
                                            <span class="badge bg-secondary">Other Device</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($session->last_seen == 'Online Now')
                                            <span class="badge bg-success">Online Now</span>
                                        @else
                                            <span class="badge bg-warning text-dark">{{ $session->last_seen }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($session->is_trusted)
                                            <span class="badge trusted-badge">🛡️ Trusted</span>
                                            <form method="POST" action="{{ route('security.untrust-device') }}" class="d-inline mt-1">
                                                @csrf @method('DELETE')
                                                <input type="hidden" name="device_token" value="{{ $session->device_token }}">
                                                <button class="btn btn-outline-secondary btn-sm mt-1" onclick="return confirm('Remove trust?')">Untrust</button>
                                            </form>
                                        @else
                                            <span class="badge untrusted-badge">⚠️ Untrusted</span>
                                            <form method="POST" action="{{ route('security.trust-device') }}" class="d-inline mt-1">
                                                @csrf
                                                <input type="hidden" name="device_token" value="{{ $session->device_token }}">
                                                <button class="btn btn-outline-success btn-sm mt-1">Trust</button>
                                            </form>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!$session->is_current_device)
                                            <form method="POST" action="{{ route('security.logout-session', $session->id) }}">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-danger btn-sm" onclick="return confirm('Logout this device?')">
                                                    Logout
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-muted small">Current</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">No active sessions found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- LOGIN HISTORY --}}
        <div class="card custom-card shadow-soft">
            <div class="custom-header-warning">🕒 Recent Login Activity</div>
            <div class="card-body">

                <div class="d-flex justify-content-end mb-3">
                    <form method="POST" action="{{ route('security.clear-history') }}">
                        @csrf @method('DELETE')
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
                                <th>📍 Location</th>
                                <th>Login Time</th>
                                <th>Logout Time</th>
                                <th>Alert</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activities as $activity)
                                <tr class="{{ $activity->is_suspicious ? 'suspicious-row' : '' }}">
                                    <td>{{ $activity->browser }}</td>
                                    <td>{{ $activity->platform }}</td>
                                    <td><code>{{ $activity->ip_address }}</code></td>
                                    <td>{{ $activity->location ?? 'Unknown' }}</td>
                                    <td>{{ $activity->login_at }}</td>
                                    <td>
                                        @if($activity->logout_at)
                                            {{ $activity->logout_at }}
                                        @else
                                            <span class="badge bg-success">Active Session</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($activity->is_suspicious)
                                            <span class="badge bg-danger">⚠️ New Device</span>
                                        @else
                                            <span class="badge bg-success">✅ Known</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No login history found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- PAGINATION --}}
                <div class="d-flex justify-content-center mt-4">
                    <ul class="pagination">
                        @for($i = 1; $i <= $activities->lastPage(); $i++)
                            <li class="page-item {{ $activities->currentPage() == $i ? 'active' : '' }}">
                                <a class="page-link" href="{{ $activities->url($i) }}">{{ $i }}</a>
                            </li>
                        @endfor
                    </ul>
                </div>

            </div>
        </div>

    </div>

    {{-- REAL-TIME SESSION COUNT --}}
    <script>
        function updateSessionCount() {
            fetch('{{ route('security.session-count') }}')
                .then(res => res.json())
                .then(data => {
                    document.getElementById('live-count').textContent = data.count;
                })
                .catch(() => {});
        }
        // Update every 10 seconds
        setInterval(updateSessionCount, 10000);
    </script>

@endsection
