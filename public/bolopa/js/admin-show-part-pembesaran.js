// ===== AJAX FUNCTIONS SIAP PAKAI untuk admin-show-part-pembesaran.js =====
// Copy dan tambahkan ke file JavaScript existing

// ========== CONFIGURATION ==========
// Get config from blade template (with fallback to auto-detect)
const config = window.vigazaConfig || {
    baseUrl: window.location.origin + window.location.pathname.split('/admin')[0],
    pembesaranId: window.location.pathname.match(/\/pembesaran\/(\d+)/)?.[1],
    csrfToken: document.querySelector('meta[name="csrf-token"]')?.content
};

const pembesaranId = config.pembesaranId;
const baseUrl = config.baseUrl;
const getCsrfToken = () => config.csrfToken || 
                           document.querySelector('meta[name="csrf-token"]')?.content || 
                           document.querySelector('input[name="_token"]')?.value;

// Normalize date-like values to YYYY-MM-DD for reliable comparisons
function toDateKey(input) {
    if (!input) return '';
    // If already ISO-like yyyy-mm-dd
    const isoMatch = String(input).match(/^(\d{4}-\d{2}-\d{2})/);
    if (isoMatch) return isoMatch[1];

    // If in dd/mm/yyyy or dd-mm-yyyy
    const dmMatch = String(input).match(/^(\d{2})[\/\-](\d{2})[\/\-](\d{4})/);
    if (dmMatch) return `${dmMatch[3]}-${dmMatch[2]}-${dmMatch[1]}`;

    // Fallback: try Date parse
    const d = new Date(input);
    if (!isNaN(d)) return d.toISOString().slice(0,10);

    // As last resort, return original string
    return String(input);
}

const batchStartDateKey = toDateKey(config.batchStartDate);
const batchStartDateLabel = batchStartDateKey
    ? new Date(batchStartDateKey).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
    : null;

function validateBatchDate(dateValue, contextLabel = 'Pencatatan') {
    if (!batchStartDateKey || !dateValue) return true;
    const entryKey = toDateKey(dateValue);
    if (!entryKey) return true;
    if (entryKey < batchStartDateKey) {
        const label = batchStartDateLabel || batchStartDateKey;
        showToast(`${contextLabel} tidak boleh sebelum tanggal masuk batch (${label}).`, 'warning');
        return false;
    }
    return true;
}

// ========== DUPLICATE SUBMISSION GUARD ==========

const duplicateTypeLabels = {
    pakan: 'pencatatan pakan',
    kematian: 'pencatatan kematian',
    monitoring: 'monitoring lingkungan',
    kesehatan: 'catatan kesehatan/vaksinasi',
    berat: 'sampling berat'
};

const duplicateEndpointPaths = {
    pakan: 'pakan/list',
    kematian: 'kematian/list',
    monitoring: 'monitoring/list',
    kesehatan: 'kesehatan/list',
    berat: 'berat/list'
};

const duplicateDateCache = {
    pakan: new Set(),
    kematian: new Set(),
    monitoring: new Set(),
    kesehatan: new Set(),
    berat: new Set()
};

function setDatasetCache(type, records = []) {
    const key = datasetCacheKeys[type];
    if (!key) return;
    window[key] = Array.isArray(records) ? records : [];
}

function getDatasetCache(type) {
    const key = datasetCacheKeys[type];
    if (!key) return [];
    const data = window[key];
    return Array.isArray(data) ? data : [];
}

const datasetCacheKeys = {
    pakan: '_pakanData',
    kematian: '_kematianData',
    monitoring: '_monitoringData',
    kesehatan: '_kesehatanData',
    berat: '_beratData'
};

const datasetByDateCache = {
    pakan: {},
    kematian: {},
    monitoring: {},
    kesehatan: {},
    berat: {}
};

function getRecordsByDateCache(type, dateKey) {
    if (!type || !dateKey) return null;
    const bucket = datasetByDateCache[type];
    if (!bucket) return null;
    if (Object.prototype.hasOwnProperty.call(bucket, dateKey)) {
        return bucket[dateKey];
    }
    return null;
}

function setRecordsByDateCache(type, dateKey, records) {
    if (!type || !dateKey) return;
    if (!datasetByDateCache[type]) {
        datasetByDateCache[type] = {};
    }
    datasetByDateCache[type][dateKey] = Array.isArray(records) ? records : [];
}

function getRecordIdentityKey(type, record) {
    if (!record) return null;
    if (record.id !== undefined && record.id !== null) {
        return `${type}-${record.id}`;
    }
    if (record.uuid) {
        return `${type}-${record.uuid}`;
    }
    const dateKey = toDateKey(extractRecordDate(type, record));
    try {
        return `${type}-${dateKey}-${JSON.stringify(record)}`;
    } catch (error) {
        console.warn('Unable to stringify record for identity key:', error);
        return `${type}-${dateKey}-${Math.random().toString(36).slice(2)}`;
    }
}

function mergeRecordsIntoCache(type, newRecords = []) {
    if (!type || !Array.isArray(newRecords) || !newRecords.length) return;
    const existing = getDatasetCache(type) || [];
    const map = new Map();
    existing.forEach((record) => {
        const key = getRecordIdentityKey(type, record);
        if (key) {
            map.set(key, record);
        }
    });
    newRecords.forEach((record) => {
        const key = getRecordIdentityKey(type, record);
        if (key) {
            map.set(key, record);
        }
    });
    setDatasetCache(type, Array.from(map.values()));
}

async function getRecordsForDate(type, dateKey, dateExtractor) {
    if (!type || !dateKey) return [];
    const cachedByDate = getRecordsByDateCache(type, dateKey);
    if (cachedByDate !== null && cachedByDate !== undefined) return cachedByDate;

    const resolver = (row) => {
        if (typeof dateExtractor === 'function') {
            return dateExtractor(row);
        }
        return extractRecordDate(type, row);
    };

    const source = getDatasetCache(type) || [];
    const matches = source.filter((row) => toDateKey(resolver(row)) === dateKey);
    if (matches.length) {
        setRecordsByDateCache(type, dateKey, matches);
        return matches;
    }

    const endpoint = duplicateEndpointPaths[type];
    if (!endpoint) {
        const emptyArr = [];
        setRecordsByDateCache(type, dateKey, emptyArr);
        return emptyArr;
    }

    try {
        const url = `${baseUrl}/admin/pembesaran/${pembesaranId}/${endpoint}?tanggal=${encodeURIComponent(dateKey)}`;
        const response = await fetch(url, { credentials: 'same-origin', cache: 'no-store' });
        const result = await response.json();
        const rows = result?.success && Array.isArray(result.data) ? result.data : [];
        setRecordsByDateCache(type, dateKey, rows);
        if (rows.length) {
            mergeRecordsIntoCache(type, rows);
            rememberRecords(type, rows);
        }
        return rows;
    } catch (error) {
        console.error(`Failed to fetch ${type} data for ${dateKey}:`, error);
        const fallback = [];
        setRecordsByDateCache(type, dateKey, fallback);
        return fallback;
    }
}

const duplicatePendingChecks = {};

function extractRecordDate(type, record) {
    if (!record) return null;
    switch (type) {
        case 'pakan':
        case 'kematian':
        case 'kesehatan':
            return record.tanggal;
        case 'monitoring':
            return record.waktu_pencatatan || record.tanggal;
        case 'berat':
            return record.tanggal_sampling || record.tanggal;
        default:
            return record.tanggal || record.waktu || record.created_at || null;
    }
}

