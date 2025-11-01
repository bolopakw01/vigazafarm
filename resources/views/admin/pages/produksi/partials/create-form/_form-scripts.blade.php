<script>
  // GLOBAL helpers so resetForm can call toggleSections()
  const FORM_STATE_KEY = 'produksi_form_state';

  // Debounced save function to avoid excessive localStorage writes
  let saveTimeout;
  function debouncedSaveFormState() {
    clearTimeout(saveTimeout);
    saveTimeout = setTimeout(saveFormState, 500); // Save after 500ms of inactivity
  }

  // Save form state to localStorage (only control states, not input values)
  function saveFormState() {
    const state = {
      // Radio buttons (control states)
      jenis_input: document.querySelector('input[name="jenis_input"]:checked')?.value || 'manual',
      fokus_manual: document.querySelector('input[name="fokus_manual"]:checked')?.value || 'burung',
      jenis_kelamin: document.querySelector('input[name="jenis_kelamin"]:checked')?.value || 'jantan',

      // Select dropdowns (control states)
      kandang_id: document.getElementById('kandang_id')?.value || '',
      pembesaran_id: document.getElementById('pembesaran_id')?.value || '',
      penetasan_id: document.getElementById('penetasan_id')?.value || '',
      status: document.getElementById('status')?.value || 'aktif',

      timestamp: Date.now()
    };

    try {
      localStorage.setItem(FORM_STATE_KEY, JSON.stringify(state));
    } catch (e) {
      console.warn('Failed to save form state to localStorage:', e);
    }
  }

  // Restore form state from localStorage (only control states)
  function restoreFormState() {
    try {
      const savedState = localStorage.getItem(FORM_STATE_KEY);
      if (!savedState) return false;

      const state = JSON.parse(savedState);

      // Only restore if saved within last 24 hours
      const oneDay = 24 * 60 * 60 * 1000;
      if (Date.now() - state.timestamp > oneDay) {
        localStorage.removeItem(FORM_STATE_KEY);
        return false;
      }

      // Restore radio buttons (control states)
      if (state.jenis_input) {
        const jenisInputRadio = document.querySelector(`input[name="jenis_input"][value="${state.jenis_input}"]`);
        if (jenisInputRadio) jenisInputRadio.checked = true;
      }

      if (state.fokus_manual) {
        const fokusManualRadio = document.querySelector(`input[name="fokus_manual"][value="${state.fokus_manual}"]`);
        if (fokusManualRadio) fokusManualRadio.checked = true;
      }

      if (state.jenis_kelamin) {
        const jenisKelaminRadio = document.querySelector(`input[name="jenis_kelamin"][value="${state.jenis_kelamin}"]`);
        if (jenisKelaminRadio) jenisKelaminRadio.checked = true;
      }

      // Restore select values (control states)
      if (state.kandang_id && document.getElementById('kandang_id')) {
        document.getElementById('kandang_id').value = state.kandang_id;
      }

      if (state.pembesaran_id && document.getElementById('pembesaran_id')) {
        document.getElementById('pembesaran_id').value = state.pembesaran_id;
      }

      if (state.penetasan_id && document.getElementById('penetasan_id')) {
        document.getElementById('penetasan_id').value = state.penetasan_id;
      }

      if (state.status && document.getElementById('status')) {
        document.getElementById('status').value = state.status;
      }

      // Input fields are NOT restored - they will be reset on page refresh

      return true;
    } catch (e) {
      console.warn('Failed to restore form state from localStorage:', e);
      return false;
    }
  }

  function generateBatchId() {
    const tanggalMulaiEl = document.getElementById('tanggal_mulai');
    const batchIdEl = document.getElementById('batch_produksi_id');

    const tanggalMulai = tanggalMulaiEl.value;
    if (!tanggalMulai) return;

    // Format date as YYYYMMDD
    const date = new Date(tanggalMulai);
    const dateStr = date.getFullYear().toString() +
                    (date.getMonth() + 1).toString().padStart(2, '0') +
                    date.getDate().toString().padStart(2, '0');

    // Determine prefix based on jenis_input and fokus_manual
    const jenisInput = document.querySelector('input[name="jenis_input"]:checked').value;
    let prefix = 'PROD';

    if (jenisInput === 'dari_penetasan') {
      prefix = 'PROD-TEL';
    } else if (jenisInput === 'manual') {
      const fokusManual = document.querySelector('input[name="fokus_manual"]:checked').value;
      prefix = fokusManual === 'telur' ? 'PROD-TEL' : 'PROD-PUY';
    } else if (jenisInput === 'dari_pembesaran') {
      prefix = 'PROD-PUY';
    }

    // Generate a simple sequential number using time
    const timestamp = Date.now().toString().slice(-4); // Last 4 digits of timestamp
    const batchId = `${prefix}-${dateStr}-${timestamp}`;

    batchIdEl.value = batchId;
  }

  function toggleCampuranFields() {
    const campuranFields = document.getElementById('campuranFields');
    const selectedKelamin = document.querySelector('input[name="jenis_kelamin"]:checked');
    const jumlahJantanField = document.getElementById('jumlah_jantan');
    const jumlahBetinaField = document.getElementById('jumlah_betina');

    if (selectedKelamin && selectedKelamin.value === 'campuran') {
      campuranFields.style.display = 'block';
      // Add name attributes when campuran is selected
      jumlahJantanField.setAttribute('name', 'jumlah_jantan');
      jumlahBetinaField.setAttribute('name', 'jumlah_betina');
    } else {
      campuranFields.style.display = 'none';
      // Remove name attributes and clear values when not using campuran
      jumlahJantanField.removeAttribute('name');
      jumlahBetinaField.removeAttribute('name');
      jumlahJantanField.value = '';
      jumlahBetinaField.value = '';
    }
    // Update validation display when fields are toggled
    validateCampuranCountsRealtime();
  }

  function toggleFokusManual() {
    // Call toggleSections to update field visibility and required attributes
    toggleSections();
    // Regenerate batch ID when fokus_manual changes
    generateBatchId();
    // Save form state
    saveFormState();
  }

  function validateForm() {
    // All validation is now handled on the server side
    // Client-side validation for campuran has been moved to controller
    return true;
  }

  function handleFormSubmit(event) {
    // This function is kept for future Sweet Alert implementation
    return validateForm();
  }

  function validateCampuranCounts() {
    // This function is no longer used for form validation
    // Kept for backward compatibility if needed elsewhere
    return true;
  }

  function validateCampuranCountsRealtime() {
    const selectedKelamin = document.querySelector('input[name="jenis_kelamin"]:checked');
    const campuranValidationAlert = document.getElementById('campuranValidationAlert');
    const campuranSuccessAlert = document.getElementById('campuranSuccessAlert');
    const campuranErrorAlert = document.getElementById('campuranErrorAlert');

    if (selectedKelamin && selectedKelamin.value === 'campuran') {
      const jumlahBurung = parseInt(document.getElementById('jumlah_burung').value) || 0;
      const jumlahJantan = parseInt(document.getElementById('jumlah_jantan').value) || 0;
      const jumlahBetina = parseInt(document.getElementById('jumlah_betina').value) || 0;
      const total = jumlahJantan + jumlahBetina;

      if (jumlahBurung > 0 && (jumlahJantan > 0 || jumlahBetina > 0)) {
        if (total === jumlahBurung) {
          campuranValidationAlert.style.display = 'none';
          campuranSuccessAlert.style.display = 'block';
          campuranErrorAlert.style.display = 'none';
        } else {
          campuranValidationAlert.style.display = 'none';
          campuranSuccessAlert.style.display = 'none';
          campuranErrorAlert.style.display = 'block';
          campuranErrorAlert.innerHTML = `
            <i class="fa-solid fa-exclamation-triangle me-2"></i>
            <strong>Error:</strong> Jumlah Puyuh tidak sesuai (${total}) / (${jumlahBurung})
          `;
        }
      } else {
        campuranValidationAlert.style.display = 'block';
        campuranSuccessAlert.style.display = 'none';
        campuranErrorAlert.style.display = 'none';
      }
    } else {
      campuranValidationAlert.style.display = 'none';
      campuranSuccessAlert.style.display = 'none';
      campuranErrorAlert.style.display = 'none';
    }
  }

  function validateTanggalAkhir() {
    const tanggalMulai = document.getElementById('tanggal_mulai').value;
    const tanggalAkhir = document.getElementById('tanggal_akhir').value;
    const tanggalAkhirField = document.getElementById('tanggal_akhir');

    // Remove existing validation classes
    tanggalAkhirField.classList.remove('is-invalid', 'is-valid');

    // Only validate if both dates are filled
    if (tanggalMulai && tanggalAkhir) {
      const startDate = new Date(tanggalMulai);
      const endDate = new Date(tanggalAkhir);

      if (endDate < startDate) {
        tanggalAkhirField.classList.add('is-invalid');
        // Add custom validation message
        let errorDiv = tanggalAkhirField.parentNode.querySelector('.invalid-feedback');
        if (!errorDiv) {
          errorDiv = document.createElement('div');
          errorDiv.className = 'invalid-feedback';
          tanggalAkhirField.parentNode.appendChild(errorDiv);
        }
        errorDiv.textContent = 'Tanggal akhir harus setelah atau sama dengan tanggal mulai';
      } else {
        tanggalAkhirField.classList.add('is-valid');
      }
    } else if (tanggalAkhir && !tanggalMulai) {
      // If end date is filled but start date is empty, show warning
      tanggalAkhirField.classList.add('is-invalid');
      let errorDiv = tanggalAkhirField.parentNode.querySelector('.invalid-feedback');
      if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        tanggalAkhirField.parentNode.appendChild(errorDiv);
      }
      errorDiv.textContent = 'Harap isi tanggal mulai terlebih dahulu';
    }
  }

  function autoFillFromPembesaran() {
    const select = document.getElementById('pembesaran_id');
    const selectedOption = select.options[select.selectedIndex];
    if (selectedOption.value) {
      // Auto-fill tanggal_mulai from pembesaran tanggal_siap
      const tanggalSiap = selectedOption.getAttribute('data-tanggal-siap');
      if (tanggalSiap) {
        const tanggalMulaiInput = document.getElementById('tanggal_mulai');
        tanggalMulaiInput.value = tanggalSiap;
        tanggalMulaiInput.classList.add('auto-filled');
        tanggalMulaiInput.dispatchEvent(new Event('input'));
        // Regenerate batch ID with new date
        generateBatchId();
      }
    }
  }

  function autoFillFromPenetasan() {
    const select = document.getElementById('penetasan_id');
    const selectedOption = select.options[select.selectedIndex];
    if (selectedOption.value) {
      // Auto-fill tanggal_mulai from penetasan tanggal_menetas
      const tanggalMenetas = selectedOption.getAttribute('data-tanggal-menetas');
      if (tanggalMenetas) {
        const tanggalMulaiInput = document.getElementById('tanggal_mulai');
        tanggalMulaiInput.value = tanggalMenetas;
        tanggalMulaiInput.classList.add('auto-filled');
        tanggalMulaiInput.dispatchEvent(new Event('input'));
        // Regenerate batch ID with new date
        generateBatchId();
      }
    }
  }

  function toggleSections() {
    const selected = document.querySelector('input[name="jenis_input"]:checked').value;

    // Show/hide sections based on jenis_input
    const manualSection = document.getElementById('manualSection');
    const pembesaranSection = document.getElementById('pembesaranSection');
    const penetasanSection = document.getElementById('penetasanSection');

    manualSection.style.display = selected === 'manual' ? 'block' : 'none';
    pembesaranSection.style.display = selected === 'dari_pembesaran' ? 'block' : 'none';
    penetasanSection.style.display = selected === 'dari_penetasan' ? 'block' : 'none';

    // Clear campuran fields when switching to dari_pembesaran or dari_penetasan
    if (selected === 'dari_pembesaran' || selected === 'dari_penetasan') {
      document.getElementById('jumlah_jantan').value = '';
      document.getElementById('jumlah_betina').value = '';
      document.getElementById('jumlah_jantan').removeAttribute('name');
      document.getElementById('jumlah_betina').removeAttribute('name');
    }

    // Show/hide fields based on jenis_input and fokus_manual
    const fieldJumlahBurung = document.getElementById('field_jumlah_burung_container');
    const fieldJumlahTelur = document.getElementById('field_jumlah_telur_container');
    const fieldJenisKelamin = document.getElementById('field_jenis_kelamin_container');
    const fieldUmurBerat = document.getElementById('field_umur_berat_container');
    const fieldFertilTelur = document.getElementById('field_fertil_telur_container');
    const fieldBeratRataTelur = document.getElementById('field_berat_rata_telur_container');
    const fieldInfoPembesaran = document.getElementById('field_info_pembesaran_container');

    // Get field elements
    const jumlahBurungField = document.getElementById('jumlah_burung');
    const jumlahTelurField = document.getElementById('jumlah_telur');
    const persentaseFertilField = document.getElementById('persentase_fertil');
    const beratRataTelurField = document.getElementById('berat_rata_telur');
    const jenisKelaminRadios = document.querySelectorAll('input[name="jenis_kelamin"]');
    const pembesaranIdField = document.getElementById('pembesaran_id');
    const penetasanIdField = document.getElementById('penetasan_id');

    if (selected === 'manual') {
      const fokus = document.querySelector('input[name="fokus_manual"]:checked').value;
      if (fokus === 'burung') {
        fieldJumlahBurung.style.display = 'block';
        fieldJumlahTelur.style.display = 'none';
        fieldJenisKelamin.style.display = 'block';
        fieldUmurBerat.style.display = 'block';
        fieldFertilTelur.style.display = 'none';
        fieldBeratRataTelur.style.display = 'none';
        if (fieldInfoPembesaran) fieldInfoPembesaran.style.display = 'none';

        // Set required attributes
        jumlahBurungField.required = true;
        jumlahTelurField.required = false;
        persentaseFertilField.required = false;
        beratRataTelurField.required = false;
        // Set jenis_kelamin as required for manual burung input
        jenisKelaminRadios.forEach(radio => radio.required = true);
      } else {
        fieldJumlahBurung.style.display = 'none';
        fieldJumlahTelur.style.display = 'block';
        fieldJenisKelamin.style.display = 'none';
        fieldUmurBerat.style.display = 'none';
        fieldFertilTelur.style.display = 'block';
        fieldBeratRataTelur.style.display = 'none';
        if (fieldInfoPembesaran) fieldInfoPembesaran.style.display = 'none';

        // Set required attributes
        jumlahBurungField.required = false;
        jumlahTelurField.required = true;
        persentaseFertilField.required = false;
        beratRataTelurField.required = true;
      }

      if (pembesaranIdField) {
        pembesaranIdField.required = false;
        pembesaranIdField.removeAttribute('required');
        pembesaranIdField.disabled = true;
        pembesaranIdField.value = '';
        pembesaranIdField.classList.remove('is-invalid');
      }
      if (penetasanIdField) {
        penetasanIdField.required = false;
        penetasanIdField.removeAttribute('required');
        penetasanIdField.disabled = true;
        penetasanIdField.value = '';
        penetasanIdField.classList.remove('is-invalid');
      }
    } else if (selected === 'dari_pembesaran') {
      fieldJumlahBurung.style.display = 'block';
      fieldJumlahTelur.style.display = 'none';
      fieldJenisKelamin.style.display = 'block';
      fieldUmurBerat.style.display = 'none'; // Hide main umur/berat fields
      fieldFertilTelur.style.display = 'none';
      fieldBeratRataTelur.style.display = 'none';
      if (fieldInfoPembesaran) fieldInfoPembesaran.style.display = 'block';

      // Set required attributes
      jumlahBurungField.required = true;
      jumlahTelurField.required = false;
      persentaseFertilField.required = false;
      beratRataTelurField.required = false;
      // jenis_kelamin is optional for dari_pembesaran
      jenisKelaminRadios.forEach(radio => radio.required = false);
      // pembesaran_id is required for dari_pembesaran
      if (pembesaranIdField) {
        pembesaranIdField.required = true;
        pembesaranIdField.setAttribute('required', 'required');
        pembesaranIdField.disabled = false;
      }
      // penetasan_id is not required
      if (penetasanIdField) {
        penetasanIdField.required = false;
        penetasanIdField.removeAttribute('required');
        penetasanIdField.disabled = true;
        penetasanIdField.value = '';
        penetasanIdField.classList.remove('is-invalid');
      }
    } else if (selected === 'dari_penetasan') {
      fieldJumlahBurung.style.display = 'none';
      fieldJumlahTelur.style.display = 'block';
      fieldJenisKelamin.style.display = 'none';
      fieldUmurBerat.style.display = 'none';
      fieldFertilTelur.style.display = 'block'; // Show fertil fields for penetasan
      fieldBeratRataTelur.style.display = 'block'; // Show berat rata telur for penetasan
      if (fieldInfoPembesaran) fieldInfoPembesaran.style.display = 'none';

      // Set required attributes
      jumlahBurungField.required = false;
      jumlahTelurField.required = true;
      persentaseFertilField.required = false; // Not required for penetasan transfer
      beratRataTelurField.required = false; // Optional for penetasan transfer
      // jenis_kelamin not shown for penetasan
      jenisKelaminRadios.forEach(radio => radio.required = false);
      // pembesaran_id is not required
      if (pembesaranIdField) {
        pembesaranIdField.required = false;
        pembesaranIdField.removeAttribute('required');
        pembesaranIdField.disabled = true;
        pembesaranIdField.value = '';
        pembesaranIdField.classList.remove('is-invalid');
      }
      // penetasan_id is required for dari_penetasan
      if (penetasanIdField) {
        penetasanIdField.required = true;
        penetasanIdField.setAttribute('required', 'required');
        penetasanIdField.disabled = false;
      }
    }

    // Update dynamic titles
    const dynamicTitles = document.querySelectorAll('.dynamic');
    dynamicTitles.forEach(title => {
      title.classList.remove('manual', 'pembesaran', 'penetasan');
      if (selected === 'manual') title.classList.add('manual');
      else if (selected === 'dari_pembesaran') title.classList.add('pembesaran');
      else if (selected === 'dari_penetasan') title.classList.add('penetasan');
    });

    // Update field hints and visibility based on jenis_input
    updateFieldHints(selected);

    // Update required asterisks in labels
    updateRequiredLabels();

    // regenerate batch id when jenis_input changes
    generateBatchId();
  }

  function updateRequiredLabels() {
    // Get field elements
    const jumlahBurungField = document.getElementById('jumlah_burung');
    const jumlahTelurField = document.getElementById('jumlah_telur');
    const persentaseFertilField = document.getElementById('persentase_fertil');
    const beratRataTelurField = document.getElementById('berat_rata_telur');
    const jenisKelaminRadios = document.querySelectorAll('input[name="jenis_kelamin"]');
    const pembesaranIdField = document.getElementById('pembesaran_id');
    const penetasanIdField = document.getElementById('penetasan_id');

    // Get label elements
    const jumlahBurungLabel = document.querySelector('label[for="jumlah_burung"]');
    const jumlahTelurLabel = document.querySelector('label[for="jumlah_telur"]');
    const persentaseFertilLabel = document.querySelector('label[for="persentase_fertil"]');
    const beratRataTelurLabel = document.querySelector('label[for="berat_rata_telur"]');
    const pembesaranIdLabel = document.querySelector('label[for="pembesaran_id"]');
    const penetasanIdLabel = document.querySelector('label[for="penetasan_id"]');

    // Find the jenis kelamin label more specifically
    const jenisKelaminLabels = document.querySelectorAll('label');
    let jenisKelaminLabel = null;
    for (let label of jenisKelaminLabels) {
      if (label.textContent.includes('Jenis Kelamin Puyuh')) {
        jenisKelaminLabel = label;
        break;
      }
    }

    // Update labels with required asterisks
    if (jumlahBurungLabel) {
      const baseText = 'Jumlah Puyuh';
      jumlahBurungLabel.innerHTML = baseText + (jumlahBurungField.required ? ' <span class="required">*</span>' : '');
    }

    if (jumlahTelurLabel) {
      const baseText = 'Jumlah Telur';
      jumlahTelurLabel.innerHTML = baseText + (jumlahTelurField.required ? ' <span class="required">*</span>' : '');
    }

    if (persentaseFertilLabel) {
      const baseText = 'Persentase Fertil (%)';
      persentaseFertilLabel.innerHTML = baseText + (persentaseFertilField.required ? ' <span class="required">*</span>' : '');
    }

    if (beratRataTelurLabel) {
      const baseText = 'Berat Rata-rata Telur (gram)';
      beratRataTelurLabel.innerHTML = baseText + (beratRataTelurField.required ? ' <span class="required">*</span>' : '');
    }

    if (pembesaranIdLabel) {
      const baseText = 'Pilih Pembesaran';
      pembesaranIdLabel.innerHTML = baseText + (pembesaranIdField && pembesaranIdField.required ? ' <span class="required">*</span>' : '');
    }

    if (penetasanIdLabel) {
      const baseText = 'Pilih Penetasan';
      penetasanIdLabel.innerHTML = baseText + (penetasanIdField && penetasanIdField.required ? ' <span class="required">*</span>' : '');
    }

    // Update jenis kelamin label based on whether it's required
    if (jenisKelaminLabel) {
      const baseText = 'Jenis Kelamin Puyuh';
      const isRequired = Array.from(jenisKelaminRadios).some(radio => radio.required);
      jenisKelaminLabel.innerHTML = baseText + (isRequired ? ' <span class="required">*</span>' : '');
    }
  }

  function updateFieldHints(jenisInput) {
    // Hide all hints first
    document.querySelectorAll('.field-hint-manual, .field-hint-pembesaran, .field-hint-penetasan').forEach(hint => {
      hint.style.display = 'none';
    });

    // Show relevant hints
    const manualHints = document.querySelectorAll('.field-hint-manual');
    const pembesaranHints = document.querySelectorAll('.field-hint-pembesaran');
    const penetasanHints = document.querySelectorAll('.field-hint-penetasan');

    if (jenisInput === 'manual') {
      manualHints.forEach(hint => hint.style.display = 'block');
    } else if (jenisInput === 'dari_pembesaran') {
      pembesaranHints.forEach(hint => hint.style.display = 'block');
    } else if (jenisInput === 'dari_penetasan') {
      penetasanHints.forEach(hint => hint.style.display = 'block');
    }

    // Reset auto-filled styling for info fields
    document.querySelectorAll('.field-auto-fill').forEach(field => {
      field.classList.remove('auto-filled');
    });
  }

  function initializeDatePlaceholders() {
    const inputs = document.querySelectorAll('.date-input');
    inputs.forEach((input) => {
      const wrapper = input.closest('.date-input-wrapper');
      if (!wrapper) return;

      const placeholder = input.getAttribute('data-placeholder') || 'Pilih tanggal';
      const label = wrapper.querySelector('.placeholder-label');
      if (label && !label.dataset.initialized) {
        label.textContent = placeholder;
        label.dataset.initialized = 'true';
      }

      const toggle = () => {
        if (input.value) {
          wrapper.classList.add('has-value');
        } else {
          wrapper.classList.remove('has-value');
        }
      };

      ['change', 'input', 'blur'].forEach(evt => {
        input.addEventListener(evt, toggle);
      });

      toggle();
    });
  }

  document.addEventListener('DOMContentLoaded', function() {
    // Set default date to today for tanggal_mulai
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('tanggal_mulai').value = today;
    document.getElementById('tanggal_mulai').dispatchEvent(new Event('input'));

    // Generate initial batch ID
    generateBatchId();

    // Attach change listeners
    const jenisInputRadios = document.querySelectorAll('input[name="jenis_input"]');
    jenisInputRadios.forEach(r => r.addEventListener('change', function() {
      toggleSections();
      saveFormState();
    }));

    // Attach change listeners for fokus_manual
    const fokusManualRadios = document.querySelectorAll('input[name="fokus_manual"]');
    fokusManualRadios.forEach(r => r.addEventListener('change', toggleFokusManual));

    // Attach change listeners for jenis_kelamin
    const jenisKelaminRadios = document.querySelectorAll('input[name="jenis_kelamin"]');
    jenisKelaminRadios.forEach(r => r.addEventListener('change', function() {
      toggleCampuranFields();
      validateCampuranCountsRealtime();
      saveFormState();
    }));

    // Add real-time validation for campuran fields
    const jumlahBurungField = document.getElementById('jumlah_burung');
    const jumlahJantanField = document.getElementById('jumlah_jantan');
    const jumlahBetinaField = document.getElementById('jumlah_betina');

    if (jumlahBurungField) {
      jumlahBurungField.addEventListener('input', validateCampuranCountsRealtime);
    }
    if (jumlahJantanField) {
      jumlahJantanField.addEventListener('input', validateCampuranCountsRealtime);
    }
    if (jumlahBetinaField) {
      jumlahBetinaField.addEventListener('input', validateCampuranCountsRealtime);
    }

    // Update batch ID when tanggal_mulai changes
    const tanggalMulaiField = document.getElementById('tanggal_mulai');
    tanggalMulaiField.addEventListener('change', generateBatchId);
    tanggalMulaiField.addEventListener('change', () => {
      tanggalMulaiField.dispatchEvent(new Event('input'));
    });

    initializeDatePlaceholders();

    // Store pembesaran and penetasan data for auto-fill
    const pembesaranData = {!! json_encode($pembesaranList ? $pembesaranList->map(function($p) {
      return [
        'id' => $p->id,
        'stok_tersedia' => $p->jumlah_siap ? ($p->jumlah_siap - ($p->indukan_ditransfer ?? 0)) : 0,
        'jumlah_siap' => $p->jumlah_siap ?? 0,
        'tanggal_siap' => $p->tanggal_siap ? $p->tanggal_siap->format('Y-m-d') : null,
        'umur_hari' => $p->umur_hari,
        'berat_rata' => $p->berat_rata_rata ?? 0,
        'jenis_kelamin' => $p->jenis_kelamin,
        'jumlah_jantan' => $p->jumlah_jantan ?? null,
        'jumlah_betina' => $p->jumlah_betina ?? null
      ];
    })->toArray() : []) !!};

    const penetasanData = {!! json_encode($penetasanList ? $penetasanList->map(function($p) {
      return [
        'id' => $p->id,
        'stok_tersedia' => $p->telur_tidak_fertil - ($p->telur_infertil_ditransfer ?? 0),
        'tanggal_menetas' => $p->tanggal_menetas ? $p->tanggal_menetas->format('Y-m-d') : null
      ];
    })->toArray() : []) !!};

    // Auto-fill jumlah_burung when pembesaran is selected
    document.getElementById('pembesaran_id').addEventListener('change', function() {
      const selectedId = this.value;
      const jumlahBurungField = document.getElementById('jumlah_burung');
      const umurBurungField = document.getElementById('umur_burung');
      const umurBurungPembesaranField = document.getElementById('umur_burung_pembesaran');
      const beratRataBurungPembesaranField = document.getElementById('berat_rata_burung_pembesaran');
      const jenisKelaminSectionRadios = document.querySelectorAll('input[name="jenis_kelamin"]');

      if (selectedId) {
        const pembesaran = pembesaranData.find(p => p.id == selectedId);
        if (pembesaran) {
          jumlahBurungField.value = pembesaran.stok_tersedia;
          umurBurungField.value = pembesaran.umur_hari || '';
          umurBurungPembesaranField.value = pembesaran.umur_hari || '';
          beratRataBurungPembesaranField.value = pembesaran.berat_rata || '';
          jumlahBurungField.classList.add('auto-filled');
          umurBurungField.classList.add('auto-filled');
          // Add auto-filled styling to info fields
          umurBurungPembesaranField.classList.add('auto-filled');
          beratRataBurungPembesaranField.classList.add('auto-filled');

          if (pembesaran.jenis_kelamin) {
            const jenisKelaminValue = pembesaran.jenis_kelamin;
            const targetRadio = document.querySelector(`input[name="jenis_kelamin"][value="${jenisKelaminValue}"]`);
            if (targetRadio) {
              targetRadio.checked = true;
              toggleCampuranFields();
              if (jenisKelaminValue === 'campuran') {
                document.getElementById('jumlah_jantan').value = '';
                document.getElementById('jumlah_betina').value = '';
              }
              validateCampuranCountsRealtime();
            }
          }
        }
      } else {
        jumlahBurungField.value = '';
        umurBurungField.value = '';
        umurBurungPembesaranField.value = '';
        beratRataBurungPembesaranField.value = '';
        jumlahBurungField.classList.remove('auto-filled');
        umurBurungField.classList.remove('auto-filled');
        // Remove auto-filled styling from info fields
        umurBurungPembesaranField.classList.remove('auto-filled');
        beratRataBurungPembesaranField.classList.remove('auto-filled');
        if (jenisKelaminSectionRadios.length) {
          // Reset to default (first radio) when pembesaran cleared
          const defaultRadio = jenisKelaminSectionRadios[0];
          if (defaultRadio) {
            defaultRadio.checked = true;
          }
          toggleCampuranFields();
          validateCampuranCountsRealtime();
        }
      }

      // Call the new auto-fill function for production fields
      autoFillFromPembesaran();
      // Save form state
      saveFormState();
    });

    // Auto-fill jumlah_telur when penetasan is selected
    document.getElementById('penetasan_id').addEventListener('change', function() {
      const selectedId = this.value;
      const jumlahTelurField = document.getElementById('jumlah_telur');
      const persentaseFertilField = document.getElementById('persentase_fertil');

      if (selectedId) {
        const penetasan = penetasanData.find(p => p.id == selectedId);
        if (penetasan) {
          // Fill form field with available stock
          jumlahTelurField.value = penetasan.stok_tersedia;
          // Set persentase fertil to 100% for penetasan since eggs are already infertile
          persentaseFertilField.value = 100;
          // Add auto-filled styling
          jumlahTelurField.classList.add('auto-filled');
        }
      } else {
        jumlahTelurField.value = '';
        persentaseFertilField.value = '';
        // Remove auto-filled styling
        jumlahTelurField.classList.remove('auto-filled');
      }

      // Call the new auto-fill function for production fields
      autoFillFromPenetasan();
      // Save form state
      saveFormState();
    });

    // Attach change listener for kandang_id
    document.getElementById('kandang_id').addEventListener('change', saveFormState);

    // Attach change listener for status
    document.getElementById('status').addEventListener('change', saveFormState);

    // Add date validation for tanggal_akhir (but don't save state on input)
    document.getElementById('tanggal_akhir').addEventListener('change', function() {
      validateTanggalAkhir();
      // Don't save form state for input field changes
    });

    document.getElementById('tanggal_mulai').addEventListener('change', function() {
      validateTanggalAkhir();
      // Don't save form state for input field changes
    });

    // Initialize UI state after a short delay to ensure radio buttons are set
    setTimeout(function() {
      // First try to restore from localStorage (only control states)
      const restoredFromStorage = restoreFormState();

      // Clear all input fields to ensure fresh start (except tanggal_mulai which is set to today)
      const inputFieldsToClear = [
        'jumlah_burung', 'jumlah_telur', 'jumlah_jantan', 'jumlah_betina',
        'tanggal_akhir', 'umur_burung', 'berat_rata_burung',
        'persentase_fertil', 'berat_rata_telur', 'harga_per_kg', 'catatan'
      ];

      inputFieldsToClear.forEach(fieldId => {
        const element = document.getElementById(fieldId);
        if (element) {
          element.value = '';
        }
      });

      // If not restored from storage, use Laravel old() values (for validation errors)
      if (!restoredFromStorage) {
        const oldJenisInput = '{{ old('jenis_input', 'manual') }}';
        const oldFokusManual = '{{ old('fokus_manual', 'burung') }}';
        const oldJenisKelamin = '{{ old('jenis_kelamin', 'jantan') }}';

        // Set radio buttons explicitly
        const jenisInputRadio = document.querySelector('input[name="jenis_input"][value="' + oldJenisInput + '"]');
        if (jenisInputRadio) {
          jenisInputRadio.checked = true;
        }

        const fokusManualRadio = document.querySelector('input[name="fokus_manual"][value="' + oldFokusManual + '"]');
        if (fokusManualRadio) {
          fokusManualRadio.checked = true;
        }

        const jenisKelaminRadio = document.querySelector(`input[name="jenis_kelamin"][value="${oldJenisKelamin}"]`);
        if (jenisKelaminRadio) {
          jenisKelaminRadio.checked = true;
        }
      }

      // Now initialize UI
      toggleSections();
      toggleCampuranFields();
      // Validate dates on load
      validateTanggalAkhir();
    }, 10);
  });

  function resetForm() {
    document.getElementById('produksiForm').reset();
    // Clear saved form state from localStorage
    try {
      localStorage.removeItem(FORM_STATE_KEY);
    } catch (e) {
      console.warn('Failed to clear form state from localStorage:', e);
    }
    // Reset tanggal_mulai to today
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('tanggal_mulai').value = today;
    // Reset auto-filled styling (only for actual form fields, not info fields)
    document.querySelectorAll('.field-auto-fill').forEach(field => {
      field.classList.remove('auto-filled');
    });
    // Reset info fields in pembesaran section
    document.getElementById('umur_burung_pembesaran').value = '';
    document.getElementById('berat_rata_burung_pembesaran').value = '';
    // Hide info fields container
    const fieldInfoPembesaran = document.getElementById('field_info_pembesaran_container');
    if (fieldInfoPembesaran) fieldInfoPembesaran.style.display = 'none';
    // Reset validation alerts
    document.getElementById('campuranValidationAlert').style.display = 'none';
    document.getElementById('campuranSuccessAlert').style.display = 'none';
    document.getElementById('campuranErrorAlert').style.display = 'none';
    // Reset persentase_fertil for penetasan
    document.getElementById('persentase_fertil').value = '';
    // Reset catatan textarea
    document.getElementById('catatan').value = '';
    // Clear campuran fields
    document.getElementById('jumlah_jantan').value = '';
    document.getElementById('jumlah_betina').value = '';
    // Remove name attributes from campuran fields
    document.getElementById('jumlah_jantan').removeAttribute('name');
    document.getElementById('jumlah_betina').removeAttribute('name');
  }
</script>