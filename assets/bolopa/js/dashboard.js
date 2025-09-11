/**
 * VigaZaFarm Dashboard JavaScript - Poultry Farm Management System
 * Enhanced Interactive Charts and Dashboard Functionality
 * Author: VigaZaFarm Team
 * Version: 3.0
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize Real-time Clock
    function initRealTimeClock() {
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            });
            
            const dateString = now.toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            
            const timeElement = document.getElementById('currentTime');
            const dateElement = document.getElementById('currentDate');
            
            if (timeElement) {
                timeElement.textContent = timeString;
            }
            
            if (dateElement) {
                dateElement.textContent = dateString;
            }
        }
        
        updateTime();
        setInterval(updateTime, 1000);
        console.log('Real-time clock initialized');
    }

    // Initialize clock first
    initRealTimeClock();

    // Enhanced color palette for poultry farm management
    const colors = {
        primary: '#0eabb4',
        secondary: '#2563eb',
        success: '#10b981',
        warning: '#f59e0b',
        danger: '#ef4444',
        purple: '#8b5cf6',
        teal: '#0891b2',
        indigo: '#6366f1',
        pink: '#ec4899',
        gray: '#6b7280'
    };

    // Poultry farm data structure
    const farmData = {
        // KPI Data
        kpis: {
            totalKandang: 8,
            totalAyam: 12450,
            produksiHarian: 2840,
            batchAktif: 15,
            tingkatPenetasan: 87.5,
            tingkatMortalitas: 1.8
        },

        // Production Pipeline Data
        pipeline: {
            penetasan: {
                persiapan: 15,
                proses: 25,
                selesai: 45,
                gagal: 15
            },
            pembesaran: {
                persiapan: 10,
                aktif: 50,
                selesai: 35,
                gagal: 5
            },
            produksi: {
                persiapan: 5,
                aktif: 60,
                selesai: 30,
                gagal: 5
            }
        },

        // Monthly trends (12 months)
        trends: {
            produksiTelur: [75000, 78000, 82000, 85000, 88000, 92000, 89000, 91000, 94000, 96000, 98000, 101000],
            mortalitas: [2.1, 1.9, 1.7, 1.5, 1.8, 2.0, 1.6, 1.4, 1.8, 1.5, 1.7, 1.8],
            fcr: [1.8, 1.7, 1.6, 1.7, 1.8, 1.9, 1.7, 1.6, 1.8, 1.7, 1.6, 1.7],
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des']
        },

        // Population by phase
        populasi: {
            doc: [500, 450, 600, 520, 580, 650, 600, 550, 620, 580, 600, 650],
            muda: [3000, 3200, 3100, 3300, 3250, 3400, 3350, 3200, 3450, 3300, 3250, 3400],
            dewasa: [8500, 8800, 8650, 8900, 8750, 9000, 8850, 8700, 8950, 8800, 8650, 8900]
        },

        // Capacity data
        capacity: {
            kandang: ['A-1', 'A-2', 'A-3', 'B-1', 'B-2', 'B-3', 'C-1', 'C-2'],
            kapasitas: [1500, 1500, 1200, 1800, 1800, 1200, 2000, 2000],
            terisi: [1450, 1480, 1150, 1750, 1720, 1180, 1950, 1890]
        },

        // Equipment status
        equipment: {
            aktif: 85,
            maintenance: 10,
            rusak: 5
        },

        // Financial data
        financial: {
            biayaPakan: [45000000, 47000000, 48000000, 50000000, 52000000, 51000000, 53000000, 54000000, 55000000, 56000000, 57000000, 58000000],
            biayaObat: [8000000, 8500000, 9000000, 8200000, 8800000, 9200000, 8600000, 8900000, 9100000, 8700000, 9300000, 9500000],
            biayaLainnya: [12000000, 11500000, 13000000, 12500000, 13500000, 12800000, 13200000, 13800000, 14000000, 13600000, 14200000, 14500000],
            revenue: [85000000, 88000000, 92000000, 89000000, 95000000, 98000000, 96000000, 99000000, 102000000, 100000000, 105000000, 108000000]
        }
    };

    let charts = {};

    // 1. Pipeline Charts - Donut Charts for Production Stages
    function initPipelineCharts() {
        // Penetasan Chart
        const penetasanElement = document.getElementById('penetasanChart');
        if (penetasanElement) {
            const penetasanOptions = {
                series: [
                    farmData.pipeline.penetasan.persiapan,
                    farmData.pipeline.penetasan.proses,
                    farmData.pipeline.penetasan.selesai,
                    farmData.pipeline.penetasan.gagal
                ],
                chart: {
                    type: 'donut',
                    height: 200
                },
                labels: ['Persiapan', 'Proses', 'Selesai', 'Gagal'],
                colors: [colors.warning, colors.primary, colors.success, colors.danger],
                legend: {
                    position: 'bottom',
                    fontSize: '12px'
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '65%'
                        }
                    }
                },
                dataLabels: {
                    enabled: true,
                    formatter: function (val) {
                        return Math.round(val) + "%";
                    }
                }
            };
            charts.penetasan = new ApexCharts(penetasanElement, penetasanOptions);
            charts.penetasan.render();
        }

        // Pembesaran Chart
        const pembesaranElement = document.getElementById('pembesaranChart');
        if (pembesaranElement) {
            const pembesaranOptions = {
                series: [
                    farmData.pipeline.pembesaran.persiapan,
                    farmData.pipeline.pembesaran.aktif,
                    farmData.pipeline.pembesaran.selesai,
                    farmData.pipeline.pembesaran.gagal
                ],
                chart: {
                    type: 'donut',
                    height: 200
                },
                labels: ['Persiapan', 'Aktif', 'Selesai', 'Gagal'],
                colors: [colors.warning, colors.primary, colors.success, colors.danger],
                legend: {
                    position: 'bottom',
                    fontSize: '12px'
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '65%'
                        }
                    }
                }
            };
            charts.pembesaran = new ApexCharts(pembesaranElement, pembesaranOptions);
            charts.pembesaran.render();
        }

        // Produksi Chart
        const produksiElement = document.getElementById('produksiChart');
        if (produksiElement) {
            const produksiOptions = {
                series: [
                    farmData.pipeline.produksi.persiapan,
                    farmData.pipeline.produksi.aktif,
                    farmData.pipeline.produksi.selesai,
                    farmData.pipeline.produksi.gagal
                ],
                chart: {
                    type: 'donut',
                    height: 200
                },
                labels: ['Persiapan', 'Aktif', 'Selesai', 'Gagal'],
                colors: [colors.warning, colors.primary, colors.success, colors.danger],
                legend: {
                    position: 'bottom',
                    fontSize: '12px'
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '65%'
                        }
                    }
                }
            };
            charts.produksi = new ApexCharts(produksiElement, produksiOptions);
            charts.produksi.render();
        }
    }

    // 2. Monitoring Trends Charts
    function initMonitoringCharts() {
        // Produksi Telur Trend
        const produksiTelurElement = document.getElementById('produksiTelurChart');
        if (produksiTelurElement) {
            const produksiTelurOptions = {
                series: [{
                    name: 'Produksi Telur',
                    data: farmData.trends.produksiTelur
                }],
                chart: {
                    type: 'line',
                    height: 350,
                    toolbar: {
                        show: true
                    }
                },
                colors: [colors.success],
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                xaxis: {
                    categories: farmData.trends.categories
                },
                yaxis: {
                    title: {
                        text: 'Jumlah Telur'
                    },
                    labels: {
                        formatter: function (val) {
                            return val.toLocaleString();
                        }
                    }
                },
                grid: {
                    borderColor: '#e7e7e7',
                    row: {
                        colors: ['#f3f3f3', 'transparent'],
                        opacity: 0.5
                    }
                },
                markers: {
                    size: 4
                }
            };
            charts.produksiTelur = new ApexCharts(produksiTelurElement, produksiTelurOptions);
            charts.produksiTelur.render();
        }

        // Mortalitas Chart
        const mortalitasElement = document.getElementById('mortalitasChart');
        if (mortalitasElement) {
            const mortalitasOptions = {
                series: [{
                    name: 'Tingkat Mortalitas (%)',
                    data: farmData.trends.mortalitas
                }],
                chart: {
                    type: 'line',
                    height: 280
                },
                colors: [colors.danger],
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                xaxis: {
                    categories: farmData.trends.categories
                },
                yaxis: {
                    title: {
                        text: 'Persentase (%)'
                    },
                    max: 3
                }
            };
            charts.mortalitas = new ApexCharts(mortalitasElement, mortalitasOptions);
            charts.mortalitas.render();
        }

        // FCR Chart
        const fcrElement = document.getElementById('fcrChart');
        if (fcrElement) {
            const fcrOptions = {
                series: [{
                    name: 'Feed Conversion Ratio',
                    data: farmData.trends.fcr
                }],
                chart: {
                    type: 'column',
                    height: 280
                },
                colors: [colors.warning],
                xaxis: {
                    categories: farmData.trends.categories
                },
                yaxis: {
                    title: {
                        text: 'FCR'
                    }
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4
                    }
                }
            };
            charts.fcr = new ApexCharts(fcrElement, fcrOptions);
            charts.fcr.render();
        }

        // Populasi by Phase
        const populasiElement = document.getElementById('populasiChart');
        if (populasiElement) {
            const populasiOptions = {
                series: [
                    {
                        name: 'DOC',
                        data: farmData.populasi.doc
                    },
                    {
                        name: 'Muda',
                        data: farmData.populasi.muda
                    },
                    {
                        name: 'Dewasa',
                        data: farmData.populasi.dewasa
                    }
                ],
                chart: {
                    type: 'area',
                    height: 350,
                    stacked: true
                },
                colors: [colors.warning, colors.primary, colors.success],
                stroke: {
                    curve: 'smooth'
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        opacityFrom: 0.6,
                        opacityTo: 0.8
                    }
                },
                xaxis: {
                    categories: farmData.trends.categories
                },
                yaxis: {
                    title: {
                        text: 'Jumlah Ayam'
                    }
                }
            };
            charts.populasi = new ApexCharts(populasiElement, populasiOptions);
            charts.populasi.render();
        }
    }

    // Initialize pipeline chart with enhanced contrast
    function initPipelineChart(type = 'donut') {
        const options = {
            series: type === 'donut' || type === 'radial' ? [30, 45, 15, 10] : 
                   [{name: 'Jumlah', data: [30, 45, 15, 10]}],
            chart: {
                type: type === 'radial' ? 'radialBar' : (type === 'donut' ? 'donut' : 'bar'),
                height: 250,
                toolbar: { show: false },
                background: '#ffffff',
                foreColor: '#374151'
            },
            labels: ['Penetasan', 'Pembesaran', 'Produksi', 'Penjualan'],
            colors: [colors.primary, colors.secondary, colors.success, colors.warning],
            plotOptions: {
                radialBar: {
                    dataLabels: {
                        name: { 
                            fontSize: '14px',
                            fontWeight: 600,
                            color: '#374151'
                        },
                        value: { 
                            fontSize: '16px',
                            fontWeight: 700,
                            color: '#1f2937'
                        }
                    },
                    hollow: {
                        size: '60%'
                    }
                },
                bar: {
                    horizontal: false,
                    columnWidth: '60%',
                    dataLabels: {
                        position: 'top'
                    }
                },
                pie: {
                    donut: {
                        size: '70%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total',
                                color: '#374151',
                                fontSize: '14px',
                                fontWeight: 600
                            }
                        }
                    }
                }
            },
            xaxis: type === 'bar' ? {
                categories: ['Penetasan', 'Pembesaran', 'Produksi', 'Penjualan'],
                labels: {
                    style: {
                        colors: '#6b7280',
                        fontSize: '12px',
                        fontWeight: 600
                    }
                }
            } : undefined,
            yaxis: type === 'bar' ? {
                labels: {
                    style: {
                        colors: '#6b7280',
                        fontSize: '12px',
                        fontWeight: 600
                    }
                }
            } : undefined,
            legend: {
                position: 'bottom',
                fontSize: '12px',
                fontWeight: 500,
                labels: {
                    colors: '#374151'
                }
            },
            dataLabels: {
                enabled: true,
                style: {
                    fontSize: '12px',
                    fontWeight: 600,
                    colors: ['#ffffff']
                }
            },
            tooltip: {
                theme: 'light',
                style: {
                    fontSize: '12px',
                    fontFamily: 'Inter, sans-serif'
                }
            }
        };

        const chart = new ApexCharts(document.querySelector("#pipelineChart"), options);
        chart.render();
        return chart;
    }

    // Initialize trend chart with enhanced visuals
    function initTrendChart(type = 'line') {
        const options = {
            series: [{
                name: 'Penetasan',
                data: [20, 25, 30, 28, 35, 40, 38]
            }, {
                name: 'Pembesaran',
                data: [30, 35, 40, 45, 42, 48, 50]
            }],
            chart: {
                type: type,
                height: 250,
                toolbar: { show: false },
                background: '#ffffff',
                foreColor: '#374151'
            },
            colors: [colors.primary, colors.secondary],
            stroke: {
                curve: 'smooth',
                width: 3
            },
            fill: {
                type: type === 'area' ? 'gradient' : 'solid',
                opacity: type === 'area' ? 0.6 : 1,
                gradient: {
                    shadeIntensity: 1,
                    type: "vertical",
                    colorStops: [
                        { offset: 0, color: colors.primary, opacity: 0.8 },
                        { offset: 100, color: colors.primary, opacity: 0.1 }
                    ]
                }
            },
            xaxis: {
                categories: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                labels: {
                    style: {
                        colors: '#6b7280',
                        fontSize: '12px',
                        fontWeight: 600
                    }
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: '#6b7280',
                        fontSize: '12px',
                        fontWeight: 600
                    }
                }
            },
            legend: {
                position: 'top',
                fontSize: '12px',
                fontWeight: 500,
                labels: {
                    colors: '#374151'
                }
            },
            grid: {
                borderColor: '#e5e7eb',
                strokeDashArray: 3
            },
            tooltip: {
                theme: 'light',
                style: {
                    fontSize: '12px',
                    fontFamily: 'Inter, sans-serif'
                }
            },
            dataLabels: {
                enabled: false
            }
        };

        const chart = new ApexCharts(document.querySelector("#trendChart"), options);
        chart.render();
        return chart;
    }

    // Initialize capacity chart with enhanced visuals
    function initCapacityChart(type = 'bar') {
        if (type === 'heatmap') {
            const options = {
                series: [{
                    name: 'Zona A',
                    data: [[0, 0, 75], [0, 1, 85], [0, 2, 65], [0, 3, 95]]
                }, {
                    name: 'Zona B', 
                    data: [[1, 0, 80], [1, 1, 70], [1, 2, 90], [1, 3, 85]]
                }, {
                    name: 'Zona C',
                    data: [[2, 0, 88], [2, 1, 92], [2, 2, 78], [2, 3, 82]]
                }],
                chart: {
                    type: 'heatmap',
                    height: 250,
                    toolbar: { show: false },
                    background: '#ffffff'
                },
                colors: [colors.primary],
                xaxis: {
                    categories: ['Kandang 1', 'Kandang 2', 'Kandang 3', 'Kandang 4'],
                    labels: {
                        style: {
                            colors: '#6b7280',
                            fontSize: '10px',
                            fontWeight: 600
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: '#6b7280',
                            fontSize: '10px',
                            fontWeight: 600
                        }
                    }
                },
                plotOptions: {
                    heatmap: {
                        radius: 4,
                        enableShades: false,
                        colorScale: {
                            ranges: [
                                { from: 0, to: 50, color: colors.danger, name: 'Rendah' },
                                { from: 51, to: 75, color: colors.warning, name: 'Sedang' },
                                { from: 76, to: 90, color: colors.success, name: 'Tinggi' },
                                { from: 91, to: 100, color: colors.primary, name: 'Optimal' }
                            ]
                        }
                    }
                },
                dataLabels: {
                    enabled: true,
                    style: {
                        colors: ['#ffffff'],
                        fontSize: '12px',
                        fontWeight: 600
                    }
                },
                tooltip: {
                    theme: 'light'
                }
            };
            const chart = new ApexCharts(document.querySelector("#capacityChart"), options);
            chart.render();
            return chart;
        }

        if (type === 'radar') {
            const options = {
                series: [{
                    name: 'Kapasitas Terisi',
                    data: [280, 450, 320, 380, 290, 200]
                }, {
                    name: 'Kapasitas Target',
                    data: [400, 500, 350, 420, 350, 280]
                }],
                chart: {
                    type: 'radar',
                    height: 250,
                    toolbar: { show: false }
                },
                colors: [colors.primary, colors.secondary],
                xaxis: {
                    categories: ['Zona A', 'Zona B', 'Zona C', 'Zona D', 'Zona E', 'Zona F']
                },
                yaxis: {
                    show: true
                },
                legend: {
                    position: 'bottom',
                    fontSize: '12px'
                }
            };
            const chart = new ApexCharts(document.querySelector("#capacityChart"), options);
            chart.render();
            return chart;
        }

        const options = {
            series: [{
                name: 'Kapasitas Terisi',
                data: [280, 450, 320, 380, 290, 200, 150, 80]
            }, {
                name: 'Kapasitas Total',
                data: [500, 600, 400, 500, 400, 300, 300, 100]
            }],
            chart: {
                type: 'bar',
                height: 250,
                toolbar: { show: false },
                background: '#ffffff',
                foreColor: '#374151'
            },
            colors: [colors.primary, '#e5e7eb'],
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '60%',
                    dataLabels: {
                        position: 'top'
                    }
                }
            },
            xaxis: {
                categories: ['A1', 'B1', 'B2', 'C1', 'C2', 'P1', 'P2', 'H1'],
                labels: {
                    style: {
                        colors: '#6b7280',
                        fontSize: '12px',
                        fontWeight: 600
                    }
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: '#6b7280',
                        fontSize: '12px',
                        fontWeight: 600
                    }
                }
            },
            legend: {
                position: 'top',
                fontSize: '12px',
                fontWeight: 500,
                labels: {
                    colors: '#374151'
                }
            },
            grid: {
                borderColor: '#e5e7eb',
                strokeDashArray: 3
            },
            tooltip: {
                theme: 'light',
                y: {
                    formatter: val => val + ' ekor'
                }
            },
            dataLabels: {
                enabled: false
            }
        };

        const chart = new ApexCharts(document.querySelector("#capacityChart"), options);
        chart.render();
        return chart;
    }

    // Initialize performance chart with enhanced visuals
    function initPerformanceChart(type = 'radialBar') {
        const options = {
            series: type === 'mixed' ? 
                [{name: 'Efisiensi', type: 'column', data: [85, 90, 78, 92, 88]}, 
                 {name: 'Target', type: 'line', data: [80, 85, 80, 90, 85]}] :
                [85, 90, 78, 92],
            chart: {
                type: type === 'mixed' ? 'line' : type,
                height: 250,
                toolbar: { show: false },
                background: '#ffffff',
                foreColor: '#374151'
            },
            colors: [colors.primary, colors.secondary, colors.success, colors.warning],
            labels: type !== 'mixed' ? ['Produksi', 'Penetasan', 'Pembesaran', 'Kualitas'] : undefined,
            xaxis: type === 'mixed' ? {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei'],
                labels: {
                    style: {
                        colors: '#6b7280',
                        fontSize: '12px',
                        fontWeight: 600
                    }
                }
            } : undefined,
            plotOptions: {
                radialBar: {
                    dataLabels: {
                        name: { 
                            fontSize: '12px',
                            fontWeight: 600,
                            color: '#374151'
                        },
                        value: { 
                            fontSize: '14px',
                            fontWeight: 700,
                            formatter: val => val + '%',
                            color: '#1f2937'
                        }
                    },
                    hollow: {
                        size: '60%'
                    }
                }
            },
            legend: {
                position: 'bottom',
                fontSize: '12px',
                fontWeight: 500,
                labels: {
                    colors: '#374151'
                }
            },
            tooltip: {
                theme: 'light',
                style: {
                    fontSize: '12px',
                    fontFamily: 'Inter, sans-serif'
                }
            }
        };

        const chart = new ApexCharts(document.querySelector("#performanceChart"), options);
        chart.render();
        return chart;
    }

    // Initialize population chart
    function initPopulationChart(type = 'area') {
        if (type === 'treemap') {
            const options = {
                series: [{
                    data: [
                        { x: 'Kandang A', y: 280 },
                        { x: 'Kandang B', y: 450 },
                        { x: 'Kandang C', y: 320 },
                        { x: 'Kandang D', y: 380 }
                    ]
                }],
                chart: {
                    type: 'treemap',
                    height: 250,
                    toolbar: { show: false }
                },
                colors: [colors.primary],
                plotOptions: {
                    treemap: {
                        enableShades: true,
                        shadeIntensity: 0.5
                    }
                }
            };
            const chart = new ApexCharts(document.querySelector("#populationChart"), options);
            chart.render();
            return chart;
        }

        const options = {
            series: [{
                name: 'Populasi Dewasa',
                data: [280, 450, 320, 380, 290, 200, 150]
            }, {
                name: 'Populasi Muda',
                data: [120, 180, 140, 160, 130, 90, 70]
            }],
            chart: {
                type: type,
                height: 250,
                toolbar: { show: false },
                stacked: type === 'area'
            },
            colors: [colors.primary, colors.secondary],
            fill: {
                type: type === 'area' ? 'gradient' : 'solid',
                opacity: type === 'area' ? 0.7 : 1
            },
            xaxis: {
                categories: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min']
            },
            legend: {
                position: 'top',
                fontSize: '12px'
            }
        };

        const chart = new ApexCharts(document.querySelector("#populationChart"), options);
        chart.render();
        return chart;
    }

    // Initialize financial chart
    function initFinancialChart(type = 'column') {
        const options = {
            series: [{
                name: 'Revenue',
                data: [4400, 5500, 5700, 5600, 6100, 5800, 6300]
            }, {
                name: 'Expenses',
                data: [2400, 2900, 3100, 2800, 3200, 3000, 3400]
            }],
            chart: {
                type: type === 'waterfall' ? 'bar' : type,
                height: 250,
                toolbar: { show: false }
            },
            colors: [colors.success, colors.danger],
            xaxis: {
                categories: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min']
            },
            legend: {
                position: 'top',
                fontSize: '12px'
            },
            plotOptions: {
                bar: {
                    columnWidth: '60%'
                }
            }
        };

        const chart = new ApexCharts(document.querySelector("#financialChart"), options);
        chart.render();
        return chart;
    }

    // Period filter handlers with enhanced UX
    document.querySelectorAll('.period-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            // Show loading
            showChartLoading('mainChart');
            
            // Update active state
            document.querySelectorAll('.period-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Update chart with animation
            setTimeout(() => {
                currentPeriod = this.dataset.period;
                initMainChart(currentPeriod);
                hideChartLoading('mainChart');
                showNotification(`Data diperbarui ke periode ${this.textContent}`, 'success');
            }, 500);
        });
    });

    // Main chart type handlers with notifications
    document.querySelectorAll('.chart-type-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            // Update active state
            document.querySelectorAll('.chart-type-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Show notification
            showNotification(`Tampilan diubah ke ${this.textContent}`, 'info');
            initMainChart(currentPeriod, this.dataset.chart);
        });
    });

    // Chart option handlers for interactive charts
    document.querySelectorAll('.chart-option-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const target = this.dataset.target;
            const chartType = this.dataset.type;
            
            // Show loading
            showChartLoading(target);
            
            // Update active state
            document.querySelectorAll(`[data-target="${target}"]`).forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Update chart with delay for smooth transition
            setTimeout(() => {
                const chartName = target.replace('Chart', '');
                if (charts[chartName]) {
                    charts[chartName].destroy();
                }
                
                switch(target) {
                    case 'pipelineChart':
                        charts.pipeline = initPipelineChart(chartType);
                        break;
                    case 'trendChart':
                        charts.trend = initTrendChart(chartType);
                        break;
                    case 'capacityChart':
                        charts.capacity = initCapacityChart(chartType);
                        break;
                    case 'performanceChart':
                        charts.performance = initPerformanceChart(chartType);
                        break;
                    case 'populationChart':
                        charts.population = initPopulationChart(chartType);
                        break;
                    case 'financialChart':
                        charts.financial = initFinancialChart(chartType);
                        break;
                }
                
                hideChartLoading(target);
                showNotification(`Chart diperbarui ke tampilan ${chartType}`, 'success');
            }, 300);
        });
    });

    // Auto refresh every 30 seconds with visual indicator
    setInterval(() => {
        console.log('Dashboard refreshed:', new Date().toLocaleTimeString());
        // Add pulse effect to show real-time updates
        document.querySelectorAll('.kpi-value').forEach(el => {
            el.classList.add('pulse');
            setTimeout(() => el.classList.remove('pulse'), 2000);
        });
    }, 30000);

    // Helper functions for loading states
    function showChartLoading(chartId) {
        const container = document.querySelector(`#${chartId}`).parentElement;
        if (!container.querySelector('.loading-overlay')) {
            const overlay = document.createElement('div');
            overlay.className = 'loading-overlay';
            overlay.innerHTML = '<div class="loading-spinner"></div>';
            container.appendChild(overlay);
        }
    }

    function hideChartLoading(chartId) {
        const container = document.querySelector(`#${chartId}`).parentElement;
        const overlay = container.querySelector('.loading-overlay');
        if (overlay) {
            overlay.remove();
        }
    }

    // Notification system
    function showNotification(message, type = 'info') {
        // Remove existing notifications
        document.querySelectorAll('.notification').forEach(n => n.remove());
        
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${type === 'success' ? colors.success : type === 'error' ? colors.danger : colors.primary};
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 1000;
            font-size: 14px;
            font-weight: 500;
            transform: translateX(100%);
            transition: transform 0.3s ease;
        `;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 100);
        
        // Auto remove
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    // Animate metric values
    setTimeout(() => {
        document.querySelectorAll('.kpi-value').forEach(el => {
            const finalValue = parseInt(el.textContent.replace(/,/g, ''));
            let currentValue = 0;
            const increment = finalValue / 50;
            const timer = setInterval(() => {
                currentValue += increment;
                if (currentValue >= finalValue) {
                    currentValue = finalValue;
                    clearInterval(timer);
                }
                el.textContent = Math.floor(currentValue).toLocaleString();
            }, 30);
        });
    }, 500);

    // Initialize individual charts with filter support
    function initIndividualCharts(period = 'daily') {
        const data = farmData[period];
        console.log('Initializing individual charts for period:', period, 'with data:', data);
        
        // Production Chart
        const productionElement = document.getElementById('productionChart');
        if (productionElement) {
            console.log('Initializing production chart...');
            
            // Destroy existing chart if it exists
            if (charts.production) {
                charts.production.destroy();
            }
            
            const chartType = currentChartTypes.production;
            const config = chartConfigs.production[chartType] || { type: 'area' };
            console.log('Production chart config:', config);
            
            const productionOptions = {
                series: [{
                    name: 'Produksi Telur',
                    data: data.eggProduction
                }],
                chart: {
                    type: config.type || 'area',
                    height: 280,
                    toolbar: { 
                        show: true,
                        tools: {
                            download: true,
                            zoom: true,
                            reset: true
                        }
                    },
                    animations: { enabled: true, speed: 800 }
                },
                colors: [colors.success],
                xaxis: {
                    categories: data.categories,
                    labels: { 
                        style: { colors: '#6b7280', fontSize: '11px' }
                    }
                },
                yaxis: {
                    labels: { 
                        style: { colors: '#6b7280', fontSize: '11px' },
                        formatter: val => val + ' butir'
                    }
                },
                tooltip: {
                    y: { 
                        formatter: val => `${val} butir telur`,
                        title: { formatter: () => 'Produksi: ' }
                    }
                },
                dataLabels: { enabled: false },
                stroke: config.stroke || { curve: 'smooth' },
                fill: config.fill || { type: 'solid' }
            };
            
            charts.production = new ApexCharts(productionElement, productionOptions);
            charts.production.render();
            console.log('Production chart rendered successfully');
        } else {
            console.error('Production chart element not found!');
        }

        // DOC Sales Chart
        const docElement = document.getElementById('docChart');
        if (docElement) {
            console.log('Initializing DOC chart...');
            
            // Destroy existing chart if it exists
            if (charts.doc) {
                charts.doc.destroy();
            }
            
            const chartType = currentChartTypes.doc;
            const config = chartConfigs.doc[chartType] || { type: 'column' };
            console.log('DOC chart config:', config);
            
            const docOptions = {
                series: [{
                    name: 'Penjualan DOC',
                    data: data.docSales
                }],
                chart: {
                    type: config.type || 'column',
                    height: 280,
                    toolbar: { 
                        show: true,
                        tools: {
                            download: true,
                            zoom: true,
                            reset: true
                        }
                    },
                    animations: { enabled: true, speed: 800 }
                },
                colors: [colors.warning],
                xaxis: {
                    categories: data.categories,
                    labels: { 
                        style: { colors: '#6b7280', fontSize: '11px' }
                    }
                },
                yaxis: {
                    labels: { 
                        style: { colors: '#6b7280', fontSize: '11px' },
                        formatter: val => val + ' ekor'
                    }
                },
                tooltip: {
                    y: { 
                        formatter: val => `${val} ekor DOC`,
                        title: { formatter: () => 'Penjualan: ' }
                    }
                },
                dataLabels: { enabled: false },
                stroke: config.stroke || { curve: 'smooth' },
                fill: config.fill || { type: 'solid' },
                plotOptions: config.plotOptions || { bar: { borderRadius: 4 } }
            };
            
            charts.doc = new ApexCharts(docElement, docOptions);
            charts.doc.render();
            console.log('DOC chart rendered successfully');
        } else {
            console.error('DOC chart element not found!');
        }

        // Mortality Chart
        const mortalityElement = document.getElementById('mortalityChart');
        if (mortalityElement) {
            console.log('Initializing mortality chart...');
            
            // Destroy existing chart if it exists
            if (charts.mortality) {
                charts.mortality.destroy();
            }
            
            const chartType = currentChartTypes.mortality;
            const config = chartConfigs.mortality[chartType] || { type: 'line' };
            console.log('Mortality chart config:', config);
            
            const mortalityOptions = {
                series: [{
                    name: 'Mortalitas',
                    data: data.mortality
                }],
                chart: {
                    type: config.type || 'line',
                    height: 280,
                    toolbar: { 
                        show: true,
                        tools: {
                            download: true,
                            zoom: true,
                            reset: true
                        }
                    },
                    animations: { enabled: true, speed: 800 }
                },
                colors: [colors.danger],
                xaxis: {
                    categories: data.categories,
                    labels: { 
                        style: { colors: '#6b7280', fontSize: '11px' }
                    }
                },
                yaxis: {
                    labels: { 
                        style: { colors: '#6b7280', fontSize: '11px' },
                        formatter: val => val + ' ekor'
                    }
                },
                tooltip: {
                    y: { 
                        formatter: val => `${val} ekor`,
                        title: { formatter: () => 'Mortalitas: ' }
                    }
                },
                dataLabels: { enabled: false },
                stroke: config.stroke || { curve: 'smooth', width: 3 },
                fill: config.fill || { type: 'solid' }
            };
            
            charts.mortality = new ApexCharts(mortalityElement, mortalityOptions);
            charts.mortality.render();
            console.log('Mortality chart rendered successfully');
        } else {
            console.error('Mortality chart element not found!');
        }
    }

    // Initialize all charts
    initMainChart();
    initIndividualCharts();

    // Period Filter Event Handlers
    document.querySelectorAll('.period-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active class from all buttons
            document.querySelectorAll('.period-btn').forEach(b => b.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            // Get selected period
            const period = this.getAttribute('data-period');
            currentPeriod = period;
            
            // Update all charts with new period data
            initMainChart(period, currentChartTypes.main);
            initIndividualCharts(period);
            
            console.log('Period changed to:', period);
        });
    });

    // Chart Type Filter Event Handlers
    document.querySelectorAll('.chart-type-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active class from all buttons
            document.querySelectorAll('.chart-type-btn').forEach(b => b.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            // Get selected chart type
            const chartType = this.getAttribute('data-type');
            currentChartTypes.main = chartType;
            
            // Update main chart with new type
            initMainChart(currentPeriod, chartType);
            
            console.log('Main chart type changed to:', chartType);
        });
    });

    // Individual Chart Options Event Handlers
    document.querySelectorAll('.chart-option-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const chartCard = this.closest('.chart-card');
            const chartId = chartCard.querySelector('.chart-container > div').id;
            const option = this.getAttribute('data-option');
            
            // Remove active class from siblings
            chartCard.querySelectorAll('.chart-option-btn').forEach(b => b.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            // Update chart type based on chart ID
            if (chartId === 'productionChart') {
                currentChartTypes.production = option;
            } else if (chartId === 'docChart') {
                currentChartTypes.doc = option;
            } else if (chartId === 'mortalityChart') {
                currentChartTypes.mortality = option;
            }
            
            // Reinitialize individual charts with new type
            initIndividualCharts(currentPeriod);
            
            console.log(`${chartId} type changed to:`, option);
        });
    });

    // Enhanced Tooltip System
    function initTooltipSystem() {
        document.querySelectorAll('[data-tooltip]').forEach(element => {
            let tooltipEl = null;
            
            element.addEventListener('mouseenter', function(e) {
                const tooltipText = this.getAttribute('data-tooltip');
                if (!tooltipText) return;
                
                // Create tooltip element
                tooltipEl = document.createElement('div');
                tooltipEl.className = 'custom-tooltip';
                tooltipEl.textContent = tooltipText;
                tooltipEl.style.cssText = `
                    position: absolute;
                    background: rgba(0, 0, 0, 0.9);
                    color: white;
                    padding: 8px 12px;
                    border-radius: 6px;
                    font-size: 12px;
                    font-weight: 500;
                    white-space: nowrap;
                    z-index: 1000;
                    opacity: 0;
                    transition: opacity 0.2s ease;
                    pointer-events: none;
                    max-width: 250px;
                    word-wrap: break-word;
                    white-space: normal;
                `;
                
                document.body.appendChild(tooltipEl);
                
                // Position tooltip
                const rect = this.getBoundingClientRect();
                const tooltipRect = tooltipEl.getBoundingClientRect();
                
                let left = rect.left + (rect.width / 2) - (tooltipRect.width / 2);
                let top = rect.top - tooltipRect.height - 8;
                
                // Adjust if tooltip goes off screen
                if (left < 10) left = 10;
                if (left + tooltipRect.width > window.innerWidth - 10) {
                    left = window.innerWidth - tooltipRect.width - 10;
                }
                if (top < 10) {
                    top = rect.bottom + 8;
                }
                
                tooltipEl.style.left = left + 'px';
                tooltipEl.style.top = top + 'px';
                
                // Fade in
                setTimeout(() => {
                    if (tooltipEl) tooltipEl.style.opacity = '1';
                }, 10);
            });
            
            element.addEventListener('mouseleave', function() {
                if (tooltipEl) {
                    tooltipEl.style.opacity = '0';
                    setTimeout(() => {
                        if (tooltipEl && tooltipEl.parentNode) {
                            tooltipEl.parentNode.removeChild(tooltipEl);
                        }
                        tooltipEl = null;
                    }, 200);
                }
            });
        });
    }

    // Initialize enhanced tooltip system
    initTooltipSystem();

    // Animate KPI values on load
    setTimeout(() => {
        document.querySelectorAll('.kpi-value').forEach(el => {
            const finalValue = parseInt(el.textContent.replace(/,/g, ''));
            let currentValue = 0;
            const increment = finalValue / 50;
            const timer = setInterval(() => {
                currentValue += increment;
                if (currentValue >= finalValue) {
                    currentValue = finalValue;
                    clearInterval(timer);
                }
                el.textContent = Math.floor(currentValue).toLocaleString();
            }, 30);
        });
    }, 500);

    // Add notification system for real-time updates
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${type === 'success' ? '#10b981' : type === 'warning' ? '#f59e0b' : '#0ea5e9'};
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 1000;
            transform: translateX(100%);
            transition: transform 0.3s ease;
        `;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Slide in
        setTimeout(() => notification.style.transform = 'translateX(0)', 100);
        
        // Auto remove
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }

    // Show welcome notification
    setTimeout(() => {
        showNotification('Dashboard VigaZaFarm berhasil dimuat!', 'success');
    }, 1000);

    console.log('VigaZaFarm Dashboard initialized successfully with enhanced features');
});

    // 3. Capacity Management Charts
    function initCapacityCharts() {
        // Kapasitas Kandang Chart
        const kapasitasKandangElement = document.getElementById('kapasitasKandangChart');
        if (kapasitasKandangElement) {
            const kapasitasKandangOptions = {
                series: [
                    {
                        name: 'Terisi',
                        data: farmData.kapasitas.kandangTerisi
                    },
                    {
                        name: 'Kosong',
                        data: farmData.kapasitas.kandangKosong
                    }
                ],
                chart: {
                    type: 'bar',
                    height: 300,
                    stacked: true
                },
                colors: [colors.primary, '#f3f4f6'],
                plotOptions: {
                    bar: {
                        horizontal: false,
                        borderRadius: 4
                    }
                },
                xaxis: {
                    categories: farmData.kapasitas.categories
                },
                yaxis: {
                    title: {
                        text: 'Kapasitas'
                    }
                },
                legend: {
                    position: 'top'
                }
            };
            charts.kapasitasKandang = new ApexCharts(kapasitasKandangElement, kapasitasKandangOptions);
            charts.kapasitasKandang.render();
        }

        // Utilisasi Equipment Chart
        const equipmentElement = document.getElementById('equipmentChart');
        if (equipmentElement) {
            const equipmentLabels = Object.keys(farmData.equipment);
            const equipmentData = Object.values(farmData.equipment);
            
            const equipmentOptions = {
                series: equipmentData,
                chart: {
                    type: 'radialBar',
                    height: 350
                },
                plotOptions: {
                    radialBar: {
                        dataLabels: {
                            name: {
                                fontSize: '14px'
                            },
                            value: {
                                fontSize: '16px',
                                formatter: function (val) {
                                    return parseInt(val) + '%';
                                }
                            },
                            total: {
                                show: true,
                                label: 'Total',
                                formatter: function (w) {
                                    return Math.round(equipmentData.reduce((a, b) => a + b, 0) / equipmentData.length) + '%';
                                }
                            }
                        }
                    }
                },
                labels: equipmentLabels,
                colors: [colors.success, colors.warning, colors.primary, colors.info, colors.danger]
            };
            charts.equipment = new ApexCharts(equipmentElement, equipmentOptions);
            charts.equipment.render();
        }
    }

    // 4. Financial Charts
    function initFinancialCharts() {
        // Pendapatan Chart
        const pendapatanElement = document.getElementById('pendapatanChart');
        if (pendapatanElement) {
            const pendapatanOptions = {
                series: [
                    {
                        name: 'Penjualan Telur',
                        data: farmData.financial.penjualanTelur
                    },
                    {
                        name: 'Penjualan DOC',
                        data: farmData.financial.penjualanDOC
                    },
                    {
                        name: 'Penjualan Ayam',
                        data: farmData.financial.penjualanAyam
                    }
                ],
                chart: {
                    type: 'line',
                    height: 350
                },
                colors: [colors.success, colors.warning, colors.primary],
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                xaxis: {
                    categories: farmData.trends.categories
                },
                yaxis: {
                    title: {
                        text: 'Rupiah (Juta)'
                    },
                    labels: {
                        formatter: function (val) {
                            return 'Rp ' + (val / 1000000).toFixed(1) + 'M';
                        }
                    }
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return 'Rp ' + val.toLocaleString();
                        }
                    }
                }
            };
            charts.pendapatan = new ApexCharts(pendapatanElement, pendapatanOptions);
            charts.pendapatan.render();
        }

        // Biaya Operasional Chart
        const biayaElement = document.getElementById('biayaChart');
        if (biayaElement) {
            const biayaOptions = {
                series: [
                    {
                        name: 'Biaya Pakan',
                        data: farmData.financial.biayaPakan
                    },
                    {
                        name: 'Biaya Obat',
                        data: farmData.financial.biayaObat
                    },
                    {
                        name: 'Biaya Listrik',
                        data: farmData.financial.biayaListrik
                    },
                    {
                        name: 'Lainnya',
                        data: farmData.financial.biayaLainnya
                    }
                ],
                chart: {
                    type: 'area',
                    height: 350,
                    stacked: true
                },
                colors: [colors.warning, colors.danger, colors.info, '#6b7280'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        opacityFrom: 0.6,
                        opacityTo: 0.8
                    }
                },
                stroke: {
                    curve: 'smooth'
                },
                xaxis: {
                    categories: farmData.trends.categories
                },
                yaxis: {
                    title: {
                        text: 'Rupiah (Juta)'
                    },
                    labels: {
                        formatter: function (val) {
                            return 'Rp ' + (val / 1000000).toFixed(1) + 'M';
                        }
                    }
                }
            };
            charts.biaya = new ApexCharts(biayaElement, biayaOptions);
            charts.biaya.render();
        }

        // Profit/Loss Chart
        const profitElement = document.getElementById('profitChart');
        if (profitElement) {
            const profitOptions = {
                series: [
                    {
                        name: 'Keuntungan Bersih',
                        data: farmData.financial.keuntunganBersih
                    }
                ],
                chart: {
                    type: 'bar',
                    height: 300
                },
                colors: [colors.success],
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        columnWidth: '60%'
                    }
                },
                xaxis: {
                    categories: farmData.trends.categories
                },
                yaxis: {
                    title: {
                        text: 'Rupiah (Juta)'
                    },
                    labels: {
                        formatter: function (val) {
                            return 'Rp ' + (val / 1000000).toFixed(1) + 'M';
                        }
                    }
                },
                dataLabels: {
                    enabled: true,
                    formatter: function (val) {
                        return 'Rp ' + (val / 1000000).toFixed(1) + 'M';
                    }
                }
            };
            charts.profit = new ApexCharts(profitElement, profitOptions);
            charts.profit.render();
        }
    }

    // 5. Main Dashboard Initialization
    function initAllCharts() {
        // Initialize all chart sections
        initPipelineCharts();
        initMonitoringCharts();
        initCapacityCharts();
        initFinancialCharts();
    }

    // Update real-time clock
    function updateClock() {
        const now = new Date();
        const options = {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        };
        
        const dateTimeElement = document.getElementById('current-datetime');
        if (dateTimeElement) {
            dateTimeElement.textContent = now.toLocaleDateString('id-ID', options);
        }
    }

    // Initialize dashboard when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Chicken Farm Dashboard initializing...');
        
        // Start clock
        updateClock();
        setInterval(updateClock, 1000);
        
        // Initialize all charts with delay for proper rendering
        setTimeout(() => {
            initAllCharts();
        }, 500);
        
        console.log('Chicken Farm Dashboard initialized successfully');
    });
