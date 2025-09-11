<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VigaZaFarm Dashboard - Burung Puyuh Management System</title>
    
    <!-- Core CSS -->
    <link href="<?= base_url('assets/css/style.min.css'); ?>" rel="stylesheet">
    <link href="<?= base_url('assets/plugins/font-awesome/css/fontawesome-all.min.css'); ?>" rel="stylesheet">
    
    <!-- Custom Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Dashboard Styles -->
    <style>
        :root {
            --primary: #0ea5e9;
            --primary-dark: #0284c7;
            --secondary: #64748b;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #06b6d4;
            --purple: #8b5cf6;
            
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --text-muted: #94a3b8;
            
            --bg-primary: #ffffff;
            --bg-secondary: #f8fafc;
            --bg-tertiary: #f1f5f9;
            
            --border-color: #e2e8f0;
            --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            --radius: 8px;
            --radius-lg: 12px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-secondary);
            color: var(--text-primary);
            margin: 0;
            padding: 0;
        }

        .dashboard-wrapper {
            min-height: 100vh;
            padding: 20px;
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Header */
        .dashboard-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border-radius: var(--radius-lg);
            padding: 24px 32px;
            margin-bottom: 24px;
            box-shadow: var(--shadow-md);
            color: white;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }

        .brand-section {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .brand-icon {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .brand-text h1 {
            font-size: 28px;
            font-weight: 700;
            margin: 0 0 4px 0;
        }

        .brand-text p {
            font-size: 16px;
            opacity: 0.9;
            margin: 0;
        }

        .datetime-widget {
            text-align: right;
        }

        .current-time, .current-date {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 8px;
            margin-bottom: 4px;
        }

        .current-time {
            font-size: 18px;
            font-weight: 600;
            font-family: 'SF Mono', Monaco, monospace;
        }

        /* Stats Cards */
        .stats-section {
            margin-bottom: 24px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
        }

        .stat-card {
            background: var(--bg-primary);
            border-radius: var(--radius-lg);
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border-color);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: var(--radius);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: white;
        }

        .stat-card.primary .stat-icon { background: var(--primary); }
        .stat-card.success .stat-icon { background: var(--success); }
        .stat-card.warning .stat-icon { background: var(--warning); }
        .stat-card.danger .stat-icon { background: var(--danger); }
        .stat-card.info .stat-icon { background: var(--info); }
        .stat-card.purple .stat-icon { background: var(--purple); }

        .stat-content h3 {
            font-size: 24px;
            font-weight: 700;
            margin: 0;
            color: var(--text-primary);
        }

        .stat-content p {
            font-size: 14px;
            color: var(--text-secondary);
            margin: 2px 0;
        }

        .stat-change {
            font-size: 12px;
            font-weight: 500;
        }

        .stat-change.positive { color: var(--success); }
        .stat-change.negative { color: var(--danger); }

        /* Dashboard Content */
        .dashboard-content {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        /* Main Chart */
        .main-chart-section {
            background: var(--bg-primary);
            border-radius: var(--radius-lg);
            padding: 24px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border-color);
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .chart-header h2 {
            font-size: 20px;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .chart-header h2 i {
            color: var(--primary);
        }

        .chart-controls {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            align-items: center;
        }

        .filter-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .filter-group label {
            font-size: 14px;
            font-weight: 500;
            color: var(--text-secondary);
        }

        .control-select {
            padding: 8px 12px;
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            font-size: 14px;
            background: var(--bg-primary);
            color: var(--text-primary);
            cursor: pointer;
            min-width: 120px;
        }

        .control-select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgb(14 165 233 / 0.1);
        }

        .btn-refresh {
            padding: 8px 16px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: var(--radius);
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background-color 0.2s ease;
        }

        .btn-refresh:hover {
            background: var(--primary-dark);
        }

        .chart-container {
            background: var(--bg-secondary);
            border-radius: var(--radius);
            padding: 16px;
            min-height: 400px;
        }

        /* Charts Grid */
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 20px;
        }

        .chart-card {
            background: var(--bg-primary);
            border-radius: var(--radius-lg);
            padding: 20px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border-color);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .chart-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .chart-card.wide {
            grid-column: span 2;
        }

        .chart-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .chart-card-header h3 {
            font-size: 16px;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .chart-card-header h3 i {
            color: var(--primary);
        }

        .chart-type-selector {
            padding: 6px 10px;
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            font-size: 12px;
            background: var(--bg-secondary);
            cursor: pointer;
        }

        .chart-card .chart-container {
            min-height: 250px;
        }

        /* Tables */
        .tables-section h2 {
            font-size: 20px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .tables-section h2 i {
            color: var(--primary);
        }

        .tables-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
        }

        .table-card {
            background: var(--bg-primary);
            border-radius: var(--radius-lg);
            padding: 20px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border-color);
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .table-header h3 {
            font-size: 16px;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .table-header h3 i {
            color: var(--primary);
        }

        .btn-export {
            padding: 6px 12px;
            background: var(--success);
            color: white;
            border: none;
            border-radius: var(--radius);
            cursor: pointer;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .table-container {
            overflow-x: auto;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th {
            background: var(--bg-secondary);
            padding: 12px 8px;
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-secondary);
            border-bottom: 1px solid var(--border-color);
        }

        .data-table td {
            padding: 12px 8px;
            font-size: 13px;
            border-bottom: 1px solid var(--bg-tertiary);
            color: var(--text-primary);
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: var(--radius);
            font-size: 11px;
            font-weight: 500;
            text-transform: uppercase;
        }

        .status-badge.success {
            background: rgb(16 185 129 / 0.1);
            color: var(--success);
        }

        .status-badge.warning {
            background: rgb(245 158 11 / 0.1);
            color: var(--warning);
        }

        .status-badge.danger {
            background: rgb(239 68 68 / 0.1);
            color: var(--danger);
        }

        /* Loading State */
        .loading-placeholder {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 200px;
            color: var(--text-muted);
            flex-direction: column;
            gap: 12px;
        }

        .loading-spinner {
            width: 32px;
            height: 32px;
            border: 3px solid var(--border-color);
            border-top: 3px solid var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .dashboard-wrapper {
                padding: 12px;
            }
            
            .header-content {
                flex-direction: column;
                text-align: center;
            }
            
            .datetime-widget {
                text-align: center;
            }
            
            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            }
            
            .chart-controls {
                justify-content: center;
            }
            
            .charts-grid {
                grid-template-columns: 1fr;
            }
            
            .chart-card.wide {
                grid-column: span 1;
            }
            
            .tables-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="dashboard-wrapper">
        <!-- Dashboard Header -->
        <div class="dashboard-header">
            <div class="header-content">
                <div class="header-left">
                    <div class="brand-section">
                        <div class="brand-icon">
                            <i class="fas fa-dove"></i>
                        </div>
                        <div class="brand-text">
                            <h1>VigaZa Farm Dashboard</h1>
                            <p>Sistem Manajemen Peternakan Burung Puyuh</p>
                        </div>
                    </div>
                </div>
                <div class="header-right">
                    <div class="datetime-widget">
                        <div class="current-time">
                            <i class="fas fa-clock"></i>
                            <span id="currentTime"><?= date('H:i:s'); ?> WIB</span>
                        </div>
                        <div class="current-date">
                            <i class="fas fa-calendar"></i>
                            <span id="currentDate"><?= date('l, d F Y'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats Cards -->
        <div class="stats-section">
            <div class="stats-grid">
                <div class="stat-card primary">
                    <div class="stat-icon">
                        <i class="fas fa-home"></i>
                    </div>
                    <div class="stat-content">
                        <h3>8</h3>
                        <p>Total Kandang</p>
                        <small class="stat-change positive">+2 bulan ini</small>
                    </div>
                </div>
                
                <div class="stat-card success">
                    <div class="stat-icon">
                        <i class="fas fa-dove"></i>
                    </div>
                    <div class="stat-content">
                        <h3>12,450</h3>
                        <p>Burung Puyuh</p>
                        <small class="stat-change positive">+5.2%</small>
                    </div>
                </div>
                
                <div class="stat-card warning">
                    <div class="stat-icon">
                        <i class="fas fa-egg"></i>
                    </div>
                    <div class="stat-content">
                        <h3>2,550</h3>
                        <p>Telur/Hari</p>
                        <small class="stat-change positive">+8.1%</small>
                    </div>
                </div>
                
                <div class="stat-card info">
                    <div class="stat-icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Rp 45.2M</h3>
                        <p>Pendapatan Bulan Ini</p>
                        <small class="stat-change positive">+12.5%</small>
                    </div>
                </div>
                
                <div class="stat-card purple">
                    <div class="stat-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stat-content">
                        <h3>88.5%</h3>
                        <p>Produktivitas</p>
                        <small class="stat-change positive">+3.2%</small>
                    </div>
                </div>
                
                <div class="stat-card danger">
                    <div class="stat-icon">
                        <i class="fas fa-heart-broken"></i>
                    </div>
                    <div class="stat-content">
                        <h3>1.2%</h3>
                        <p>Tingkat Mortalitas</p>
                        <small class="stat-change negative">-0.8%</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Dashboard Content -->
        <div class="dashboard-content">
            <!-- Main Chart Section -->
            <div class="main-chart-section">
                <div class="chart-header">
                    <h2><i class="fas fa-chart-area"></i> Laporan Komprehensif Farm</h2>
                    <div class="chart-controls">
                        <div class="filter-group">
                            <label for="periodFilter">Periode:</label>
                            <select id="periodFilter" class="control-select">
                                <option value="7days">7 Hari Terakhir</option>
                                <option value="30days" selected>30 Hari Terakhir</option>
                                <option value="3months">3 Bulan Terakhir</option>
                                <option value="6months">6 Bulan Terakhir</option>
                                <option value="1year">1 Tahun Terakhir</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label for="chartType">Tipe Chart:</label>
                            <select id="chartType" class="control-select">
                                <option value="line" selected>Line Chart</option>
                                <option value="bar">Bar Chart</option>
                                <option value="area">Area Chart</option>
                                <option value="mixed">Mixed Chart</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label for="dataType">Data:</label>
                            <select id="dataType" class="control-select">
                                <option value="all" selected>Semua Data</option>
                                <option value="production">Produksi Telur</option>
                                <option value="population">Populasi Puyuh</option>
                                <option value="financial">Keuangan</option>
                                <option value="health">Kesehatan</option>
                            </select>
                        </div>
                        <button id="refreshChart" class="btn-refresh">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                </div>
                <div class="chart-container">
                    <div id="mainChart">
                        <div class="loading-placeholder">
                            <div class="loading-spinner"></div>
                            <p>Loading chart data...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Secondary Charts Grid -->
            <div class="charts-grid">
                <!-- Production Chart -->
                <div class="chart-card">
                    <div class="chart-card-header">
                        <h3><i class="fas fa-egg"></i> Produksi Telur</h3>
                        <div class="chart-options">
                            <select class="chart-type-selector" data-chart="production">
                                <option value="line" selected>Line</option>
                                <option value="bar">Bar</option>
                                <option value="area">Area</option>
                            </select>
                        </div>
                    </div>
                    <div class="chart-container">
                        <div id="productionChart">
                            <div class="loading-placeholder">
                                <div class="loading-spinner"></div>
                                <p>Loading...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Population Chart -->
                <div class="chart-card">
                    <div class="chart-card-header">
                        <h3><i class="fas fa-dove"></i> Populasi Puyuh</h3>
                        <div class="chart-options">
                            <select class="chart-type-selector" data-chart="population">
                                <option value="bar" selected>Bar</option>
                                <option value="line">Line</option>
                                <option value="pie">Pie</option>
                            </select>
                        </div>
                    </div>
                    <div class="chart-container">
                        <div id="populationChart">
                            <div class="loading-placeholder">
                                <div class="loading-spinner"></div>
                                <p>Loading...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Financial Chart -->
                <div class="chart-card">
                    <div class="chart-card-header">
                        <h3><i class="fas fa-money-bill-wave"></i> Keuangan</h3>
                        <div class="chart-options">
                            <select class="chart-type-selector" data-chart="financial">
                                <option value="area" selected>Area</option>
                                <option value="bar">Bar</option>
                                <option value="line">Line</option>
                            </select>
                        </div>
                    </div>
                    <div class="chart-container">
                        <div id="financialChart">
                            <div class="loading-placeholder">
                                <div class="loading-spinner"></div>
                                <p>Loading...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Health Monitoring Chart -->
                <div class="chart-card">
                    <div class="chart-card-header">
                        <h3><i class="fas fa-heartbeat"></i> Monitoring Kesehatan</h3>
                        <div class="chart-options">
                            <select class="chart-type-selector" data-chart="health">
                                <option value="line" selected>Line</option>
                                <option value="area">Area</option>
                                <option value="bar">Bar</option>
                            </select>
                        </div>
                    </div>
                    <div class="chart-container">
                        <div id="healthChart">
                            <div class="loading-placeholder">
                                <div class="loading-spinner"></div>
                                <p>Loading...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Performance Metrics -->
                <div class="chart-card wide">
                    <div class="chart-card-header">
                        <h3><i class="fas fa-tachometer-alt"></i> Metrik Performa Farm</h3>
                        <div class="chart-options">
                            <select class="chart-type-selector" data-chart="performance">
                                <option value="mixed" selected>Mixed</option>
                                <option value="line">Line</option>
                                <option value="bar">Bar</option>
                            </select>
                        </div>
                    </div>
                    <div class="chart-container">
                        <div id="performanceChart">
                            <div class="loading-placeholder">
                                <div class="loading-spinner"></div>
                                <p>Loading...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Tables Section -->
            <div class="tables-section">
                <h2><i class="fas fa-table"></i> Data Terbaru</h2>
                
                <div class="tables-grid">
                    <!-- Recent Production Data -->
                    <div class="table-card">
                        <div class="table-header">
                            <h3><i class="fas fa-egg"></i> Produksi Terbaru</h3>
                            <div class="table-actions">
                                <button class="btn-export" data-table="production">
                                    <i class="fas fa-download"></i> Export
                                </button>
                            </div>
                        </div>
                        <div class="table-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Kandang</th>
                                        <th>Telur</th>
                                        <th>Kualitas A</th>
                                        <th>Kualitas B</th>
                                        <th>Rusak</th>
                                    </tr>
                                </thead>
                                <tbody id="productionTableBody">
                                    <tr>
                                        <td>2024-01-15</td>
                                        <td>PR-001</td>
                                        <td>95</td>
                                        <td>85</td>
                                        <td>10</td>
                                        <td>0</td>
                                    </tr>
                                    <tr>
                                        <td>2024-01-14</td>
                                        <td>PR-002</td>
                                        <td>88</td>
                                        <td>78</td>
                                        <td>8</td>
                                        <td>2</td>
                                    </tr>
                                    <tr>
                                        <td>2024-01-13</td>
                                        <td>PR-003</td>
                                        <td>92</td>
                                        <td>82</td>
                                        <td>9</td>
                                        <td>1</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Recent Breeding Data -->
                    <div class="table-card">
                        <div class="table-header">
                            <h3><i class="fas fa-heart"></i> Pembiakan Terbaru</h3>
                            <div class="table-actions">
                                <button class="btn-export" data-table="breeding">
                                    <i class="fas fa-download"></i> Export
                                </button>
                            </div>
                        </div>
                        <div class="table-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Batch</th>
                                        <th>Telur Set</th>
                                        <th>Menetas</th>
                                        <th>Gagal</th>
                                        <th>Success Rate</th>
                                    </tr>
                                </thead>
                                <tbody id="breedingTableBody">
                                    <tr>
                                        <td>2024-01-15</td>
                                        <td>PT-001</td>
                                        <td>100</td>
                                        <td>92</td>
                                        <td>8</td>
                                        <td><span class="status-badge success">92%</span></td>
                                    </tr>
                                    <tr>
                                        <td>2024-01-14</td>
                                        <td>PT-002</td>
                                        <td>120</td>
                                        <td>110</td>
                                        <td>10</td>
                                        <td><span class="status-badge success">92%</span></td>
                                    </tr>
                                    <tr>
                                        <td>2024-01-13</td>
                                        <td>PT-003</td>
                                        <td>90</td>
                                        <td>85</td>
                                        <td>5</td>
                                        <td><span class="status-badge success">94%</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Recent Health Data -->
                    <div class="table-card">
                        <div class="table-header">
                            <h3><i class="fas fa-heartbeat"></i> Kesehatan Terbaru</h3>
                            <div class="table-actions">
                                <button class="btn-export" data-table="health">
                                    <i class="fas fa-download"></i> Export
                                </button>
                            </div>
                        </div>
                        <div class="table-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Kandang</th>
                                        <th>Populasi</th>
                                        <th>Sehat</th>
                                        <th>Sakit</th>
                                        <th>Mati</th>
                                    </tr>
                                </thead>
                                <tbody id="healthTableBody">
                                    <tr>
                                        <td>2024-01-15</td>
                                        <td>KD-001</td>
                                        <td>1,200</td>
                                        <td>1,195</td>
                                        <td>3</td>
                                        <td>2</td>
                                    </tr>
                                    <tr>
                                        <td>2024-01-14</td>
                                        <td>KD-002</td>
                                        <td>1,150</td>
                                        <td>1,145</td>
                                        <td>4</td>
                                        <td>1</td>
                                    </tr>
                                    <tr>
                                        <td>2024-01-13</td>
                                        <td>KD-003</td>
                                        <td>1,300</td>
                                        <td>1,290</td>
                                        <td>8</td>
                                        <td>2</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Dependencies -->
    <script src="<?= base_url('assets/plugins/jquery/jquery.min.js'); ?>"></script>
    <script src="<?= base_url('assets/bundles/apexcharts.bundle.js'); ?>"></script>
    
    <!-- Dashboard JavaScript -->
    <script>
        // Dashboard Core Class
        class VigaFarmDashboard {
            constructor() {
                this.charts = {};
                this.init();
            }

            init() {
                this.updateClock();
                this.loadCharts();
                this.setupEventListeners();
                
                // Update clock every second
                setInterval(() => this.updateClock(), 1000);
            }

            updateClock() {
                const now = new Date();
                const timeString = now.toLocaleTimeString('id-ID', {
                    hour12: false,
                    timeZone: 'Asia/Jakarta'
                }) + ' WIB';
                const dateString = now.toLocaleDateString('id-ID', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
                
                document.getElementById('currentTime').textContent = timeString;
                document.getElementById('currentDate').textContent = dateString;
            }

            loadCharts() {
                // Check if ApexCharts is available
                if (typeof ApexCharts === 'undefined') {
                    console.log('ApexCharts not loaded, loading from CDN...');
                    this.loadApexChartsFromCDN(() => {
                        this.initializeCharts();
                    });
                } else {
                    this.initializeCharts();
                }
            }

            loadApexChartsFromCDN(callback) {
                const script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/apexcharts@3.44.0/dist/apexcharts.min.js';
                script.onload = callback;
                document.head.appendChild(script);
            }

            initializeCharts() {
                setTimeout(() => {
                    this.createMainChart();
                    this.createProductionChart();
                    this.createPopulationChart();
                    this.createFinancialChart();
                    this.createHealthChart();
                    this.createPerformanceChart();
                }, 500);
            }

            createMainChart() {
                const container = document.getElementById('mainChart');
                if (!container) return;

                container.innerHTML = '';

                const options = {
                    series: [{
                        name: 'Produksi Telur',
                        data: [820, 850, 880, 845, 890, 875, 920, 940, 960, 985]
                    }, {
                        name: 'Populasi Puyuh',
                        data: [12200, 12300, 12250, 12400, 12350, 12450, 12500, 12480, 12520, 12550]
                    }, {
                        name: 'Pendapatan (Juta)',
                        data: [42, 45, 47, 44, 48, 46, 50, 52, 54, 56]
                    }],
                    chart: {
                        type: 'line',
                        height: 380,
                        toolbar: {
                            show: true
                        }
                    },
                    colors: ['#0ea5e9', '#10b981', '#f59e0b'],
                    stroke: {
                        curve: 'smooth',
                        width: 3
                    },
                    xaxis: {
                        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt']
                    },
                    yaxis: [{
                        title: {
                            text: 'Produksi Telur'
                        }
                    }, {
                        opposite: true,
                        title: {
                            text: 'Populasi & Pendapatan'
                        }
                    }],
                    legend: {
                        position: 'top'
                    },
                    grid: {
                        borderColor: '#e2e8f0'
                    }
                };

                this.charts.main = new ApexCharts(container, options);
                this.charts.main.render();
            }

            createProductionChart() {
                const container = document.getElementById('productionChart');
                if (!container) return;

                container.innerHTML = '';

                const options = {
                    series: [{
                        name: 'Telur Diproduksi',
                        data: [95, 88, 92, 90, 87, 94, 96]
                    }],
                    chart: {
                        type: 'line',
                        height: 250,
                        toolbar: { show: false }
                    },
                    colors: ['#0ea5e9'],
                    stroke: {
                        curve: 'smooth',
                        width: 3
                    },
                    xaxis: {
                        categories: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min']
                    }
                };

                this.charts.production = new ApexCharts(container, options);
                this.charts.production.render();
            }

            createPopulationChart() {
                const container = document.getElementById('populationChart');
                if (!container) return;

                container.innerHTML = '';

                const options = {
                    series: [{
                        name: 'Populasi',
                        data: [1200, 1150, 1300, 1250, 1180, 1220, 1350, 1400]
                    }],
                    chart: {
                        type: 'bar',
                        height: 250,
                        toolbar: { show: false }
                    },
                    colors: ['#10b981'],
                    xaxis: {
                        categories: ['KD-001', 'KD-002', 'KD-003', 'KD-004', 'KD-005', 'KD-006', 'KD-007', 'KD-008']
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: 4
                        }
                    }
                };

                this.charts.population = new ApexCharts(container, options);
                this.charts.population.render();
            }

            createFinancialChart() {
                const container = document.getElementById('financialChart');
                if (!container) return;

                container.innerHTML = '';

                const options = {
                    series: [{
                        name: 'Pendapatan',
                        data: [45, 48, 52, 47, 55, 50, 58]
                    }, {
                        name: 'Pengeluaran',
                        data: [25, 28, 30, 27, 32, 29, 34]
                    }],
                    chart: {
                        type: 'area',
                        height: 250,
                        toolbar: { show: false }
                    },
                    colors: ['#10b981', '#ef4444'],
                    fill: {
                        type: 'gradient',
                        gradient: {
                            opacityFrom: 0.6,
                            opacityTo: 0.1
                        }
                    },
                    xaxis: {
                        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul']
                    }
                };

                this.charts.financial = new ApexCharts(container, options);
                this.charts.financial.render();
            }

            createHealthChart() {
                const container = document.getElementById('healthChart');
                if (!container) return;

                container.innerHTML = '';

                const options = {
                    series: [{
                        name: 'Sehat',
                        data: [98.5, 98.2, 98.8, 98.4, 98.9, 98.6, 98.7]
                    }, {
                        name: 'Sakit',
                        data: [1.2, 1.5, 1.0, 1.3, 0.8, 1.1, 1.0]
                    }, {
                        name: 'Mati',
                        data: [0.3, 0.3, 0.2, 0.3, 0.3, 0.3, 0.3]
                    }],
                    chart: {
                        type: 'line',
                        height: 250,
                        toolbar: { show: false }
                    },
                    colors: ['#10b981', '#f59e0b', '#ef4444'],
                    stroke: {
                        curve: 'smooth',
                        width: 2
                    },
                    xaxis: {
                        categories: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min']
                    }
                };

                this.charts.health = new ApexCharts(container, options);
                this.charts.health.render();
            }

            createPerformanceChart() {
                const container = document.getElementById('performanceChart');
                if (!container) return;

                container.innerHTML = '';

                const options = {
                    series: [{
                        name: 'Efisiensi Pakan',
                        type: 'column',
                        data: [85, 88, 90, 87, 92, 89, 94]
                    }, {
                        name: 'Produktivitas',
                        type: 'line',
                        data: [88, 85, 91, 89, 94, 90, 96]
                    }],
                    chart: {
                        type: 'line',
                        height: 250,
                        toolbar: { show: false }
                    },
                    colors: ['#8b5cf6', '#06b6d4'],
                    stroke: {
                        width: [0, 3]
                    },
                    xaxis: {
                        categories: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min']
                    }
                };

                this.charts.performance = new ApexCharts(container, options);
                this.charts.performance.render();
            }

            setupEventListeners() {
                // Chart type selectors
                document.querySelectorAll('.chart-type-selector').forEach(selector => {
                    selector.addEventListener('change', (e) => {
                        const chartName = e.target.dataset.chart;
                        const newType = e.target.value;
                        this.changeChartType(chartName, newType);
                    });
                });

                // Refresh button
                document.getElementById('refreshChart')?.addEventListener('click', () => {
                    this.refreshAllCharts();
                });

                // Export buttons
                document.querySelectorAll('.btn-export').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        const tableType = e.target.closest('.btn-export').dataset.table;
                        this.exportTable(tableType);
                    });
                });
            }

            changeChartType(chartName, newType) {
                if (this.charts[chartName]) {
                    this.charts[chartName].updateOptions({
                        chart: {
                            type: newType
                        }
                    });
                }
            }

            refreshAllCharts() {
                Object.values(this.charts).forEach(chart => {
                    if (chart && chart.updateSeries) {
                        chart.updateSeries(chart.w.config.series);
                    }
                });
            }

            exportTable(tableType) {
                alert(`Export ${tableType} table - Feature akan segera tersedia`);
            }
        }

        // Initialize Dashboard
        document.addEventListener('DOMContentLoaded', function() {
            window.vigaFarmDashboard = new VigaFarmDashboard();
            console.log('VigaZaFarm Dashboard v3.0 Loaded Successfully');
        });
    </script>
</body>
</html>
