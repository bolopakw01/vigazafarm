<script>
  let kapasitasSisaSaatIni = 0;
  let lastCapacityAlertValue = null;

  function triggerFlashToast(icon, title, message, timer = 3500) {
    if (!message) {
      return;
    }

    Swal.fire({
      toast: true,
      position: 'top-end',
      icon,
      title,
      text: message,
      showConfirmButton: false,
      timer,
      timerProgressBar: true,
      didOpen: toast => {
        toast.addEventListener('mouseenter', Swal.stopTimer);
        toast.addEventListener('mouseleave', Swal.resumeTimer);
      }
    });
  }

    const kandangSelectEl = document.getElementById('kandang_id');
    if (kandangSelectEl) {
      kandangSelectEl.addEventListener('change', () => {
        updateCapacityInfo();
        enforceCapacityLimit(false);
        saveFormState();
      });
      updateCapacityInfo();
    }

  function formatCapacityNumber(value) {
    const numeric = Number.isFinite(value) ? value : parseInt(value ?? 0, 10) || 0;
    return new Intl.NumberFormat('id-ID').format(Math.max(numeric, 0));
  }

  function getKandangSelect() {
    return document.getElementById('kandang_id');
  }

  function getCapacityInfoElements() {
    return {
      card: document.getElementById('kandangCapacityInfo'),
      text: document.getElementById('kandangCapacityInfoText')
    };
  }

  function isBurungModeActive() {
    const jenisInput = document.querySelector('input[name="jenis_input"]:checked')?.value || 'manual';
    if (jenisInput === 'dari_pembesaran') {
      return true;
    }
    if (jenisInput === 'manual') {
      const fokusManual = document.querySelector('input[name="fokus_manual"]:checked')?.value || 'burung';
      return fokusManual === 'burung';
    }
    return false;
  }

  function updateCapacityInfo() {
    const { card, text } = getCapacityInfoElements();
    const select = getKandangSelect();

    if (!card || !text) {
      return;
    }

    if (!select || !select.value) {
      kapasitasSisaSaatIni = 0;
      card.classList.remove('capacity-warning');
      text.textContent = 'Pilih kandang untuk melihat stok tersisa.';
      return;
    }

    const option = select.options[select.selectedIndex];
    const total = parseInt(option?.dataset?.kapasitas ?? '0', 10) || 0;
    const used = parseInt(option?.dataset?.terpakai ?? '0', 10) || 0;
    const remaining = parseInt(option?.dataset?.sisa ?? '0', 10);
    kapasitasSisaSaatIni = Math.max(remaining, 0);

    text.innerHTML = `Sisa <strong>${formatCapacityNumber(remaining)}</strong> dari ${formatCapacityNumber(total)} slot (terpakai ${formatCapacityNumber(used)})`;
    card.classList.toggle('capacity-warning', remaining <= 0);
  }

  function enforceCapacityLimit(showAlert = false) {
    if (!isBurungModeActive()) {
      lastCapacityAlertValue = null;
      return true;
    }

    const jumlahField = document.getElementById('jumlah_burung');
    const select = getKandangSelect();

    if (!jumlahField || !select || !select.value) {
      lastCapacityAlertValue = null;
      return true;
    }

    const value = parseInt(jumlahField.value || '0', 10);
    if (!Number.isFinite(value) || value <= 0) {
      lastCapacityAlertValue = null;
      return true;
    }

    if (kapasitasSisaSaatIni <= 0) {
      if (showAlert && lastCapacityAlertValue !== 'penuh') {
        Swal.fire({
          icon: 'warning',
          title: 'Kapasitas penuh',
          text: 'Kandang yang dipilih sudah penuh. Pilih kandang lain atau selesaikan batch aktif.',
        });
        lastCapacityAlertValue = 'penuh';
      }
      jumlahField.value = '';
      validateCampuranCountsRealtime();
      return false;
    }

    if (value > kapasitasSisaSaatIni) {
      if (showAlert && lastCapacityAlertValue !== value) {
        Swal.fire({
          icon: 'warning',
          title: 'Melebihi kapasitas',
          text: `Jumlah puyuh melebihi sisa kapasitas (${formatCapacityNumber(kapasitasSisaSaatIni)}). Nilai akan disesuaikan otomatis.`,
        });
        lastCapacityAlertValue = value;
      }
      jumlahField.value = kapasitasSisaSaatIni;
      validateCampuranCountsRealtime();
      return false;
    }

    lastCapacityAlertValue = null;
    return true;
  }

  function validateCapacityBeforeSubmit() {
    if (!isBurungModeActive()) {
      return true;
    }

    const jumlahField = document.getElementById('jumlah_burung');
    const select = getKandangSelect();

    if (!jumlahField || !select || !select.value) {
      return true;
    }

    const value = parseInt(jumlahField.value || '0', 10);
    const selectedLabel = select.options[select.selectedIndex]?.text?.trim() || 'kandang terpilih';

    if (kapasitasSisaSaatIni <= 0) {
      Swal.fire({
        icon: 'error',
        title: 'Kandang penuh',
        text: `${selectedLabel} sudah tidak memiliki slot tersisa.`,
      });
      return false;
    }

    if (value > kapasitasSisaSaatIni) {
      Swal.fire({
        icon: 'error',
        title: 'Melebihi kapasitas',
        text: `Jumlah indukan melewati sisa kapasitas (${formatCapacityNumber(kapasitasSisaSaatIni)}).`,
      });
      return false;
    }

    return true;
  }

  // GLOBAL helpers so resetForm can call toggleSections()
  const FORM_STATE_KEY = 'produksi_form_state';
  const hasValidationErrors = @json($errors->any());

  // Debounced save function to avoid excessive localStorage writes
  let saveTimeout;
  function debouncedSaveFormState() {
    clearTimeout(saveTimeout);
    saveTimeout = setTimeout(saveFormState, 500); // Save after 500ms of inactivity
  }

  // Save form state to localStorage (only control states, not input values)
  function saveFormState() {
    const state = {
      jenis_input: document.querySelector('input[name="jenis_input"]:checked')?.value || 'manual',
      fokus_manual: document.querySelector('input[name="fokus_manual"]:checked')?.value || 'burung',
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

      // Input fields are NOT restored - they will be reset on page refresh

      return true;
    } catch (e) {
      console.warn('Failed to restore form state from localStorage:', e);
      return false;
    }
  }

  // Clear all user-entered values but keep control state (radio toggles)
  function clearInputValuesKeepState() {
    const form = document.getElementById('produksiForm');
    if (!form) return;

    const preserveNames = new Set(['jenis_input', 'fokus_manual', 'jenis_kelamin', '_token', '_method']);

    form.querySelectorAll('input, textarea, select').forEach(el => {
      const name = el.name || '';
      const type = (el.type || '').toLowerCase();

      if (preserveNames.has(name)) return;
      if (['button', 'submit', 'reset', 'hidden'].includes(type)) return;

      if (type === 'radio' || type === 'checkbox') {
        el.checked = false;
        return;
      }

      if (el.tagName === 'SELECT') {
        el.value = '';
        return;
      }

      el.value = '';
      el.classList.remove('auto-filled', 'is-valid', 'is-invalid');
    });
  }

  function generateBatchId(force = false) {
    const tanggalMulaiEl = document.getElementById('tanggal_mulai');
    const batchIdEl = document.getElementById('batch_produksi_id');

    if (!tanggalMulaiEl || !batchIdEl) {
      return;
    }

    if (!force && batchIdEl.value && batchIdEl.value.trim().length > 0) {
      return;
    }

    const tanggalMulai = tanggalMulaiEl.value;
    if (!tanggalMulai) return;

    // Format date as YYYYMMDD
    const date = new Date(tanggalMulai);
    const dateStr = date.getFullYear().toString() +
                    (date.getMonth() + 1).toString().padStart(2, '0') +
                    date.getDate().toString().padStart(2, '0');

    // Determine prefix based on jenis_input and fokus_manual
    const jenisInputRadio = document.querySelector('input[name="jenis_input"]:checked');
    const jenisInput = jenisInputRadio ? jenisInputRadio.value : 'manual';
    let prefix = 'PROD';

    if (jenisInput === 'dari_produksi') {
      prefix = 'PROD-TEL';
    } else if (jenisInput === 'manual') {
      const fokusManualRadio = document.querySelector('input[name="fokus_manual"]:checked');
      const fokusManual = fokusManualRadio ? fokusManualRadio.value : 'burung';
      prefix = fokusManual === 'telur' ? 'PROD-TEL' : 'PROD-PUY';
    } else if (jenisInput === 'dari_pembesaran') {
      prefix = 'PROD-PUY';
    }

    // Generate code (UI only); final mapping will be normalized server-side to BatchProduksi
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
    generateBatchId(true);
    // Save form state
    saveFormState();
  }

  function validateForm() {
    if (!validateCapacityBeforeSubmit()) {
      return false;
    }

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
        generateBatchId(true);
      }

      // Auto-fill jumlah puyuh, umur, dan berat rata-rata dari pembesaran terpilih
      const stokTersedia = parseInt(selectedOption.dataset.stokTersedia || '0', 10);
      const umurHari = parseInt(selectedOption.dataset.umurHari || '0', 10);
      const beratRata = parseFloat(selectedOption.dataset.beratRata || '0');

      const jumlahBurungField = document.getElementById('jumlah_burung');
      const umurField = document.getElementById('umur_hari');
      const beratField = document.getElementById('berat_rata');

      if (jumlahBurungField) {
        jumlahBurungField.value = Number.isFinite(stokTersedia) && stokTersedia > 0 ? stokTersedia : '';
        jumlahBurungField.classList.toggle('auto-filled', !!jumlahBurungField.value);
      }

      if (umurField) {
        umurField.value = Number.isFinite(umurHari) && umurHari > 0 ? umurHari : '';
        umurField.classList.toggle('auto-filled', !!umurField.value);
      }

      if (beratField) {
        beratField.value = Number.isFinite(beratRata) && beratRata > 0 ? beratRata : '';
        beratField.classList.toggle('auto-filled', !!beratField.value);
      }
    }
  }

  function autoFillFromProduksi() {
    const select = document.getElementById('produksi_sumber_id');
    if (!select) return;

    const selectedOption = select.options[select.selectedIndex];
    const jumlahTelurField = document.getElementById('jumlah_telur');
    const persentaseFertilField = document.getElementById('persentase_fertil');

    if (!jumlahTelurField) {
      return;
    }

    if (selectedOption && selectedOption.value) {
      const stokTersedia = parseInt(selectedOption.dataset.telurTersedia || '0', 10);
      if (!Number.isNaN(stokTersedia) && stokTersedia > 0) {
        jumlahTelurField.value = stokTersedia;
        jumlahTelurField.classList.add('auto-filled');
        if (persentaseFertilField) {
          persentaseFertilField.value = 100;
        }
      } else {
        jumlahTelurField.value = '';
        jumlahTelurField.classList.remove('auto-filled');
        if (persentaseFertilField) {
          persentaseFertilField.value = '';
        }
      }
    } else {
      jumlahTelurField.value = '';
      jumlahTelurField.classList.remove('auto-filled');
      if (persentaseFertilField) {
        persentaseFertilField.value = '';
      }
    }
  }

  function toggleSections() {
    const selected = document.querySelector('input[name="jenis_input"]:checked').value;

    // Show/hide sections based on jenis_input
    const manualSection = document.getElementById('manualSection');
    const pembesaranSection = document.getElementById('pembesaranSection');
    const produksiSection = document.getElementById('produksiSourceSection');
    const isProduksiTransfer = selected === 'dari_produksi';

    manualSection.style.display = selected === 'manual' ? 'block' : 'none';
    pembesaranSection.style.display = selected === 'dari_pembesaran' ? 'block' : 'none';
    produksiSection.style.display = isProduksiTransfer ? 'block' : 'none';

    // Clear campuran fields when switching to dari_pembesaran or transfer telur
    if (selected === 'dari_pembesaran' || isProduksiTransfer) {
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
    const produksiIdField = document.getElementById('produksi_sumber_id');

    if (jumlahTelurField) {
      jumlahTelurField.readOnly = false;
      jumlahTelurField.classList.remove('bg-light');
      jumlahTelurField.removeAttribute('title');
    }

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
        beratRataTelurField.removeAttribute('required');
        // Set jenis_kelamin as required for manual burung input
        jenisKelaminRadios.forEach(radio => radio.required = true);
      } else {
        fieldJumlahBurung.style.display = 'none';
        fieldJumlahTelur.style.display = 'block';
        fieldJenisKelamin.style.display = 'none';
        fieldUmurBerat.style.display = 'none';
        fieldFertilTelur.style.display = 'block';
        fieldBeratRataTelur.style.display = 'block';
        if (fieldInfoPembesaran) fieldInfoPembesaran.style.display = 'none';

        // Set required attributes
        jumlahBurungField.required = false;
        jumlahTelurField.required = true;
        persentaseFertilField.required = false;
        beratRataTelurField.required = false;
        beratRataTelurField.removeAttribute('required');
      }

      if (pembesaranIdField) {
        pembesaranIdField.required = false;
        pembesaranIdField.removeAttribute('required');
        pembesaranIdField.disabled = true;
        pembesaranIdField.value = '';
        pembesaranIdField.classList.remove('is-invalid');
      }
      if (produksiIdField) {
        produksiIdField.required = false;
        produksiIdField.removeAttribute('required');
        produksiIdField.disabled = true;
        produksiIdField.value = '';
        produksiIdField.classList.remove('is-invalid');
      }
    } else if (selected === 'dari_pembesaran') {
      fieldJumlahBurung.style.display = 'block';
      fieldJumlahTelur.style.display = 'none';
      fieldJenisKelamin.style.display = 'block';
      fieldUmurBerat.style.display = 'none'; // Hide main umur/berat fields
      fieldFertilTelur.style.display = 'none';
      fieldBeratRataTelur.style.display = 'none';
      if (fieldInfoPembesaran) fieldInfoPembesaran.style.display = 'block';

      // Auto-fill values from selected pembesaran
      autoFillFromPembesaran();

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
      // produksi_sumber_id is not required
      if (produksiIdField) {
        produksiIdField.required = false;
        produksiIdField.removeAttribute('required');
        produksiIdField.disabled = true;
        produksiIdField.value = '';
        produksiIdField.classList.remove('is-invalid');
      }
    } else if (isProduksiTransfer) {
      fieldJumlahBurung.style.display = 'none';
      fieldJumlahTelur.style.display = 'block';
      fieldJenisKelamin.style.display = 'none';
      fieldUmurBerat.style.display = 'none';
      fieldFertilTelur.style.display = 'block';
      fieldBeratRataTelur.style.display = 'block';
      if (fieldInfoPembesaran) fieldInfoPembesaran.style.display = 'none';

      // Set required attributes
      jumlahBurungField.required = false;
      jumlahTelurField.required = false;
      persentaseFertilField.required = false;
      beratRataTelurField.required = false;
      jenisKelaminRadios.forEach(radio => radio.required = false);

      if (jumlahTelurField) {
        jumlahTelurField.readOnly = true;
        jumlahTelurField.classList.add('bg-light');
        jumlahTelurField.setAttribute('title', 'Jumlah telur diambil otomatis dari produksi puyuh terpilih');
      }

      if (pembesaranIdField) {
        pembesaranIdField.required = false;
        pembesaranIdField.removeAttribute('required');
        pembesaranIdField.disabled = true;
        pembesaranIdField.value = '';
        pembesaranIdField.classList.remove('is-invalid');
      }

      if (produksiIdField) {
        if (selected === 'dari_produksi') {
          produksiIdField.required = true;
          produksiIdField.setAttribute('required', 'required');
          produksiIdField.disabled = false;
        } else {
          produksiIdField.required = false;
          produksiIdField.removeAttribute('required');
          produksiIdField.disabled = true;
        }
      }
    }

    // Update dynamic titles
    const dynamicTitles = document.querySelectorAll('.dynamic');
    dynamicTitles.forEach(title => {
      title.classList.remove('manual', 'pembesaran', 'produksi');
      if (selected === 'manual') title.classList.add('manual');
      else if (selected === 'dari_pembesaran') title.classList.add('pembesaran');
      else if (isProduksiTransfer) title.classList.add('produksi');
    });

    // Update field hints and visibility based on jenis_input
    updateFieldHints(selected);

    // Update required asterisks in labels
    updateRequiredLabels();

    // regenerate batch id when jenis_input changes
    generateBatchId(true);
    enforceCapacityLimit(false);
  }

  function updateRequiredLabels() {
    // Get field elements
    const jumlahBurungField = document.getElementById('jumlah_burung');
    const jumlahTelurField = document.getElementById('jumlah_telur');
    const persentaseFertilField = document.getElementById('persentase_fertil');
    const beratRataTelurField = document.getElementById('berat_rata_telur');
    const jenisKelaminRadios = document.querySelectorAll('input[name="jenis_kelamin"]');
    const pembesaranIdField = document.getElementById('pembesaran_id');
    const produksiIdField = document.getElementById('produksi_sumber_id');

    // Get label elements
    const jumlahBurungLabel = document.querySelector('label[for="jumlah_burung"]');
    const jumlahTelurLabel = document.querySelector('label[for="jumlah_telur"]');
    const persentaseFertilLabel = document.querySelector('label[for="persentase_fertil"]');
    const beratRataTelurLabel = document.querySelector('label[for="berat_rata_telur"]');
    const pembesaranIdLabel = document.querySelector('label[for="pembesaran_id"]');
    const produksiIdLabel = document.querySelector('label[for="produksi_sumber_id"]');

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

    if (produksiIdLabel) {
      const baseText = 'Pilih Produksi Puyuh';
      produksiIdLabel.innerHTML = baseText + (produksiIdField && produksiIdField.required ? ' <span class="required">*</span>' : '');
    }

    // Update jenis kelamin label based on whether it's required
    if (jenisKelaminLabel) {
      const baseText = 'Jenis Kelamin Puyuh';
      const isRequired = Array.from(jenisKelaminRadios).some(radio => radio.required);
      jenisKelaminLabel.innerHTML = baseText + (isRequired ? ' <span class="required">*</span>' : '');
    }
  }

  function updateHargaLabel(jenisInput) {
    const hargaLabel = document.getElementById('harga_label');
    const hargaHintManual = document.getElementById('harga_hint_manual');
    const hargaHintProduksi = document.getElementById('harga_hint_produksi');

    let isForEggs = false;

    if (jenisInput === 'dari_produksi') {
      isForEggs = true;
    } else if (jenisInput === 'manual') {
      const fokus = document.querySelector('input[name="fokus_manual"]:checked').value;
      if (fokus === 'telur') {
        isForEggs = true;
      }
    }

    if (isForEggs) {
      // For eggs (telur)
      hargaLabel.textContent = 'Harga per Butir';
      hargaHintManual.style.display = 'none';
      if (hargaHintProduksi) {
        hargaHintProduksi.style.display = 'block';
      }
    } else {
      // For quail (puyuh)
      hargaLabel.textContent = 'Harga per Ekor';
      hargaHintManual.style.display = 'block';
      if (hargaHintProduksi) {
        hargaHintProduksi.style.display = 'none';
      }
    }
  }

  function updateFieldHints(jenisInput) {
    // Hide all hints first
    document.querySelectorAll('.field-hint-manual, .field-hint-pembesaran, .field-hint-produksi').forEach(hint => {
      hint.style.display = 'none';
    });

    // Show relevant hints
    const manualHints = document.querySelectorAll('.field-hint-manual');
    const pembesaranHints = document.querySelectorAll('.field-hint-pembesaran');
    const produksiHints = document.querySelectorAll('.field-hint-produksi');

    if (jenisInput === 'manual') {
      manualHints.forEach(hint => hint.style.display = 'block');
    } else if (jenisInput === 'dari_pembesaran') {
      pembesaranHints.forEach(hint => hint.style.display = 'block');
    } else if (jenisInput === 'dari_produksi') {
      produksiHints.forEach(hint => hint.style.display = 'block');
    }

    // Update harga label and hint based on jenis_input
    updateHargaLabel(jenisInput);

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
    @if(session('success'))
    triggerFlashToast('success', 'Berhasil!', @json(session('success')));
    @endif

    @if(session('error'))
    triggerFlashToast('error', 'Gagal!', @json(session('error')));
    @endif

    const tanggalMulaiField = document.getElementById('tanggal_mulai');
    const today = new Date().toISOString().split('T')[0];
    if (tanggalMulaiField && !tanggalMulaiField.value) {
      tanggalMulaiField.value = today;
      tanggalMulaiField.dispatchEvent(new Event('input'));
    }

    // Generate initial batch ID (only when empty)
    generateBatchId(false);

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
      jumlahBurungField.addEventListener('input', () => {
        validateCampuranCountsRealtime();
        enforceCapacityLimit(true);
      });
    }
    if (jumlahJantanField) {
      jumlahJantanField.addEventListener('input', validateCampuranCountsRealtime);
    }
    if (jumlahBetinaField) {
      jumlahBetinaField.addEventListener('input', validateCampuranCountsRealtime);
    }

    // Update batch ID when tanggal_mulai changes
    if (tanggalMulaiField) {
      tanggalMulaiField.addEventListener('change', () => {
        generateBatchId(true);
        tanggalMulaiField.dispatchEvent(new Event('input'));
        validateTanggalAkhir();
      });
    }

    initializeDatePlaceholders();

    // Store pembesaran data for auto-fill
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
      enforceCapacityLimit(true);
      // Save form state
      saveFormState();
    });

    const produksiSumberSelect = document.getElementById('produksi_sumber_id');
    if (produksiSumberSelect) {
      produksiSumberSelect.addEventListener('change', function() {
        autoFillFromProduksi();
        saveFormState();
      });

      // Trigger once on load to sync initial values
      autoFillFromProduksi();
    }

    // Attach change listener for kandang_id
    document.getElementById('kandang_id').addEventListener('change', saveFormState);

    // Attach change listener for status
    document.getElementById('status').addEventListener('change', saveFormState);

    // Add date validation for tanggal_akhir (but don't save state on input)
    const tanggalAkhirField = document.getElementById('tanggal_akhir');
    if (tanggalAkhirField) {
      tanggalAkhirField.addEventListener('change', function() {
        validateTanggalAkhir();
        // Don't save form state for input field changes
      });
    }

    // Initialize UI state after a short delay to ensure radio buttons are set
    setTimeout(function() {
      // For fresh loads without validation errors, clear all input values but keep tab selection state
      if (!hasValidationErrors) {
        clearInputValuesKeepState();
      }

      // First try to restore from localStorage (only control states)
      const restoredFromStorage = !hasValidationErrors && restoreFormState();

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

  function performFormReset() {
    document.getElementById('produksiForm').reset();

    try {
      localStorage.removeItem(FORM_STATE_KEY);
    } catch (e) {
      console.warn('Failed to clear form state from localStorage:', e);
    }

    const today = new Date().toISOString().split('T')[0];
    const tanggalMulaiField = document.getElementById('tanggal_mulai');
    tanggalMulaiField.value = today;
    tanggalMulaiField.dispatchEvent(new Event('input'));

    document.querySelectorAll('.field-auto-fill').forEach(field => {
      field.classList.remove('auto-filled');
    });

    document.getElementById('umur_burung_pembesaran').value = '';
    document.getElementById('berat_rata_burung_pembesaran').value = '';

    const fieldInfoPembesaran = document.getElementById('field_info_pembesaran_container');
    if (fieldInfoPembesaran) {
      fieldInfoPembesaran.style.display = 'none';
    }

    document.getElementById('campuranValidationAlert').style.display = 'none';
    document.getElementById('campuranSuccessAlert').style.display = 'none';
    document.getElementById('campuranErrorAlert').style.display = 'none';

    document.getElementById('persentase_fertil').value = '';
    document.getElementById('catatan').value = '';
    document.getElementById('jumlah_jantan').value = '';
    document.getElementById('jumlah_betina').value = '';
    document.getElementById('jumlah_jantan').removeAttribute('name');
    document.getElementById('jumlah_betina').removeAttribute('name');

    toggleSections();
    toggleCampuranFields();
    generateBatchId(true);
    validateTanggalAkhir();
    saveFormState();
    updateCapacityInfo();
    enforceCapacityLimit(false);

    triggerFlashToast('success', 'Berhasil!', 'Form produksi berhasil direset.', 2200);
  }

  function resetForm() {
    Swal.fire({
      title: 'Reset Form?',
      text: 'Semua data yang telah diisi akan dibersihkan.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#f59e0b',
      cancelButtonColor: '#6b7280',
      confirmButtonText: 'Ya, Reset',
      cancelButtonText: 'Batal'
    }).then(result => {
      if (result.isConfirmed) {
        performFormReset();
      }
    });
  }
</script>