function formatDateForDisplay(dateKey) {
    if (!dateKey) return '-';
    const dateObj = new Date(dateKey);
    if (Number.isNaN(dateObj.getTime())) return dateKey;
    return dateObj.toLocaleDateString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

function rememberDate(type, dateKey) {
    if (!type || !dateKey) return;
    if (!duplicateDateCache[type]) {
        duplicateDateCache[type] = new Set();
    }
    duplicateDateCache[type].add(dateKey);
}

function rememberRecords(type, records = []) {
    records.forEach((record) => {
        const dateKey = toDateKey(extractRecordDate(type, record));
        if (dateKey) rememberDate(type, dateKey);
    });
}

function getDuplicateCheckUrl(type, dateKey) {
    const endpoint = duplicateEndpointPaths[type];
    if (!endpoint || !dateKey) return null;
    return `${baseUrl}/admin/pembesaran/${pembesaranId}/${endpoint}?tanggal=${encodeURIComponent(dateKey)}`;
}

async function fetchDuplicateStatus(type, dateKey) {
    if (!type || !dateKey) return false;
    const cacheKey = `${type}-${dateKey}`;
    if (duplicatePendingChecks[cacheKey]) {
        return duplicatePendingChecks[cacheKey];
    }

    const url = getDuplicateCheckUrl(type, dateKey);
    if (!url) return false;

    duplicatePendingChecks[cacheKey] = fetch(url, { credentials: 'same-origin' })
        .then((response) => response.json())
        .then((result) => {
            const hasData = result?.success && Array.isArray(result.data) && result.data.length > 0;
            if (hasData) {
                rememberDate(type, dateKey);
            }
            return hasData;
        })
        .catch((error) => {
            console.error('Failed to check duplicate status:', type, error);
            return false;
        })
        .finally(() => {
            delete duplicatePendingChecks[cacheKey];
        });

    return duplicatePendingChecks[cacheKey];
}

async function hasExistingRecord(type, dateKey) {
    if (!type || !dateKey) return false;
    if (duplicateDateCache[type]?.has(dateKey)) {
        return true;
    }
    return fetchDuplicateStatus(type, dateKey);
}

function getTodayDateKey() {
    const now = new Date();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    return `${now.getFullYear()}-${month}-${day}`;
}

async function confirmDuplicateSubmission(type, dateValue) {
    if (!type || !dateValue) return true;
    const dateKey = toDateKey(dateValue);
    if (!dateKey) return true;

    const exists = await hasExistingRecord(type, dateKey);
    if (!exists) return true;

    const displayDate = formatDateForDisplay(dateKey);
    const label = duplicateTypeLabels[type] || 'pencatatan';

    if (typeof Swal !== 'undefined') {
        const result = await Swal.fire({
            title: 'Catatan sudah ada di tanggal ini',
            html: `Anda sudah memiliki ${label} pada tanggal <strong>${displayDate}</strong>.<br>Menambahkan data baru akan mengganti catatan sebelumnya untuk tanggal tersebut. Lanjutkan?`,
            icon: 'warning',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Tambahkan',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#0d6efd',
            cancelButtonColor: '#6c757d'
        });
        return result.isConfirmed;
    }

    return window.confirm(`Data ${label} pada ${displayDate} sudah ada. Timpa catatan yang lama?`);
}

function registerLocalSubmission(type, record, fallbackDateValue) {
    let entryDate = extractRecordDate(type, record);
    if (!entryDate && fallbackDateValue) {
        entryDate = fallbackDateValue;
    }

    const entryKey = toDateKey(entryDate);
    if (!entryKey) return;
    rememberDate(type, entryKey);
}

// ========== FORMATTING HELPERS ==========
const rupiahFormatter = new Intl.NumberFormat('id-ID', {
    maximumFractionDigits: 0,
});

const kgFormatter = new Intl.NumberFormat('id-ID', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
});

const formatCurrency = (value = 0) => rupiahFormatter.format(Math.round(parseFloat(value) || 0));
const formatKg = (value = 0) => kgFormatter.format(parseFloat(value) || 0);
const formatDecimal = (value = 0, digits = 2) => (Number.isFinite(parseFloat(value)) ? parseFloat(value).toFixed(digits) : (0).toFixed(digits));

function updateTextValue(selector, formattedValue, numericValue) {
    const el = document.querySelector(selector);
    if (!el) return;
    el.textContent = formattedValue;
    if (!Number.isNaN(numericValue) && el.dataset) {
        el.dataset.value = numericValue;
    }
}

function getHealthCost() {
    const wrapper = document.getElementById('info-total-keseluruhan-wrapper');
    if (!wrapper) return 0;
    const raw = parseFloat(wrapper.dataset?.healthTotal || 0);
    return Number.isNaN(raw) ? 0 : raw;
}

function setHealthCost(value) {
    const wrapper = document.getElementById('info-total-keseluruhan-wrapper');
    if (!wrapper) return;
    const numeric = parseFloat(value) || 0;
    wrapper.dataset.healthTotal = numeric;
}

function recalcTotalBiayaKeseluruhan() {
    const totalPakan = parseFloat(document.querySelector('#info-total-biaya-pakan')?.dataset?.value || 0);
    const healthCost = getHealthCost();
    const grandTotal = (Number.isNaN(totalPakan) ? 0 : totalPakan) + healthCost;
    updateTextValue('#info-total-biaya-keseluruhan', formatCurrency(grandTotal), grandTotal);
}

function getNumericFromElement(selector) {
    const el = document.querySelector(selector);
    if (!el) return 0;
    const datasetValue = el.dataset?.value;
    if (datasetValue !== undefined) {
        const parsed = parseFloat(datasetValue);
        if (!Number.isNaN(parsed)) return parsed;
    }
    const rawText = (el.textContent || '').replace(/[^0-9,.-]/g, '');
    if (!rawText) return 0;
    const normalized = rawText.replace(/\./g, '').replace(',', '.');
    const numeric = parseFloat(normalized);
    return Number.isNaN(numeric) ? 0 : numeric;
}

function getRingkasanBiayaData() {
    const panel = document.getElementById('ringkasan-biaya-panel');
    const populasi = parseFloat(panel?.dataset?.populasi || 0) || 0;
    const batch = panel?.dataset?.batch || '-';
    const totalPakanKg = getNumericFromElement('#info-total-pakan-kg');
    const totalBiayaPakan = getNumericFromElement('#info-total-biaya-pakan');
    const totalBiayaKesehatan = getNumericFromElement('#info-total-biaya-kesehatan');
    const totalKeseluruhan = getNumericFromElement('#info-total-biaya-keseluruhan');
    const biayaPerEkor = populasi > 0 ? totalKeseluruhan / populasi : 0;

    return {
        batch,
        populasi,
        totalPakanKg,
        totalBiayaPakan,
        totalBiayaKesehatan,
        totalKeseluruhan,
        biayaPerEkor
    };
}

function downloadCsv(filename, rows) {
    const csvContent = rows
        .map((row) => row.map((value) => `"${String(value ?? '').replace(/"/g, '""')}"`).join(','))
        .join('\n');

    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = filename;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
}

function updatePakanSummaries(summary) {
    if (!summary) return;
    const totalKg = parseFloat(summary.total_konsumsi_kg ?? 0) || 0;
    const totalCost = parseFloat(summary.total_biaya ?? 0) || 0;
    updateTextValue('#kai-total-biaya-pakan', formatCurrency(totalCost), totalCost);
    updateTextValue('#info-total-biaya-pakan', formatCurrency(totalCost), totalCost);
    updateTextValue('#info-total-pakan-kg', formatKg(totalKg), totalKg);
    recalcTotalBiayaKeseluruhan();
}

function updateKesehatanSummary(totalBiaya) {
    const numeric = parseFloat(totalBiaya ?? 0) || 0;
    updateTextValue('#info-total-biaya-kesehatan', formatCurrency(numeric), numeric);
    setHealthCost(numeric);
    recalcTotalBiayaKeseluruhan();
}


// ========== HELPER FUNCTIONS ==========

function getRecorderName(record) {
    const user = record?.pengguna;
    if (!user) return '-';
    return user.nama_pengguna || user.username || user.nama || user.name || '-';
}

const toastElementId = 'pembesaran-toast';
function showToast(message, type = 'success') {
    const toast = document.getElementById(toastElementId);
    if (!toast) {
        console.warn('Toast container not found');
        return;
    }

    const backgrounds = {
        success: '#22c55e',
        error: '#ef4444',
        warning: '#f97316'
    };

    if (toast.dataset.timeoutId) {
        clearTimeout(parseInt(toast.dataset.timeoutId, 10));
    }

    toast.textContent = message;
    toast.style.background = backgrounds[type] || backgrounds.success;
    toast.style.display = 'block';
    toast.classList.add('bolopa-tabel-show');

    const timeoutId = window.setTimeout(() => {
        toast.classList.remove('bolopa-tabel-show');
        toast.style.display = 'none';
        toast.dataset.timeoutId = '';
    }, 3200);

    toast.dataset.timeoutId = timeoutId.toString();
}

async function submitAjax(url, data) {
    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin', // CRITICAL: Send cookies with request
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        return response.ok ? result : { success: false, message: result.message || 'Terjadi kesalahan' };
    } catch (error) {
        console.error('AJAX Error:', error);
        return { success: false, message: 'Gagal menghubungi server' };
    }
}

// ========== FORM PAKAN ==========

