@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-gray-800">Dashboard SimSarpras</h2>
        <span class="badge bg-light text-dark border p-2">
            <i class="far fa-calendar-alt me-1"></i> {{ date('d F Y') }}
        </span>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-start border-primary border-4 shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Aset</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalAset) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-boxes fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-start border-success border-4 shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Kondisi Baik</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($asetBaik) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-start border-danger border-4 shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Kondisi Rusak</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($asetRusak) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tools fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-start border-info border-4 shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Backup Terakhir</div>
                            <div class="small mb-0 font-weight-bold text-gray-800">{{ $lastBackup }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-database fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3 d-flex align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chart-area me-2"></i>Monitoring Utilitas (7 Hari)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area" style="height: 320px;">
                        <canvas id="utilitasChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-dark">Status Koneksi & Sistem</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <span><i class="fab fa-telegram text-info me-2"></i>Notifikasi Telegram</span>
                        <span class="badge rounded-pill bg-success">AKTIF</span>
                    </div>
                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-server text-primary me-2"></i>Database Server</span>
                        <span class="badge rounded-pill bg-success">ONLINE</span>
                    </div>
                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-shield-alt text-warning me-2"></i>Maintenance Mode</span>
                        <span class="badge rounded-pill bg-secondary">OFF</span>
                    </div>
                    <hr>
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary btn-sm" onclick="location.reload()">
                            <i class="fas fa-sync-alt me-1"></i> Refresh Data
                        </button>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 bg-primary text-white">
                <div class="card-body">
                    <div class="small text-white-50">Kepatuhan Kalibrasi</div>
                    <div class="h4 mb-0">94%</div>
                    <div class="progress progress-sm mt-2" style="height: 5px;">
                        <div class="progress-bar bg-white" role="progressbar" style="width: 94%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('utilitasChart').getContext('2d');
        
        // Gradien untuk grafik agar lebih elegan
        let gradientAir = ctx.createLinearGradient(0, 0, 0, 400);
        gradientAir.addColorStop(0, 'rgba(13, 202, 240, 0.4)');
        gradientAir.addColorStop(1, 'rgba(13, 202, 240, 0.0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chartLabels),
                datasets: [
                    {
                        label: 'Solar Genset (L)',
                        data: @json($dataSolar),
                        borderColor: '#ffc107',
                        borderWidth: 3,
                        backgroundColor: 'transparent',
                        pointBackgroundColor: '#ffc107',
                        tension: 0.4
                    },
                    {
                        label: 'Air Bersih (m3)',
                        data: @json($dataAir),
                        borderColor: '#0dcaf0',
                        borderWidth: 3,
                        backgroundColor: gradientAir,
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'pH IPAL',
                        data: @json($dataIpal),
                        borderColor: '#198754',
                        borderWidth: 2,
                        borderDash: [5, 5], // Garis putus-putus untuk pH
                        backgroundColor: 'transparent',
                        pointStyle: 'circle',
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                },
                scales: {
                    y: { 
                        beginAtZero: false,
                        grid: { drawBorder: false, color: '#f0f0f0' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    });
</script>
@endsection