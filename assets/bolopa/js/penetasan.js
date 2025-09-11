/**
 * Vigaza Farm - Penetasan Module JavaScript
 * Specific functions for penetasan management
 */

// Penetasan Module Configuration
const VigzaPenetasan = {
    baseUrl: VigzaAdmin.baseUrl,
    
    // Initialize module
    init: function() {
        this.initFormValidation();
        this.initBatchGenerator();
        this.initModalHandlers();
        this.initDataTable();
    },
    
    // Initialize form validation
    initFormValidation: function() {
        // Custom validation for penetasan forms
        $('.form-penetasan').on('submit', function(e) {
            const form = $(this);
            let isValid = true;
            
            // Validate jumlah telur
            const jumlahTelur = parseInt(form.find('#jumlah_telur').val()) || 0;
            if (jumlahTelur <= 0) {
                isValid = false;
                VigzaAdmin.swal.warning('Validasi Error', 'Jumlah telur harus lebih dari 0');
            }
            
            // Validate suhu
            const suhu = parseFloat(form.find('#suhu_rata').val()) || 0;
            if (suhu < 35 || suhu > 40) {
                isValid = false;
                VigzaAdmin.swal.warning('Validasi Error', 'Suhu harus antara 35-40°C');
            }
            
            // Validate kelembaban
            const kelembaban = parseFloat(form.find('#kelembaban_rata').val()) || 0;
            if (kelembaban < 40 || kelembaban > 80) {
                isValid = false;
                VigzaAdmin.swal.warning('Validasi Error', 'Kelembaban harus antara 40-80%');
            }
            
            if (!isValid) {
                e.preventDefault();
                return false;
            }
        });
    },
    
    // Initialize batch code generator
    initBatchGenerator: function() {
        const $batchField = $('#batch');
        if ($batchField.length && !$batchField.val()) {
            // Add generate button
            $batchField.after(`
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-outline-vigaza" onclick="VigzaPenetasan.generateBatch()">
                        <i class="fas fa-magic me-1"></i> Generate Batch
                    </button>
                </div>
            `);
        }
    },
    
    // Generate batch code
    generateBatch: function() {
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const timestamp = Date.now().toString().slice(-3);
        
        const batchCode = `BATCH-${year}-${month}-${day}-${timestamp}`;
        $('#batch').val(batchCode);
        
        VigzaAdmin.swal.success('Berhasil!', `Batch code generated: ${batchCode}`);
    },
    
    // Initialize modal handlers
    initModalHandlers: function() {
        // Modal selesai penetasan
        window.selesaiPenetasan = function(id, batch) {
            $('#modalSelesai').modal('show');
            $('#batchName').text(batch);
            $('#formSelesai').attr('action', VigzaPenetasan.baseUrl + 'penetasan/selesai/' + id);
            
            // Reset form
            $('#hasil_menetas').val('');
            $('#hasil_gagal').val('');
        };
        
        // Form selesai submit handler
        $('#formSelesai').on('submit', function(e) {
            const hasilMenetas = parseInt($('#hasil_menetas').val()) || 0;
            const hasilGagal = parseInt($('#hasil_gagal').val()) || 0;
            
            if (hasilMenetas <= 0 && hasilGagal <= 0) {
                e.preventDefault();
                VigzaAdmin.swal.warning('Validasi Error', 'Minimal salah satu hasil harus diisi dengan nilai > 0');
                return false;
            }
            
            // Show loading
            const $btn = $(this).find('button[type="submit"]');
            const originalText = $btn.html();
            $btn.prop('disabled', true);
            $btn.html('<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...');
            
            // Reset after 10 seconds if not redirected
            setTimeout(() => {
                $btn.prop('disabled', false);
                $btn.html(originalText);
            }, 10000);
        });
    },
    
    // Initialize DataTable with custom settings
    initDataTable: function() {
        if ($('.table-penetasan').length) {
            $('.table-penetasan').DataTable({
                responsive: true,
                pageLength: 15,
                order: [[0, 'desc']], // Order by ID descending
                columnDefs: [
                    {
                        targets: -1, // Last column (actions)
                        orderable: false,
                        searchable: false
                    },
                    {
                        targets: [6], // Persentase column
                        render: function(data, type, row) {
                            if (type === 'display') {
                                const percentage = parseFloat(data);
                                let badgeClass = 'secondary';
                                
                                if (percentage >= 85) badgeClass = 'success';
                                else if (percentage >= 70) badgeClass = 'warning';
                                else if (percentage > 0) badgeClass = 'danger';
                                
                                return `<span class="badge badge-${badgeClass}">${percentage.toFixed(1)}%</span>`;
                            }
                            return data;
                        }
                    },
                    {
                        targets: [9], // Status column
                        render: function(data, type, row) {
                            if (type === 'display') {
                                let badgeClass = 'secondary';
                                switch (data.toLowerCase()) {
                                    case 'proses':
                                        badgeClass = 'warning';
                                        break;
                                    case 'selesai':
                                        badgeClass = 'success';
                                        break;
                                    case 'gagal':
                                        badgeClass = 'danger';
                                        break;
                                }
                                return `<span class="badge badge-${badgeClass}">${data}</span>`;
                            }
                            return data;
                        }
                    }
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
                },
                drawCallback: function() {
                    VigzaAdmin.initTooltips();
                }
            });
        }
    },
    
    // Calculate hatching percentage
    calculatePercentage: function(menetas, total) {
        if (!total || total <= 0) return 0;
        return (menetas / total) * 100;
    },
    
    // Validate batch code format
    validateBatchCode: function(batch) {
        const pattern = /^BATCH-\d{4}-\d{2}-\d{2}(-\d{3})?$/;
        return pattern.test(batch);
    },
    
    // Format date for display
    formatDate: function(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
    },
    
    // Calculate days difference
    daysDifference: function(startDate, endDate) {
        const start = new Date(startDate);
        const end = new Date(endDate);
        const diffTime = Math.abs(end - start);
        return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    }
};

// Auto-initialize when DOM is ready
$(document).ready(function() {
    if (VigzaAdmin.currentModule === 'penetasan') {
        VigzaPenetasan.init();
    }
});

// Export global object
window.VigzaPenetasan = VigzaPenetasan;