const pakanForm = document.querySelector('form[aria-label="Form pencatatan pakan harian"]');
if (pakanForm) {
    // Auto-calculate total biaya & auto-fill harga from dropdown
    const feedSelect = pakanForm.querySelector('select[name="feed_item_id"]');
    const jumlahKg = pakanForm.querySelector('input[name="jumlah_kg"]');
    const hargaPerKg = pakanForm.querySelector('input[name="harga_per_kg"]');
    const totalBiaya = pakanForm.querySelector('input[name="total_biaya"]');
    const feedUnitLabel = document.getElementById('feed-unit-label');

    // Auto-fill harga when pakan selected
    feedSelect?.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const harga = selectedOption?.getAttribute('data-price') || 0;
        const unit = selectedOption?.getAttribute('data-unit') || feedUnitLabel?.dataset?.defaultUnit || 'kg';
        if (hargaPerKg) hargaPerKg.value = harga;
        if (feedUnitLabel) feedUnitLabel.textContent = unit;
        updateTotal();
    });

    function updateTotal() {
        const kg = parseFloat(jumlahKg?.value || 0);
        const harga = parseFloat(hargaPerKg?.value || 0);
        if (totalBiaya) {
            const total = kg * harga;
            totalBiaya.value = 'Rp ' + (total || 0).toLocaleString('id-ID');
        }
    }
    jumlahKg?.addEventListener('input', updateTotal);
    hargaPerKg?.addEventListener('input', updateTotal);

    // Submit handler
    pakanForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const tanggalPakan = formData.get('tanggal');
        if (!validateBatchDate(tanggalPakan, 'Tanggal pakan')) return;
        const allowSubmit = await confirmDuplicateSubmission('pakan', tanggalPakan);
        if (!allowSubmit) return;
        const feedItemId = formData.get('feed_item_id');
        const payload = {
            tanggal: tanggalPakan,
            feed_item_id: feedItemId ? parseInt(feedItemId) : null,
            jumlah_kg: parseFloat(formData.get('jumlah_kg')),
            sisa_pakan_kg: formData.get('sisa_pakan_kg') ? parseFloat(formData.get('sisa_pakan_kg')) : null,
            harga_per_kg: formData.get('harga_per_kg') ? parseFloat(formData.get('harga_per_kg')) : null,
        };

        const result = await submitAjax(`${baseUrl}/admin/pembesaran/${pembesaranId}/pakan`, payload);

        if (result.success) {
            showToast(result.message || 'Data pakan berhasil disimpan');
            this.reset();
            if (feedUnitLabel) feedUnitLabel.textContent = feedUnitLabel.dataset?.defaultUnit || 'kg';
            updateTotal();
            registerLocalSubmission('pakan', result.data, tanggalPakan);
            loadPakanData(); // Reload chart
        } else {
            showToast(result.message, 'error');
        }
    });
}

// ========== FORM KEMATIAN ==========

const kematianForm = document.querySelector('form[aria-label="Form pencatatan kematian harian"]');
if (kematianForm) {
    kematianForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const tanggalKematian = formData.get('tanggal');
        if (!validateBatchDate(tanggalKematian, 'Tanggal kematian')) return;
        const allowSubmit = await confirmDuplicateSubmission('kematian', tanggalKematian);
        if (!allowSubmit) return;
        const result = await submitAjax(`${baseUrl}/admin/pembesaran/${pembesaranId}/kematian`, {
            tanggal: tanggalKematian,
            jumlah: parseInt(formData.get('jumlah_ekor')),
            penyebab: formData.get('penyebab'),
            keterangan: formData.get('catatan') || ''
        });
        
        if (result.success) {
            showToast(result.message || 'Data kematian berhasil disimpan');
            this.reset();
            registerLocalSubmission('kematian', result.data, tanggalKematian);
            
            // DSS Alert untuk mortalitas tinggi
            if (result.is_high_mortality && result.alert) {
                setTimeout(() => showToast(result.alert, 'warning'), 500);
            }
            
            // Update metrics di halaman
            updateMetric('.bolopa-kai-red .bolopa-kai-value', 
                `${result.mortalitas.toFixed(2)}<small style="font-size:0.45em;">%</small>`);
            updateMetric('.bolopa-kai-red .bolopa-kai-label', 
                `Mortalitas (${result.total_mati} ekor)`);
            
            loadKematianData(); // Reload chart
        } else {
            showToast(result.message, 'error');
        }
    });
}

// ========== FORM MONITORING LINGKUNGAN ==========

const monitoringForm = document.querySelector('form[aria-label="Form monitoring lingkungan"]');
if (monitoringForm) {
    monitoringForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const tanggal = formData.get('tanggal');
        const waktu = formData.get('waktu');
        if (!validateBatchDate(tanggal, 'Tanggal monitoring')) return;
        const allowSubmit = await confirmDuplicateSubmission('monitoring', tanggal);
        if (!allowSubmit) return;
        
        // Gabungkan tanggal dan waktu menjadi datetime
        const waktu_pencatatan = `${tanggal} ${waktu}:00`;
        
        const result = await submitAjax(`${baseUrl}/admin/pembesaran/${pembesaranId}/monitoring`, {
            waktu_pencatatan: waktu_pencatatan,
            suhu: parseFloat(formData.get('suhu')),
            kelembaban: parseFloat(formData.get('kelembaban')),
            intensitas_cahaya: formData.get('intensitas_cahaya') ? parseFloat(formData.get('intensitas_cahaya')) : null,
            kondisi_ventilasi: formData.get('kondisi_ventilasi') || null,
            catatan: formData.get('catatan') || null
        });
        
        if (result.success) {
            showToast(result.message || 'Data monitoring berhasil disimpan');
            this.reset();
            registerLocalSubmission('monitoring', result.data, tanggal);
            
            // DSS Alert untuk lingkungan tidak ideal
            if (result.dss_alert) {
                setTimeout(() => showToast(result.dss_alert, 'warning'), 500);
            }
            
            loadMonitoringData(); // Reload chart
        } else {
            showToast(result.message, 'error');
        }
    });
}

// ========== FORM KESEHATAN & VAKSINASI ==========

const kesehatanForm = document.querySelector('form[aria-label="Form kesehatan & vaksinasi"]');
if (kesehatanForm) {
    kesehatanForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const tanggalKesehatan = formData.get('tanggal');
        if (!validateBatchDate(tanggalKesehatan, 'Tanggal kesehatan')) return;
        const allowSubmit = await confirmDuplicateSubmission('kesehatan', tanggalKesehatan);
        if (!allowSubmit) return;
        const result = await submitAjax(`${baseUrl}/admin/pembesaran/${pembesaranId}/kesehatan`, {
            tanggal: tanggalKesehatan,
            tipe_kegiatan: formData.get('tipe_kegiatan'),
            nama_vaksin_obat: formData.get('nama_vaksin_obat'),
            jumlah_burung: parseInt(formData.get('jumlah_burung')),
            catatan: formData.get('catatan') || null,
            biaya: formData.get('biaya') ? parseFloat(formData.get('biaya')) : null,
            petugas: formData.get('petugas') || null
        });
        if (result.success) {
            showToast(result.message || 'Data kesehatan berhasil disimpan');
            this.reset();
            registerLocalSubmission('kesehatan', result.data, tanggalKesehatan);
            loadKesehatanData();
        } else {
            showToast(result.message, 'error');
        }
    });
}

// ========== FORM BERAT RATA-RATA ==========

const beratForm = document.querySelector('form[aria-label="Form pencatatan mingguan - sampling berat"]');
if (beratForm) {
    beratForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const samplingDateKey = getTodayDateKey();
        const allowSubmit = await confirmDuplicateSubmission('berat', samplingDateKey);
        if (!allowSubmit) return;
        const result = await submitAjax(`${baseUrl}/admin/pembesaran/${pembesaranId}/berat`, {
            umur_hari: parseInt(formData.get('umur_hari')),
            berat_rata_rata: parseFloat(formData.get('berat_rata_rata'))
        });
        
        if (result.success) {
            showToast(result.message || 'Data berat berhasil disimpan');
            this.reset();
            registerLocalSubmission('berat', result.sampling, samplingDateKey);
            
            // Show status berat
            if (result.status && result.status.message) {
                setTimeout(() => {
                    const badgeClass = result.status.badge === 'warning' ? 'warning' : 'success';
                    showToast(`${result.status.message}`, badgeClass);
                }, 500);
            }
            
            // Update metric if available
            if (result.data && result.data.berat_rata_rata) {
                updateMetric('.bolopa-kai-green .bolopa-kai-value', 
                    `${Math.round(result.data.berat_rata_rata)}g`);
            }
            
            loadBeratData(); // Reload chart
        } else {
            showToast(result.message, 'error');
        }
    });
}

// ========== FORM LAPORAN HARIAN ==========

