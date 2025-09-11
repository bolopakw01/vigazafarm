/**
 * Vigaza Farm - Admin Dashboard JavaScript
 * Common functions for all admin modules
 */

// Global Configuration
const VigzaAdmin = {
    baseUrl: $('base').attr('href') || window.location.origin + '/',
    currentModule: '',
    
    // SweetAlert2 Configuration
    swal: {
        success: (title, text = '') => {
            return Swal.fire({
                icon: 'success',
                title: title,
                text: text,
                showConfirmButton: false,
                timer: 2000,
                toast: true,
                position: 'top-end',
                background: '#f0f9ff',
                customClass: {
                    popup: 'colored-toast'
                }
            });
        },
        
        error: (title, text = '') => {
            return Swal.fire({
                icon: 'error',
                title: title,
                text: text,
                showConfirmButton: true,
                confirmButtonColor: '#ef4444',
                background: '#fef2f2',
                customClass: {
                    popup: 'colored-toast'
                }
            });
        },
        
        warning: (title, text = '') => {
            return Swal.fire({
                icon: 'warning',
                title: title,
                text: text,
                showConfirmButton: true,
                confirmButtonColor: '#f59e0b',
                background: '#fffbeb',
                customClass: {
                    popup: 'colored-toast'
                }
            });
        },
        
        confirm: (title, text = '', confirmText = 'Ya, Hapus!') => {
            return Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: confirmText,
                cancelButtonText: 'Batal',
                background: '#fffbeb',
                customClass: {
                    popup: 'colored-toast'
                }
            });
        }
    }
};

// Initialize when document ready
$(document).ready(function() {
    VigzaAdmin.init();
});

// Main initialization
VigzaAdmin.init = function() {
    this.initDataTables();
    this.initDatePickers();
    this.initFormValidation();
    this.initTooltips();
    this.bindGlobalEvents();
};

// Initialize DataTables
VigzaAdmin.initDataTables = function() {
    if ($.fn.DataTable) {
        $('.js-basic-example').DataTable({
            responsive: true,
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
            },
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                 '<"row"<"col-sm-12"tr>>' +
                 '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            drawCallback: function() {
                VigzaAdmin.initTooltips();
            }
        });
    }
};

// Initialize Date Pickers
VigzaAdmin.initDatePickers = function() {
    if ($.fn.datepicker) {
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
            language: 'id'
        });
    }
};

// Initialize Form Validation
VigzaAdmin.initFormValidation = function() {
    // Custom validation for required fields
    $('.form-required').on('submit', function(e) {
        let isValid = true;
        
        $(this).find('[required]').each(function() {
            if (!$(this).val()) {
                isValid = false;
                $(this).addClass('is-invalid');
                
                // Add error message if not exists
                if (!$(this).siblings('.invalid-feedback').length) {
                    $(this).after('<div class="invalid-feedback">Field ini wajib diisi</div>');
                }
            } else {
                $(this).removeClass('is-invalid');
                $(this).siblings('.invalid-feedback').remove();
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            VigzaAdmin.swal.warning('Validasi Error', 'Mohon lengkapi semua field yang wajib diisi');
        }
    });
    
    // Remove validation on input
    $('[required]').on('input change', function() {
        if ($(this).val()) {
            $(this).removeClass('is-invalid');
            $(this).siblings('.invalid-feedback').remove();
        }
    });
};

// Initialize Tooltips
VigzaAdmin.initTooltips = function() {
    if ($.fn.tooltip) {
        $('[data-toggle="tooltip"]').tooltip();
    }
};

// Bind Global Events
VigzaAdmin.bindGlobalEvents = function() {
    // Loading state for forms
    $('.form-submit').on('submit', function() {
        const $btn = $(this).find('button[type="submit"]');
        const originalText = $btn.html();
        
        $btn.prop('disabled', true);
        $btn.html('<span class="spinner-border spinner-border-sm me-2"></span>Memproses...');
        
        // Reset after 5 seconds if not handled by success/error
        setTimeout(() => {
            $btn.prop('disabled', false);
            $btn.html(originalText);
        }, 5000);
    });
    
    // Confirm delete buttons
    $('.btn-delete').on('click', function(e) {
        e.preventDefault();
        const url = $(this).attr('href') || $(this).data('url');
        const itemName = $(this).data('name') || 'item ini';
        
        VigzaAdmin.swal.confirm(
            'Konfirmasi Hapus',
            `Apakah Anda yakin ingin menghapus ${itemName}? Tindakan ini tidak dapat dibatalkan.`
        ).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    });
    
    // Auto-hide alerts
    $('.alert').each(function() {
        const $alert = $(this);
        if ($alert.hasClass('alert-success')) {
            setTimeout(() => {
                $alert.fadeOut();
            }, 3000);
        }
    });
};

// Utility Functions
VigzaAdmin.utils = {
    // Format currency
    formatCurrency: function(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(amount);
    },
    
    // Format date
    formatDate: function(date, format = 'dd/mm/yyyy') {
        if (!date) return '-';
        const d = new Date(date);
        const day = String(d.getDate()).padStart(2, '0');
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const year = d.getFullYear();
        
        switch (format) {
            case 'dd/mm/yyyy':
                return `${day}/${month}/${year}`;
            case 'yyyy-mm-dd':
                return `${year}-${month}-${day}`;
            default:
                return d.toLocaleDateString('id-ID');
        }
    },
    
    // Show loading
    showLoading: function(element) {
        $(element).addClass('loading');
        $(element).append('<div class="loading-overlay"><div class="spinner-border text-primary"></div></div>');
    },
    
    // Hide loading
    hideLoading: function(element) {
        $(element).removeClass('loading');
        $(element).find('.loading-overlay').remove();
    },
    
    // Debounce function
    debounce: function(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
};

// AJAX Setup
$.ajaxSetup({
    beforeSend: function(xhr, settings) {
        // Add CSRF token if available
        const token = $('meta[name="csrf-token"]').attr('content');
        if (token) {
            xhr.setRequestHeader('X-CSRF-TOKEN', token);
        }
    },
    error: function(xhr, status, error) {
        console.error('AJAX Error:', error);
        VigzaAdmin.swal.error('Error', 'Terjadi kesalahan dalam memproses permintaan');
    }
});

// Export global object
window.VigzaAdmin = VigzaAdmin;
