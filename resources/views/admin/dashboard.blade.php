@extends('layout')
@section('content')
<h1>Admin Analytics</h1>

<div class="dashboard-grid">
    <div class="stat-card"><span class="stat-label">Individual Users</span><span class="stat-value">{{ $stats['total_users'] }}</span></div>
    <div class="stat-card"><span class="stat-label">Active Exchanges</span><span class="stat-value">{{ $stats['active_exchanges'] }}</span></div>
    <div class="stat-card"><span class="stat-label">Disputed Exchanges</span><span class="stat-value">{{ $stats['disputed_exchanges'] }}</span></div>
    <div class="stat-card"><span class="stat-label">Capital Applications</span><span class="stat-value">{{ $stats['capital_applications'] }}</span></div>
</div>

<h2>Registrations by Month</h2>
<canvas id="registrationsChart" height="100"></canvas>

<div class="quick-actions">
    <a href="{{ route('capital.index') }}" class="btn-secondary">Review Capital Applications</a>
    <a href="{{ route('admin.users') }}" class="btn-secondary">Manage Users</a>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('registrationsChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($registrationsByMonth->keys()) !!},
        datasets: [{ label: 'New Users', data: {!! json_encode($registrationsByMonth->values()) !!}, backgroundColor: '#0b3d91' }]
    },
  options: { responsive: true, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
});
</script>
@endsection