// Auto-generate catatan button handler
const btnGenerateCatatan = document.getElementById('btn-generate-catatan');
if (btnGenerateCatatan) {
    btnGenerateCatatan.addEventListener('click', async function() {
        const tanggalLaporan = document.getElementById('tanggal_laporan').value;
        if (!tanggalLaporan) {
            showToast('Pilih tanggal laporan terlebih dahulu', 'warning');
            return;
        }
        if (!validateBatchDate(tanggalLaporan, 'Tanggal laporan')) return;
        
        // Show loading state
        const btnText = this.innerHTML;
        this.disabled = true;
        this.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Generating...';
        
        try {
            // Check whether a laporan for this date already exists (to warn user)
            const laporanListResp = await fetch(`${baseUrl}/admin/pembesaran/${pembesaranId}/laporan-harian/list`, {
                credentials: 'same-origin',
                cache: 'no-store'
            });
            const laporanList = await laporanListResp.json();
                const keyTanggal = toDateKey(tanggalLaporan);
                const existsForDate = (laporanList.data || []).some(l => toDateKey(l.tanggal) === keyTanggal);
            if (existsForDate) {
                const confirmAdd = await Swal.fire({
                    title: 'Laporan sudah ada untuk tanggal ini',
                    html: 'Terdapat laporan untuk tanggal yang Anda pilih. Apakah Anda yakin ingin menambahkan laporan baru untuk tanggal yang sama?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, tambahkan',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#0d6efd',
                    cancelButtonColor: '#6c757d'
                });

                if (!confirmAdd.isConfirmed) {
                    this.disabled = false;
                    this.innerHTML = btnText;
                    return;
                }
            }
            // Fetch datasets for selected date, fallback to server when cache misses
            const fetchRecordsForSection = (type, dateExtractor) =>
                getRecordsForDate(type, keyTanggal, dateExtractor);

            const [
                pakanHariIni,
                kematianHariIni,
                kesehatanHariIni,
                monitoringHariIni,
                beratHariIni
            ] = await Promise.all([
                fetchRecordsForSection('pakan', (row) => row.tanggal),
                fetchRecordsForSection('kematian', (row) => row.tanggal),
                fetchRecordsForSection('kesehatan', (row) => row.tanggal),
                fetchRecordsForSection('monitoring', (row) => row.waktu_pencatatan || row.tanggal),
                fetchRecordsForSection('berat', (row) => row.tanggal_sampling || row.tanggal)
            ]);
            
            const dateLabel = new Date(tanggalLaporan).toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });

            const headerDivider = '='.repeat(60);
            const sectionDivider = '-'.repeat(60);

            const formatRupiah = (value) => {
                const numeric = parseFloat(value);
                if (Number.isNaN(numeric)) return '-';
                return `Rp ${formatCurrency(numeric)}`;
            };

            const formatDate = (value, withTime = false) => {
                const dateObj = new Date(value);
                if (Number.isNaN(dateObj.getTime())) return '-';
                if (withTime) {
                    return dateObj.toLocaleString('id-ID', {
                        day: '2-digit',
                        month: 'short',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                }
                return dateObj.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric'
                });
            };

            const cleanText = (value) => {
                if (!value) return '-';
                return String(value).replace(/\s+/g, ' ').trim();
            };

            const addSection = (linesAcc, title, records, renderer, emptyMessage) => {
                const titleLine = `${title.toUpperCase()}${records.length ? ` (${records.length} catatan)` : ''}`;
                linesAcc.push(titleLine);
                linesAcc.push(sectionDivider);

                if (!records.length) {
                    linesAcc.push(`• ${emptyMessage}`);
                    linesAcc.push('');
                    return;
                }

                records.forEach((record, index) => {
                    const rendered = renderer(record, index);
                    const renderedLines = Array.isArray(rendered) ? rendered : [rendered];
                    renderedLines
                        .filter((line) => typeof line === 'string' && line.trim().length > 0)
                        .forEach((line, lineIdx) => {
                            const prefix = lineIdx === 0
                                ? `${String(index + 1).padStart(2, '0')}. `
                                : '    • ';
                            linesAcc.push(prefix + line);
                        });
                    linesAcc.push('');
                });
            };

            const lines = [
                `LAPORAN HARIAN (${dateLabel})`,
                headerDivider,
                ''
            ];

            addSection(lines, 'Pakan', pakanHariIni, (row) => {
                const feedLabel = row.feed_item?.name || row.stok_pakan?.nama_pakan || row.jenis_pakan || 'Pakan';
                const konsumsi = Number.isFinite(parseFloat(row.jumlah_kg)) ? `${formatDecimal(row.jumlah_kg)} kg` : '-';
                const sisaKg = row.sisa_pakan_kg;
                const legacyKarung = row.jumlah_karung ?? row.jumlah_karung_sisa;
                let sisaDisplay = '-';
                if (sisaKg !== null && sisaKg !== undefined && !Number.isNaN(parseFloat(sisaKg))) {
                    sisaDisplay = `${formatDecimal(sisaKg)} kg`;
                } else if (legacyKarung !== null && legacyKarung !== undefined && legacyKarung !== '') {
                    sisaDisplay = `${parseInt(legacyKarung, 10) || 0} karung`;
                }
                const biayaDisplay = formatRupiah(row.total_biaya ?? row.biaya);
                const pencatat = getRecorderName(row);
                const catatan = cleanText(row.catatan);
                return [
                    `${feedLabel}`,
                    `Terpakai ${konsumsi} | Sisa ${sisaDisplay} | Biaya ${biayaDisplay} | Pencatat ${pencatat}`,
                    catatan && catatan !== '-' ? `Catatan: ${catatan}` : null
                ];
            }, 'Belum ada catatan pakan');

            addSection(lines, 'Kematian', kematianHariIni, (row) => {
                const tanggal = formatDate(row.tanggal);
                const jumlah = parseInt(row.jumlah, 10);
                const penyebab = cleanText(row.penyebab || 'Tidak diketahui');
                const catatan = cleanText(row.keterangan || row.catatan);
                return [
                    `${tanggal} — ${Number.isNaN(jumlah) ? '-' : `${jumlah} ekor`}`,
                    `Penyebab ${penyebab}`,
                    catatan && catatan !== '-' ? `Catatan: ${catatan}` : null
                ];
            }, 'Tidak ada kejadian kematian');

            addSection(lines, 'Kesehatan/Vaksinasi', kesehatanHariIni, (row) => {
                const tipe = cleanText(row.tipe_kegiatan?.replace(/_/g, ' ') || 'Kegiatan');
                const namaObat = cleanText(row.nama_vaksin_obat);
                const jumlah = row.jumlah_burung ? `${row.jumlah_burung} ekor` : '-';
                const biayaDisplay = formatRupiah(row.biaya);
                const petugas = cleanText(row.petugas || getRecorderName(row));
                const catatan = cleanText(row.catatan);
                return [
                    `${tipe} – ${namaObat}`,
                    `${jumlah} | Biaya ${biayaDisplay} | Petugas ${petugas}`,
                    catatan && catatan !== '-' ? `Catatan: ${catatan}` : null
                ];
            }, 'Tidak ada kegiatan kesehatan');

            addSection(lines, 'Monitoring Lingkungan', monitoringHariIni, (row) => {
                const waktu = formatDate(row.waktu_pencatatan || row.tanggal, true);
                const suhu = Number.isNaN(parseFloat(row.suhu)) ? '-' : `${formatDecimal(row.suhu, 1)}°C`;
                const kelembaban = Number.isNaN(parseFloat(row.kelembaban)) ? '-' : `${formatDecimal(row.kelembaban, 1)}%`;
                const ventilasi = cleanText(row.kondisi_ventilasi || row.ventilasi);
                const catatan = cleanText(row.catatan);
                return [
                    `${waktu}`,
                    `Suhu ${suhu} | Kelembaban ${kelembaban} | Ventilasi ${ventilasi}`,
                    catatan && catatan !== '-' ? `Catatan: ${catatan}` : null
                ];
            }, 'Belum ada monitoring');

            addSection(lines, 'Sampling Berat', beratHariIni, (row) => {
                const tanggal = formatDate(row.tanggal_sampling || row.tanggal);
                const umur = row.umur_hari ? `${row.umur_hari} hari` : '-';
                const beratAvg = Number.isNaN(parseFloat(row.berat_rata_rata)) ? '-' : `${formatDecimal(row.berat_rata_rata, 1)} gram`;
                const sampel = row.jumlah_sampel ? `${row.jumlah_sampel} ekor` : '-';
                const catatan = cleanText(row.catatan);
                return [
                    `${tanggal} — Umur ${umur}`,
                    `Rata-rata ${beratAvg} | Sampel ${sampel}`,
                    catatan && catatan !== '-' ? `Catatan: ${catatan}` : null
                ];
            }, 'Belum ada sampling berat');

            lines.push('CATATAN TAMBAHAN');
            lines.push(sectionDivider);
            lines.push('• Tambahkan poin penting lainnya di bawah ini.');
            lines.push('- ');

            document.getElementById('catatan_laporan').value = lines.join('\n');
            showToast('Catatan berhasil di-generate! Silakan sesuaikan jika perlu.', 'success');
            
        } catch (error) {
            console.error('Error generating catatan:', error);
            showToast('Gagal generate catatan: ' + error.message, 'error');
        } finally {
            // Restore button
            this.disabled = false;
            this.innerHTML = btnText;
        }
    });
}

