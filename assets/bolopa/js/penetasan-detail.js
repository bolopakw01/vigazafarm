/**
 * Penetasan Detail Modal JavaScript
 * Professional and elegant detail view for penetasan data
 */

// Global namespace to avoid conflicts
var PenetasanDetail = {
    
    // Configuration
    config: {
        defaultDuration: 21,
        dateFormat: 'id-ID',
        statusColors: {
            'proses': '#0fa9b4',
            'selesai': '#28a745',
            'gagal': '#dc3545',
            'default': '#6c757d'
        }
    },
    
    // Utility functions
    utils: {
        // Safe date formatting
        formatDate: function(dateString, options) {
            if (!dateString) return 'Belum diatur';
            
            try {
                var date = new Date(dateString);
                if (isNaN(date.getTime())) return dateString;
                
                var defaultOptions = {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                };
                
                var finalOptions = options || defaultOptions;
                return date.toLocaleDateString(PenetasanDetail.config.dateFormat, finalOptions);
            } catch(e) {
                console.warn('Date formatting error:', e);
                return dateString;
            }
        },
        
        // Safe number formatting
        formatNumber: function(value, decimals) {
            if (value === null || value === undefined) return '0';
            
            var num = parseFloat(value);
            if (isNaN(num)) return '0';
            
            if (decimals !== undefined) {
                return num.toFixed(decimals);
            }
            
            return num.toString();
        },
        
        // Safe string extraction
        getString: function(value, fallback) {
            return (value && value.toString().trim()) || fallback || 'N/A';
        },
        
        // Calculate days difference
        daysDifference: function(startDate, endDate) {
            try {
                var start = new Date(startDate);
                var end = endDate ? new Date(endDate) : new Date();
                
                if (isNaN(start.getTime()) || isNaN(end.getTime())) {
                    return 0;
                }
                
                var diffTime = end.getTime() - start.getTime();
                return Math.floor(diffTime / (1000 * 60 * 60 * 24));
            } catch(e) {
                console.warn('Date calculation error:', e);
                return 0;
            }
        }
    },
    
    // Data processing functions
    processData: function(rawData, targetId) {
        if (!rawData || !Array.isArray(rawData)) {
            throw new Error('Invalid data format');
        }
        
        // Find target item
        var targetItem = null;
        for (var i = 0; i < rawData.length; i++) {
            if (rawData[i] && rawData[i].id_penetasan == targetId) {
                targetItem = rawData[i];
                break;
            }
        }
        
        if (!targetItem) {
            throw new Error('Data not found for ID: ' + targetId);
        }
        
        return this.formatItemData(targetItem);
    },
    
    formatItemData: function(item) {
        var utils = this.utils;
        var config = this.config;
        
        // Extract basic data
        var data = {
            batch: utils.getString(item.batch),
            mesin: utils.getString(item.nama_mesin),
            status: utils.getString(item.status, 'unknown').toLowerCase(),
            jumlahTelur: parseInt(item.jumlah_telur) || 0,
            hasilMenetas: parseInt(item.hasil_menetas) || 0,
            hasilGagal: parseInt(item.hasil_gagal) || 0,
            suhuRata: parseFloat(item.suhu_rata) || 0,
            kelembabanRata: parseFloat(item.kelembaban_rata) || 0,
            catatan: utils.getString(item.catatan, ''),
            lamaPenetasan: parseInt(item.lama_penetasan) || config.defaultDuration,
            tanggalMulai: item.tanggal_mulai || '',
            tanggalSelesai: item.tanggal_selesai || ''
        };
        
        // Calculate timeline data
        data.timeline = this.calculateTimeline(data);
        
        // Calculate success rate
        data.successRate = data.jumlahTelur > 0 ? 
            ((data.hasilMenetas / data.jumlahTelur) * 100).toFixed(1) : '0.0';
        
        // Get status color
        data.statusColor = config.statusColors[data.status] || config.statusColors.default;
        
        return data;
    },
    
    calculateTimeline: function(data) {
        var utils = this.utils;
        var timeline = {
            startFormatted: 'Belum diatur',
            endFormatted: 'Belum selesai',
            estimation: '',
            remaining: '',
            progress: 0,
            statusText: ''
        };
        
        // Format start date
        if (data.tanggalMulai) {
            timeline.startFormatted = utils.formatDate(data.tanggalMulai);
        }
        
        // Process active status
        if (data.status === 'proses' && data.tanggalMulai) {
            var daysPassed = utils.daysDifference(data.tanggalMulai);
            var currentDay = daysPassed + 1;
            var remaining = data.lamaPenetasan - daysPassed;
            
            // Calculate progress
            timeline.progress = Math.min(100, Math.max(0, (daysPassed / data.lamaPenetasan) * 100));
            
            // Calculate estimation
            try {
                var estimationDate = new Date(data.tanggalMulai);
                estimationDate.setDate(estimationDate.getDate() + data.lamaPenetasan);
                timeline.estimation = utils.formatDate(estimationDate.toISOString());
            } catch(e) {
                timeline.estimation = 'Error perhitungan';
            }
            
            // Format remaining text
            if (remaining > 0) {
                timeline.remaining = remaining + ' hari lagi';
                timeline.statusText = 'Hari ke-' + currentDay + ' dari ' + data.lamaPenetasan;
            } else if (remaining === 0) {
                timeline.remaining = 'Hari ini selesai';
                timeline.statusText = 'Target hari ke-' + data.lamaPenetasan;
            } else {
                timeline.remaining = 'Terlambat ' + Math.abs(remaining) + ' hari';
                timeline.statusText = 'Hari ke-' + currentDay;
            }
        }
        
        // Process completed status
        if (data.status === 'selesai' && data.tanggalSelesai) {
            timeline.endFormatted = utils.formatDate(data.tanggalSelesai);
            timeline.progress = 100;
            timeline.statusText = 'Selesai';
        }
        
        return timeline;
    },
    
    // HTML generation functions
    generateHTML: function(data) {
        var html = '<div class="penetasan-content">';
        
        // Header
        html += this.generateHeader(data);
        
        // Main content grid
        html += '<div class="row">';
        html += '<div class="col-md-6">';
        html += this.generateTimelineCard(data);
        html += this.generateInfoCard(data);
        html += '</div>';
        html += '<div class="col-md-6">';
        html += this.generateStatsCard(data);
        html += this.generateEnvironmentCard(data);
        html += '</div>';
        html += '</div>';
        
        // Notes if available
        if (data.catatan && data.catatan.trim().length > 0) {
            html += this.generateNotesSection(data);
        }
        
        html += '</div>';
        return html;
    },
    
    generateHeader: function(data) {
        var html = '<div class="penetasan-header">';
        html += '<div class="penetasan-batch-title">' + data.batch + '</div>';
        html += '<div class="penetasan-batch-subtitle">Kode Batch Penetasan</div>';
        html += '<span class="penetasan-status-badge" style="background-color: ' + data.statusColor + ';">';
        html += data.status.toUpperCase();
        html += '</span>';
        html += '</div>';
        return html;
    },
    
    generateTimelineCard: function(data) {
        var html = '<div class="penetasan-card" style="border-left: 4px solid ' + data.statusColor + ';">';
        html += '<div class="penetasan-card-header">';
        html += '<h6 class="penetasan-card-title">';
        html += '<i class="fa fa-calendar-check-o penetasan-card-icon" style="color: ' + data.statusColor + ';"></i>';
        html += 'Timeline Penetasan';
        html += '</h6>';
        html += '</div>';
        html += '<div class="penetasan-card-body">';
        
        // Start date
        html += '<div class="penetasan-timeline-item">';
        html += '<span class="penetasan-timeline-label">';
        html += '<i class="fa fa-play-circle" style="margin-right: 8px;"></i>Tanggal Mulai';
        html += '</span>';
        html += '<span class="penetasan-timeline-value">' + data.timeline.startFormatted + '</span>';
        html += '</div>';
        
        // Progress for active process
        if (data.status === 'proses') {
            html += '<div class="penetasan-progress-section">';
            html += '<div class="penetasan-progress-header">';
            html += '<span class="penetasan-timeline-label">Progress Penetasan</span>';
            html += '<span style="color: ' + data.statusColor + '; font-weight: 600;">' + Math.round(data.timeline.progress) + '%</span>';
            html += '</div>';
            html += '<div class="penetasan-progress-bar">';
            html += '<div class="penetasan-progress-fill" style="width: ' + data.timeline.progress + '%;"></div>';
            html += '</div>';
            html += '<div class="penetasan-progress-text">' + data.timeline.remaining + '</div>';
            html += '<div class="penetasan-progress-text">' + data.timeline.statusText + '</div>';
            html += '</div>';
            
            if (data.timeline.estimation) {
                html += '<div class="penetasan-timeline-item">';
                html += '<span class="penetasan-timeline-label">';
                html += '<i class="fa fa-flag-checkered" style="margin-right: 8px;"></i>Estimasi Selesai';
                html += '</span>';
                html += '<span class="penetasan-timeline-value" style="color: #0fa9b4;">' + data.timeline.estimation + '</span>';
                html += '</div>';
            }
        }
        
        // End date if completed
        if (data.status === 'selesai') {
            html += '<div class="penetasan-timeline-item">';
            html += '<span class="penetasan-timeline-label">';
            html += '<i class="fa fa-flag-checkered" style="margin-right: 8px;"></i>Tanggal Selesai';
            html += '</span>';
            html += '<span class="penetasan-timeline-value" style="color: #28a745;">' + data.timeline.endFormatted + '</span>';
            html += '</div>';
        }
        
        html += '</div>';
        html += '</div>';
        return html;
    },
    
    generateInfoCard: function(data) {
        var html = '<div class="penetasan-card">';
        html += '<div class="penetasan-card-header">';
        html += '<h6 class="penetasan-card-title">';
        html += '<i class="fa fa-info-circle penetasan-card-icon" style="color: #17a2b8;"></i>';
        html += 'Informasi Dasar';
        html += '</h6>';
        html += '</div>';
        html += '<div class="penetasan-card-body">';
        html += '<div class="row">';
        html += '<div class="col-6">';
        html += '<small style="color: #6c757d; display: block; margin-bottom: 5px;">Mesin Penetasan</small>';
        html += '<strong>' + data.mesin + '</strong>';
        html += '</div>';
        html += '<div class="col-6">';
        html += '<small style="color: #6c757d; display: block; margin-bottom: 5px;">Durasi Target</small>';
        html += '<strong>' + data.lamaPenetasan + ' Hari</strong>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        return html;
    },
    
    generateStatsCard: function(data) {
        var html = '<div class="penetasan-card">';
        html += '<div class="penetasan-card-header">';
        html += '<h6 class="penetasan-card-title">';
        html += '<i class="fa fa-bar-chart penetasan-card-icon" style="color: #0fa9b4;"></i>';
        html += 'Statistik Hasil';
        html += '</h6>';
        html += '</div>';
        html += '<div class="penetasan-card-body">';
        
        // Stats grid
        html += '<div class="penetasan-stats-grid">';
        html += '<div class="penetasan-stat-card total">';
        html += '<span class="penetasan-stat-number">' + data.jumlahTelur.toLocaleString('id-ID') + '</span>';
        html += '<span class="penetasan-stat-label">Total Telur</span>';
        html += '</div>';
        html += '<div class="penetasan-stat-card success">';
        html += '<span class="penetasan-stat-number">' + data.hasilMenetas.toLocaleString('id-ID') + '</span>';
        html += '<span class="penetasan-stat-label">Berhasil</span>';
        html += '</div>';
        html += '<div class="penetasan-stat-card failed">';
        html += '<span class="penetasan-stat-number">' + data.hasilGagal.toLocaleString('id-ID') + '</span>';
        html += '<span class="penetasan-stat-label">Gagal</span>';
        html += '</div>';
        html += '</div>';
        
        // Success rate
        html += '<div class="penetasan-success-rate">';
        html += '<div class="penetasan-success-rate-number">' + data.successRate + '%</div>';
        html += '<div class="penetasan-success-rate-label">Tingkat Keberhasilan</div>';
        html += '</div>';
        
        html += '</div>';
        html += '</div>';
        return html;
    },
    
    generateEnvironmentCard: function(data) {
        var html = '<div class="penetasan-card">';
        html += '<div class="penetasan-card-header">';
        html += '<h6 class="penetasan-card-title">';
        html += '<i class="fa fa-thermometer-half penetasan-card-icon" style="color: #ffc107;"></i>';
        html += 'Parameter Lingkungan';
        html += '</h6>';
        html += '</div>';
        html += '<div class="penetasan-card-body">';
        html += '<div class="penetasan-env-grid">';
        html += '<div class="penetasan-env-item">';
        html += '<div class="penetasan-env-value temperature">' + this.utils.formatNumber(data.suhuRata, 1) + '°C</div>';
        html += '<div class="penetasan-env-label">Suhu Rata-rata</div>';
        html += '</div>';
        html += '<div class="penetasan-env-item">';
        html += '<div class="penetasan-env-value humidity">' + this.utils.formatNumber(data.kelembabanRata, 1) + '%</div>';
        html += '<div class="penetasan-env-label">Kelembaban</div>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        return html;
    },
    
    generateNotesSection: function(data) {
        var html = '<div class="row" style="margin-top: 20px;">';
        html += '<div class="col-12">';
        html += '<div class="penetasan-card">';
        html += '<div class="penetasan-card-header">';
        html += '<h6 class="penetasan-card-title">';
        html += '<i class="fa fa-sticky-note penetasan-card-icon" style="color: #17a2b8;"></i>';
        html += 'Catatan Khusus';
        html += '</h6>';
        html += '</div>';
        html += '<div class="penetasan-card-body">';
        html += '<div class="penetasan-notes">';
        html += '<p class="penetasan-notes-content">"' + data.catatan + '"</p>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        return html;
    },
    
    // Main show function
    show: function(id, rawData) {
        try {
            console.log('PenetasanDetail: Loading detail for ID:', id);
            
            // Validate SweetAlert2
            if (!window.Swal) {
                throw new Error('SweetAlert2 not available');
            }
            
            // Process data
            var data = this.processData(rawData, id);
            
            // Generate HTML
            var htmlContent = this.generateHTML(data);
            
            // Show modal
            window.Swal.fire({
                title: '<i class="fa fa-cube" style="margin-right: 8px;"></i>Detail Penetasan',
                html: htmlContent,
                width: '800px',
                maxWidth: '85%',
                padding: '0',
                confirmButtonText: '<i class="fa fa-times" style="margin-right: 6px;"></i>Tutup',
                confirmButtonColor: '#0fa9b4',
                buttonsStyling: true,
                customClass: {
                    popup: 'penetasan-detail-modal',
                    title: 'penetasan-detail-title',
                    htmlContainer: 'penetasan-detail-content',
                    actions: 'penetasan-detail-actions'
                },
                showClass: {
                    popup: 'animate__animated animate__fadeInUp animate__faster'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutDown animate__faster'
                }
            });
            
        } catch (error) {
            console.error('PenetasanDetail Error:', error);
            
            if (window.Swal) {
                window.Swal.fire({
                    title: 'Error',
                    text: 'Terjadi kesalahan: ' + error.message,
                    icon: 'error',
                    confirmButtonColor: '#dc3545'
                });
            } else {
                alert('Error: ' + error.message);
            }
        }
    }
};

// Global function for backward compatibility
function showDetail(id) {
    // Get PHP data safely
    var phpData = null;
    
    try {
        // Use the global data set in the view
        phpData = window.penetasanData || null;
        
        if (!phpData) {
            throw new Error('No data available');
        }
        
        PenetasanDetail.show(id, phpData);
        
    } catch (error) {
        console.error('showDetail Error:', error);
        
        if (window.Swal) {
            window.Swal.fire('Error', 'Tidak dapat memuat data: ' + error.message, 'error');
        } else {
            alert('Error: ' + error.message);
        }
    }
}