const laporanForm = document.querySelector('form[aria-label="Form laporan harian"]');
if (laporanForm) {
    laporanForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const tanggalLaporan = formData.get('tanggal_laporan');
        if (!validateBatchDate(tanggalLaporan, 'Tanggal laporan')) return;
        const catatan = formData.get('catatan');
        
        // Validasi catatan tidak boleh kosong
        if (!catatan || catatan.trim().length === 0) {
            showToast('Catatan laporan tidak boleh kosong. Klik "Generate Catatan" atau isi manual.', 'warning');
            return;
        }
        
        // Check if laporan already exists for this date
        try {
            const keyTanggal = toDateKey(tanggalLaporan);
                let laporanListData = window._laporanCache;
                if (!laporanListData) {
                    const resp = await fetch(`${baseUrl}/admin/pembesaran/${pembesaranId}/laporan-harian/list`, { credentials: 'same-origin' });
                    const listResult = await resp.json();
                    laporanListData = listResult.data || [];
                    window._laporanCache = laporanListData;
                }
                const exists = (laporanListData || []).some(l => toDateKey(l.tanggal) === keyTanggal);

            if (exists) {
                // Inform user that laporan for this date already exists
                await Swal.fire({
                    icon: 'info',
                    title: 'Sudah tercatat',
                    html: 'Anda sudah melakukan pencatatan laporan harian untuk tanggal ini. Jika ingin mengubah catatan, buka laporan tersebut melalui tombol <strong>Detail</strong>.',
                    confirmButtonText: 'Tutup',
                    confirmButtonColor: '#0d6efd'
                });
                return;
            }

            // Proceed to submit if not exists
            const result = await submitAjax(`${baseUrl}/admin/pembesaran/${pembesaranId}/laporan-harian`, {
                tanggal: tanggalLaporan,
                catatan_kejadian: catatan
            });

            if (result.success) {
                // Server may respond with already_exists:true to indicate a pre-existing record
                if (result.already_exists) {
                    // Inform user it's already recorded and show message from server
                    await Swal.fire({
                        icon: 'info',
                        title: 'Sudah tercatat',
                        html: result.message || 'Anda sudah melakukan pencatatan laporan harian untuk tanggal ini.',
                        confirmButtonText: 'Tutup',
                        confirmButtonColor: '#0d6efd'
                    });
                    // Refresh client-side cache and history to reflect server state
                    window._laporanCache = window._laporanCache || [];
                    loadLaporanData();
                } else {
                    showToast('Laporan harian berhasil disimpan');
                    this.reset();
                    loadLaporanData(); // Reload history
                }
            } else {
                showToast(result.message, 'error');
            }
        } catch (err) {
            console.error('Error checking existing laporan:', err);
            showToast('Gagal memeriksa laporan tersedia. Silakan coba lagi.', 'error');
        }
    });
}

// ========== RINGKASAN BIAYA ACTIONS ==========

const ringkasanExportBtn = document.getElementById('btn-ringkasan-export');
if (ringkasanExportBtn) {
    ringkasanExportBtn.addEventListener('click', () => {
        const data = getRingkasanBiayaData();
        const now = new Date();
        const timestamp = now.toLocaleString('id-ID');
        const filenameDate = now.toISOString().slice(0, 10);
        const filename = `ringkasan-biaya-${data.batch || 'batch'}-${filenameDate}.csv`;

        const rows = [
            ['Item', 'Nilai', 'Satuan'],
            ['Batch', data.batch, ''],
            ['Tanggal Export', timestamp, ''],
            ['Populasi Saat Ini', data.populasi.toLocaleString('id-ID'), 'ekor'],
            ['Total Konsumsi Pakan', data.totalPakanKg.toFixed(2), 'kg'],
            ['Total Biaya Pakan', `Rp ${formatCurrency(data.totalBiayaPakan)}`, '-'],
            ['Biaya Kesehatan & Vaksinasi', `Rp ${formatCurrency(data.totalBiayaKesehatan)}`, '-'],
            ['Total Keseluruhan', `Rp ${formatCurrency(data.totalKeseluruhan)}`, '-'],
            ['Biaya per Ekor', `Rp ${formatCurrency(Math.round(data.biayaPerEkor))}`, '-']
        ];

        downloadCsv(filename, rows);
        showToast('Ringkasan biaya berhasil diekspor ke CSV');
    });
}

// ========== DATA LOADERS ==========

async function loadPakanData() {
    try {
        console.log('📊 Loading pakan data...');
        const response = await fetch(`${baseUrl}/admin/pembesaran/${pembesaranId}/pakan/list`, {
            credentials: 'same-origin' // Send cookies
        });
        console.log('📊 Pakan response status:', response.status);
        const result = await response.json();
        console.log('📊 Pakan result:', result);
        
        if (result.success && result.data) {
            console.log('📊 Rendering pakan data, count:', result.data.length);
            renderPakanChart(result.data);
            renderPakanHistory(result.data);
            setDatasetCache('pakan', result.data);
        } else {
            console.warn('📊 Pakan data not successful or empty');
            // Still try to render with empty state
            renderPakanHistory([]);
            setDatasetCache('pakan', []);
        }

        if (result.success && Array.isArray(result.data)) {
            rememberRecords('pakan', result.data);
        }

        if (result.summary) {
            updatePakanSummaries(result.summary);
        }
    } catch (error) {
        console.error('❌ Error loading pakan data:', error);
        renderPakanHistory([]);
    }
}

async function loadKematianData() {
    try {
        console.log('📊 Loading kematian data...');
        const response = await fetch(`${baseUrl}/admin/pembesaran/${pembesaranId}/kematian/list`, {
            credentials: 'same-origin'
        });
        console.log('📊 Kematian response status:', response.status);
        const result = await response.json();
        console.log('📊 Kematian result:', result);
        
        if (result.success && result.data) {
            console.log('📊 Rendering kematian data, count:', result.data.length);
            renderKematianChart(result.data, result.mortalitas);
            renderKematianHistory(result.data);
            setDatasetCache('kematian', result.data);
        } else {
            console.warn('📊 Kematian data not successful or empty');
            renderKematianHistory([]);
            setDatasetCache('kematian', []);
        }

        if (result.success && Array.isArray(result.data)) {
            rememberRecords('kematian', result.data);
        }
    } catch (error) {
        console.error('❌ Error loading kematian data:', error);
        renderKematianHistory([]);
    }
}

async function loadLaporanData() {
    try {
        console.log('📊 Loading laporan data...');
        const response = await fetch(`${baseUrl}/admin/pembesaran/${pembesaranId}/laporan-harian/list`, {
            credentials: 'same-origin'
        });
        console.log('📊 Laporan response status:', response.status);
        const result = await response.json();
        console.log('📊 Laporan result:', result);
        
        if (result.success && result.data) {
            console.log('📊 Rendering laporan data, count:', result.data.length);
            // Cache laporan list for quick client-side checks (avoid duplicate submissions)
            window._laporanCache = result.data || [];
            renderLaporanHistory(result.data);
        } else {
            console.warn('📊 Laporan data not successful or empty');
            window._laporanCache = [];
            renderLaporanHistory([]);
        }
    } catch (error) {
        console.error('❌ Error loading laporan data:', error);
        renderLaporanHistory([]);
    }
}

async function loadMonitoringData() {
    try {
        console.log('📊 Loading monitoring data...');
        const response = await fetch(`${baseUrl}/admin/pembesaran/${pembesaranId}/monitoring/list`, {
            credentials: 'same-origin'
        });
        console.log('📊 Monitoring response status:', response.status);
        const result = await response.json();
        console.log('📊 Monitoring result:', result);
        
        if (result.success && result.data) {
            console.log('📊 Rendering monitoring data, count:', result.data.length);
            renderMonitoringChart(result.data);
            renderMonitoringHistory(result.data);
            setDatasetCache('monitoring', result.data);
        } else {
            console.warn('📊 Monitoring data not successful or empty');
            renderMonitoringHistory([]);
            setDatasetCache('monitoring', []);
        }

        if (result.success && Array.isArray(result.data)) {
            rememberRecords('monitoring', result.data);
        }
    } catch (error) {
        console.error('❌ Error loading monitoring data:', error);
        renderMonitoringHistory([]);
    }
}

async function loadBeratData() {
    // Load dari endpoint berat/list yang menyimpan history sampling
    try {
        console.log('📊 Loading berat data from berat sampling...');
        const response = await fetch(`${baseUrl}/admin/pembesaran/${pembesaranId}/berat/list`, {
            credentials: 'same-origin'
        });
        const result = await response.json();
        
        if (result.success && result.data && result.data.length > 0) {
            rememberRecords('berat', result.data);
            renderBeratChart(result.data);
            renderBeratHistory(result.data);
            setDatasetCache('berat', result.data);
        } else {
            document.getElementById('weightError').style.display = 'block';
            document.getElementById('weightAnalysis').style.display = 'none';
            renderBeratHistory([]);
            setDatasetCache('berat', []);
        }
    } catch (error) {
        console.error('❌ Error loading berat data:', error);
        document.getElementById('weightError').style.display = 'block';
        document.getElementById('weightAnalysis').style.display = 'none';
        renderBeratHistory([]);
    }
}

async function loadKesehatanData() {
    try {
        const response = await fetch(`${baseUrl}/admin/pembesaran/${pembesaranId}/kesehatan/list`, {
            credentials: 'same-origin' // Send cookies
        });
        const result = await response.json();
        
        if (result.success) {
            rememberRecords('kesehatan', result.data || []);
            renderKesehatanHistory(result.data || []);
            setDatasetCache('kesehatan', result.data || []);
            if (Object.prototype.hasOwnProperty.call(result, 'total_biaya')) {
                updateKesehatanSummary(result.total_biaya);
            }
        } else {
            renderKesehatanHistory([]);
            setDatasetCache('kesehatan', []);
        }
    } catch (error) {
        console.error('Error loading kesehatan data:', error);
    }
}

// ========== CHART RENDERERS ==========

function renderPakanChart(data) {
    if (!data || !data.length) {
        document.getElementById('feedError').style.display = 'block';
        document.getElementById('feedAnalysis').style.display = 'none';
        return;
    }
    
    document.getElementById('feedError').style.display = 'none';
    document.getElementById('feedAnalysis').style.display = 'block';
    
    const categories = data.map(d => new Date(d.tanggal).toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit' }));
    const series = [{ name: 'Pakan (kg)', data: data.map(d => parseFloat(d.jumlah_kg)) }];
    
    if (window.feedChart) {
        window.feedChart.updateSeries(series);
        window.feedChart.updateOptions({ xaxis: { categories } });
    } else {
        window.feedChart = new ApexCharts(document.querySelector('#chartFeed'), {
            chart: { type: 'area', height: 240, toolbar: { show: false } },
            series: series,
            xaxis: { categories: categories, labels: { rotate: -45 } },
            colors: ['#0077b6'],
            stroke: { curve: 'smooth', width: 2 },
            fill: { type: 'gradient', gradient: { opacityFrom: 0.6, opacityTo: 0.1 } },
            dataLabels: { enabled: false },
            tooltip: { y: { formatter: val => val.toFixed(2) + ' kg' } }
        });
        window.feedChart.render();
    }
    
    const total = series[0].data.reduce((a, b) => a + b, 0);
    const avg = total / series[0].data.length;
    document.getElementById('feedAnalysis').textContent = 
        `Total: ${total.toFixed(2)} kg | Rata-rata: ${avg.toFixed(2)} kg/hari`;
}

function renderKematianChart(data, mortalitasPct) {
    if (!data || !data.length) {
        document.getElementById('mortalityError').style.display = 'block';
        document.getElementById('mortalityAnalysis').style.display = 'none';
        return;
    }
    
    document.getElementById('mortalityError').style.display = 'none';
    document.getElementById('mortalityAnalysis').style.display = 'block';
    
    const categories = data.map(d => new Date(d.tanggal).toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit' }));
    // Hitung mortalitas kumulatif
    let kumulatif = 0;
    const mortalitasData = data.map((d) => {
        kumulatif += parseInt(d.jumlah);
        return kumulatif;
    });
    
    const series = [{ name: 'Kematian Kumulatif (ekor)', data: mortalitasData }];
    
    if (window.mortalityChart) {
        window.mortalityChart.updateSeries(series);
        window.mortalityChart.updateOptions({ xaxis: { categories } });
    } else {
        window.mortalityChart = new ApexCharts(document.querySelector('#chartMortality'), {
            chart: { type: 'line', height: 240, toolbar: { show: false } },
            series: series,
            xaxis: { categories: categories, labels: { rotate: -45 } },
            colors: ['#ef4444'],
            stroke: { curve: 'smooth', width: 3 },
            dataLabels: { enabled: false },
            tooltip: { y: { formatter: val => val + ' ekor' } },
            yaxis: { 
                title: { text: 'Kematian (ekor)' },
                min: 0
            },
            markers: {
                size: 4,
                colors: ['#ef4444'],
                strokeColors: '#fff',
                strokeWidth: 2
            }
        });
        window.mortalityChart.render();
    }
    
    const totalDead = data.reduce((sum, d) => sum + parseInt(d.jumlah), 0);
    document.getElementById('mortalityAnalysis').textContent = 
        `Total kematian: ${totalDead} ekor | Mortalitas: ${mortalitasPct.toFixed(2)}%`;
}

function renderMonitoringChart(data) {
    if (!data || !data.length) {
        document.getElementById('envError').style.display = 'block';
        document.getElementById('envAnalysis').style.display = 'none';
        return;
    }
    
    document.getElementById('envError').style.display = 'none';
    document.getElementById('envAnalysis').style.display = 'block';
    
    const categories = data.map(d => new Date(d.waktu_pencatatan).toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit' }));
    const tempData = data.map(d => parseFloat(d.suhu));
    const humData = data.map(d => parseFloat(d.kelembaban));
    
    if (window.envChart) {
        window.envChart.updateSeries([
            { name: 'Suhu (°C)', data: tempData },
            { name: 'Kelembaban (%)', data: humData }
        ]);
        window.envChart.updateOptions({ xaxis: { categories } });
    } else {
        window.envChart = new ApexCharts(document.querySelector('#chartEnv'), {
            chart: { type: 'line', height: 240, toolbar: { show: false } },
            series: [
                { name: 'Suhu (°C)', data: tempData },
                { name: 'Kelembaban (%)', data: humData }
            ],
            xaxis: { categories: categories, labels: { rotate: -45 } },
            colors: ['#f59e0b', '#06b6d4'],
            stroke: { curve: 'smooth', width: 2 },
            dataLabels: { enabled: false },
            yaxis: [
                { title: { text: 'Suhu (°C)' }, min: 20, max: 35 },
                { opposite: true, title: { text: 'Kelembaban (%)' }, min: 40, max: 80 }
            ]
        });
        window.envChart.render();
    }
    
    const avgTemp = tempData.reduce((a, b) => a + b, 0) / tempData.length;
    const avgHum = humData.reduce((a, b) => a + b, 0) / humData.length;
    document.getElementById('envAnalysis').textContent = 
        `Rata-rata Suhu: ${avgTemp.toFixed(1)}°C | Kelembaban: ${avgHum.toFixed(1)}%`;
}

function renderBeratChart(data) {
    if (!data || !data.length) {
        document.getElementById('weightError').style.display = 'block';
        document.getElementById('weightAnalysis').style.display = 'none';
        return;
    }
    
    document.getElementById('weightError').style.display = 'none';
    document.getElementById('weightAnalysis').style.display = 'block';
    
    const categories = data.map(d => new Date(d.tanggal_sampling).toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit' }));
    const beratData = data.map(d => parseFloat(d.berat_rata_rata));
    
    if (window.weightChart) {
        window.weightChart.updateSeries([{ name: 'Berat Rata-rata (gram)', data: beratData }]);
        window.weightChart.updateOptions({ xaxis: { categories } });
    } else {
        window.weightChart = new ApexCharts(document.querySelector('#chartWeight'), {
            chart: { type: 'line', height: 240, toolbar: { show: false } },
            series: [{ name: 'Berat Rata-rata (gram)', data: beratData }],
            xaxis: { categories: categories, labels: { rotate: -45 } },
            colors: ['#10b981'],
            stroke: { curve: 'smooth', width: 3 },
            dataLabels: { enabled: false },
            tooltip: { y: { formatter: val => val.toFixed(0) + ' gram' } },
            yaxis: { 
                title: { text: 'Berat (gram)' },
                min: 0
            },
            markers: {
                size: 4,
                colors: ['#10b981'],
                strokeColors: '#fff',
                strokeWidth: 2,
                hover: { size: 6 }
            }
        });
        window.weightChart.render();
    }
    
    const latestWeight = beratData[beratData.length - 1];
    const avgWeight = beratData.reduce((a, b) => a + b, 0) / beratData.length;
    const growth = beratData.length > 1 ? latestWeight - beratData[0] : 0;
    
    document.getElementById('weightAnalysis').textContent = 
        `Berat terkini: ${latestWeight.toFixed(0)}g | Rata-rata: ${avgWeight.toFixed(0)}g | Pertumbuhan: ${growth.toFixed(0)}g`;
}

// ========== HISTORY RENDERERS ==========

function renderPakanHistory(data) {
    const container = document.getElementById('pakan-history-content');
    console.log('🔍 renderPakanHistory called, container found:', !!container, 'data count:', data?.length || 0);
    
    if (!container) {
        console.error('❌ Container #pakan-history-content not found!');
        return;
    }
    
    if (!data || data.length === 0) {
        console.log('ℹ️ No pakan data to display');
        container.innerHTML = '<p class="text-muted small mb-0">Belum ada data pakan</p>';
        return;
    }
    
    console.log('✅ Rendering', data.length, 'pakan records');
    console.log('📝 Sample record:', data[0]);
    
    container.innerHTML = `
        <table class="table table-sm table-hover mb-0">
            <thead>
                <tr>
                    <th style="width:18%" class="text-start">Tanggal</th>
                    <th style="width:24%" class="text-start">Jenis Pakan</th>
                    <th style="width:14%" class="text-end">Jumlah</th>
                    <th style="width:12%" class="text-end">Sisa Pakan</th>
                    <th style="width:14%" class="text-end">Biaya</th>
                    <th style="width:18%" class="text-start">Dicatat Oleh</th>
                </tr>
            </thead>
            <tbody>
                ${data.slice(0, 10).map(d => {
                    const feedLabel = d.feed_item?.name || d.stok_pakan?.nama_pakan || '-';
                    const hasSisaKg = d.sisa_pakan_kg !== null && d.sisa_pakan_kg !== undefined;
                    const sisaKg = hasSisaKg ? parseFloat(d.sisa_pakan_kg) : null;
                    const legacyKarung = d.jumlah_karung ?? d.jumlah_karung_sisa;
                    let sisaDisplay = '-';
                    if (hasSisaKg && !Number.isNaN(sisaKg) && sisaKg > 0) {
                        sisaDisplay = `${sisaKg.toFixed(2)} kg`;
                    } else if (legacyKarung !== null && legacyKarung !== undefined && legacyKarung !== '') {
                        sisaDisplay = `${parseInt(legacyKarung, 10) || 0} karung`;
                    }
                    const totalBiaya = parseFloat(d.total_biaya);
                    return `
                        <tr>
                            <td class="text-start">${new Date(d.tanggal).toLocaleDateString('id-ID', {day:'2-digit', month:'short'})}</td>
                            <td class="text-start"><small>${feedLabel}</small></td>
                            <td class="text-end">${parseFloat(d.jumlah_kg).toFixed(2)} kg</td>
                            <td class="text-end">${sisaDisplay}</td>
                            <td class="text-end"><small>Rp ${(Number.isNaN(totalBiaya) ? 0 : totalBiaya).toLocaleString('id-ID')}</small></td>
                            <td class="text-start"><small>${getRecorderName(d)}</small></td>
                        </tr>
                    `;
                }).join('')}
            </tbody>
        </table>
        ${data.length > 10 ? `<p class="text-muted small mt-2 mb-0 text-center">Menampilkan 10 dari ${data.length} data</p>` : ''}
    `;
}

function renderKematianHistory(data) {
    const container = document.getElementById('kematian-history-content');
    console.log('🔍 renderKematianHistory called, container found:', !!container, 'data count:', data?.length || 0);
    
    if (!container) {
        console.error('❌ Container #kematian-history-content not found!');
        return;
    }
    
    if (!data || data.length === 0) {
        console.log('ℹ️ No kematian data to display');
        container.innerHTML = '<p class="text-muted small mb-0">Belum ada data kematian</p>';
        return;
    }
    
    console.log('✅ Rendering', data.length, 'kematian records');
    
    container.innerHTML = `
        <table class="table table-sm table-hover mb-0">
            <thead>
                <tr>
                    <th style="width:20%" class="text-start">Tanggal</th>
                    <th style="width:15%" class="text-end">Jumlah</th>
                    <th style="width:20%" class="text-start">Penyebab</th>
                    <th style="width:25%" class="text-start">Catatan</th>
                    <th style="width:20%" class="text-start">Dicatat Oleh</th>
                </tr>
            </thead>
            <tbody>
                ${data.slice(0, 10).map(d => {
                    const note = d.keterangan || d.catatan || '-';
                    return `
                        <tr>
                            <td class="text-start">${new Date(d.tanggal).toLocaleDateString('id-ID', {day:'2-digit', month:'short'})}</td>
                            <td class="text-end">${d.jumlah} ekor</td>
                            <td class="text-start"><small>${d.penyebab || '-'}</small></td>
                            <td class="text-start"><small>${note}</small></td>
                            <td class="text-start"><small>${getRecorderName(d)}</small></td>
                        </tr>
                    `;
                }).join('')}
            </tbody>
        </table>
        ${data.length > 10 ? `<p class="text-muted small mt-2 mb-0 text-center">Menampilkan 10 dari ${data.length} data</p>` : ''}
    `;
}

function renderMonitoringHistory(data) {
    const container = document.getElementById('monitoring-history-content');
    console.log('🔍 renderMonitoringHistory called, container found:', !!container, 'data count:', data?.length || 0);
    
    if (!container) {
        console.error('❌ Container #monitoring-history-content not found!');
        return;
    }
    
    if (!data || data.length === 0) {
        console.log('ℹ️ No monitoring data to display');
        container.innerHTML = '<p class="text-muted small mb-0">Belum ada data monitoring</p>';
        return;
    }
    
    console.log('✅ Rendering', data.length, 'monitoring records');
    
    container.innerHTML = `
        <table class="table table-sm table-hover mb-0">
            <thead>
                <tr>
                    <th style="width:22%">Waktu</th>
                    <th style="width:12%" class="text-end">Suhu</th>
                    <th style="width:18%" class="text-end">Kelembaban</th>
                    <th style="width:18%">Ventilasi</th>
                    <th style="width:15%">Catatan</th>
                    <th style="width:15%">Dicatat Oleh</th>
                </tr>
            </thead>
            <tbody>
                ${data.slice(0, 10).map(d => {
                    const note = d.catatan || '-';
                    return `
                        <tr>
                            <td><small>${new Date(d.waktu_pencatatan || d.tanggal).toLocaleString('id-ID', {day:'2-digit', month:'short', hour:'2-digit', minute:'2-digit'})}</small></td>
                            <td class="text-end">${parseFloat(d.suhu).toFixed(1)}°C</td>
                            <td class="text-end">${parseFloat(d.kelembaban).toFixed(1)}%</td>
                            <td><small>${d.kondisi_ventilasi || '-'}</small></td>
                            <td><small>${note}</small></td>
                            <td><small>${getRecorderName(d)}</small></td>
                        </tr>
                    `;
                }).join('')}
            </tbody>
        </table>
        ${data.length > 10 ? `<p class="text-muted small mt-2 mb-0 text-center">Menampilkan 10 dari ${data.length} data</p>` : ''}
    `;
}

function renderLaporanHistory(data) {
    const container = document.getElementById('laporan-history-content');
    console.log('🔍 renderLaporanHistory called, container found:', !!container, 'data count:', data?.length || 0);
    
    if (!container) {
        console.error('❌ Container #laporan-history-content not found!');
        return;
    }
    
    if (!data || data.length === 0) {
        console.log('ℹ️ No laporan data to display');
        container.innerHTML = '<p class="text-muted small mb-0">Belum ada laporan harian</p>';
        return;
    }
    
    console.log('✅ Rendering', data.length, 'laporan records');
    console.log('📝 Sample laporan record:', data[0]);
    
    // Get base URL for detail page
    const baseUrl = window.vigazaConfig?.baseUrl || '';
    const pembesaranId = window.vigazaConfig?.pembesaranId || '';
    
    container.innerHTML = `
        <table class="table table-sm table-hover mb-0">
            <thead>
                <tr>
                    <th style="width:18%" class="text-start">Tanggal</th>
                    <th style="width:13%" class="text-end">Populasi</th>
                    <th style="width:13%" class="text-end">Pakan (kg)</th>
                    <th style="width:10%" class="text-end">Mati</th>
                    <th style="width:33%" class="text-start">Catatan</th>
                    <th style="width:13%" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                ${data.slice(0, 10).map((d) => `
                    <tr>
                        <td class="text-start"><small>${new Date(d.tanggal).toLocaleDateString('id-ID', {day:'2-digit', month:'short', year:'numeric'})}</small></td>
                        <td class="text-end">${parseInt(d.jumlah_burung || 0).toLocaleString('id-ID')}</td>
                        <td class="text-end">${parseFloat(d.konsumsi_pakan_kg || 0).toFixed(2)}</td>
                        <td class="text-end">${parseInt(d.jumlah_kematian || 0)}</td>
                        <td class="text-start"><small class="text-muted">${(d.catatan_kejadian || '-').substring(0, 35)}${(d.catatan_kejadian || '').length > 35 ? '...' : ''}</small></td>
                        <td class="text-center">
                            <a href="${baseUrl}/admin/pembesaran/${pembesaranId}/laporan-harian/${d.id}"
                               class="btn btn-sm btn-outline-primary text-decoration-none"
                               aria-label="Detail Laporan"
                               style="text-decoration:none">
                                Detail
                            </a>
                        </td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
        ${data.length > 10 ? `<p class="text-muted small mt-2 mb-0 text-center">Menampilkan 10 dari ${data.length} data</p>` : ''}
    `;
}

function renderKesehatanHistory(data) {
    console.log('🔍 renderKesehatanHistory called, container found:', !!document.getElementById('kesehatan-history-content'), 'data count:', data?.length);
    
    const container = document.getElementById('kesehatan-history-content');
    if (!container) {
        console.warn('⚠️ Kesehatan history container not found');
        return;
    }
    
    if (!data || data.length === 0) {
        container.innerHTML = '<p class="text-muted small mb-0">ℹ️ Belum ada riwayat kesehatan & vaksinasi</p>';
        return;
    }
    
    console.log('✅ Rendering', data.length, 'kesehatan records');
    console.log('📝 Sample kesehatan record:', data[0]);
    
    const rows = data.map(item => {
        const tanggal = new Date(item.tanggal).toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        });
        
        const tipeKegiatan = {
            'vaksinasi': '<span class="badge bg-primary">Vaksinasi</span>',
            'pengobatan': '<span class="badge bg-danger">Pengobatan</span>',
            'pemeriksaan_rutin': '<span class="badge bg-info">Pemeriksaan</span>',
            'karantina': '<span class="badge bg-warning">Karantina</span>'
        }[item.tipe_kegiatan] || `<span class="badge bg-secondary">${item.tipe_kegiatan}</span>`;
        
        const biaya = item.biaya ? `Rp ${parseInt(item.biaya).toLocaleString('id-ID')}` : '-';
        
        const catatanText = item.catatan ?? item.gejala;
        const catatanButton = catatanText ? 
            `<button type="button" class="btn btn-sm btn-info btn-detail-catatan" onclick="showKesehatanCatatanModal('${tanggal}', '${getRecorderName(item)}', '${catatanText.replace(/'/g, "\\'").replace(/"/g, '\\"')}')" title="Lihat detail catatan">
                Detail
            </button>` : '<span class="text-muted">-</span>';
        
        return `
            <tr>
                <td class="text-start">${tanggal}</td>
                <td class="text-start">${tipeKegiatan}</td>
                <td class="text-start">${item.nama_vaksin_obat || '-'}</td>
                <td class="text-end">${item.jumlah_burung || '-'}</td>
                <td class="text-end">${biaya}</td>
                <td class="text-start">${item.petugas || '-'}</td>
                <td class="text-start">${getRecorderName(item)}</td>
                <td class="text-start">${catatanButton}</td>
            </tr>
        `;
    }).join('');
    
    container.innerHTML = `
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead>
                    <tr>
                        <th class="text-start">Tanggal</th>
                        <th class="text-start">Tipe</th>
                        <th class="text-start">Vaksin/Obat</th>
                        <th class="text-end">Jumlah Burung</th>
                        <th class="text-end">Biaya</th>
                        <th class="text-start">Petugas</th>
                        <th class="text-start">Dicatat Oleh</th>
                        <th class="text-start">Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    ${rows}
                </tbody>
            </table>
        </div>
    `;
}

// ========== KESEHATAN CATATAN MODAL ==========

function showKesehatanCatatanModal(tanggal, dibuatOleh, catatan) {
    // Create modal HTML if it doesn't exist
    let modal = document.getElementById('kesehatanCatatanModal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'kesehatanCatatanModal';
        modal.className = 'modal fade';
        modal.innerHTML = `
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fa-solid fa-sticky-note text-info me-2"></i>
                            Detail Catatan Kesehatan
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="border rounded p-3 bg-light">
                                    <h6 class="text-muted mb-2">
                                        <i class="fa-solid fa-calendar-days me-1"></i>Tanggal
                                    </h6>
                                    <p class="mb-0 fw-bold" id="modalTanggal"></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border rounded p-3 bg-light">
                                    <h6 class="text-muted mb-2">
                                        <i class="fa-solid fa-user me-1"></i>Dibuat Oleh
                                    </h6>
                                    <p class="mb-0 fw-bold" id="modalDibuatOleh"></p>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="border rounded p-3">
                                    <h6 class="text-muted mb-3">
                                        <i class="fa-solid fa-file-lines me-1"></i>Isi Catatan
                                    </h6>
                                    <div id="modalCatatan" class="catatan-content" style="max-height: 300px; overflow-y: auto; white-space: pre-wrap; line-height: 1.5;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fa-solid fa-times me-1"></i>Tutup
                        </button>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    }
    
    // Fill modal content
    document.getElementById('modalTanggal').textContent = tanggal;
    document.getElementById('modalDibuatOleh').textContent = dibuatOleh;
    document.getElementById('modalCatatan').textContent = catatan;
    
    // Show modal
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
}

function renderBeratHistory(data) {
    console.log('🔍 renderBeratHistory called, container found:', !!document.getElementById('berat-history-content'), 'data count:', data?.length);
    
    const container = document.getElementById('berat-history-content');
    if (!container) {
        console.warn('⚠️ Berat history container not found');
        return;
    }
    
    if (!data || data.length === 0) {
        container.innerHTML = '<p class="text-muted small mb-0">ℹ️ Belum ada riwayat sampling berat</p>';
        return;
    }
    
    console.log('✅ Rendering', data.length, 'berat records');
    console.log('📝 Sample berat record:', data[0]);
    
    const rows = data.slice(0, 50).map(item => {
        const tanggal = new Date(item.tanggal_sampling).toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        });
        
        const beratGram = parseFloat(item.berat_rata_rata);
        const beratKg = (beratGram / 1000).toFixed(3);
        
        return `
            <tr>
                <td class="text-start">${tanggal}</td>
                <td class="text-end">${item.umur_hari} hari</td>
                <td class="text-end">${beratGram.toFixed(0)} g</td>
                <td class="text-end">${beratKg} kg</td>
                <td class="text-start">${item.catatan || '-'}</td>
                <td class="text-start">${getRecorderName(item)}</td>
            </tr>
        `;
    }).join('');
    
    container.innerHTML = `
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead>
                    <tr>
                        <th class="text-start">Tanggal Sampling</th>
                        <th class="text-end">Umur</th>
                        <th class="text-end">Berat (gram)</th>
                        <th class="text-end">Berat (kg)</th>
                        <th class="text-start">Catatan</th>
                        <th class="text-start">Dicatat Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    ${rows}
                </tbody>
            </table>
        </div>
        ${data.length > 50 ? `<p class="text-muted small mt-2 mb-0 text-center">Menampilkan 50 dari ${data.length} data</p>` : ''}
    `;
}

// ========== UPDATE METRICS ==========

function updateMetric(selector, html) {
    const el = document.querySelector(selector);
    if (el) el.innerHTML = html;
}

// ========== INITIAL LOAD ==========

// Load all data when page loads
if (pembesaranId) {
    Promise.all([
        loadPakanData(),
        loadKematianData(),
        loadLaporanData(),
        loadMonitoringData(),
        loadBeratData(),
        loadKesehatanData()
    ]).then(() => {
        console.log('✅ All data loaded');
    }).catch(err => {
        console.error('Error loading data:', err);
    });
}

console.log('✅ AJAX functions initialized');
