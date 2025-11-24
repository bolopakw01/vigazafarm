<!-- Section Sumber Produksi -->
<div id="produksiSourceSection" class="section-box" style="display:none;">
  <h6 class="section-title produksi">Data Dari Produksi Puyuh</h6>
  <p class="text-muted mb-3">
    <i class="fa-solid fa-info-circle me-1"></i>
    Ambil stok telur langsung dari produksi puyuh yang masih aktif. Jumlah telur akan otomatis terintegrasi dengan pencatatan produksi puyuh yang dipilih.
  </p>
  <div class="row g-3">
    <div class="col-md-12">
      <label for="produksi_sumber_id" class="form-label">Pilih Produksi Puyuh</label>
      <select id="produksi_sumber_id" name="produksi_sumber_id" class="form-select">
        <option value="">Pilih Produksi Aktif</option>
        @forelse($produksiSumberList as $produksi)
          @php
            $telurSiap = max((int) ($produksi->total_telur_tersedia ?? 0), 0);
            $disabledAttr = $telurSiap <= 0 ? 'disabled' : '';
            $formattedReady = number_format($telurSiap, 0, ',', '.');
            $batchLabel = $produksi->batch_produksi_id ?? 'Tanpa Kode';
            $kandangLabel = $produksi->kandang->nama_kandang ?? '-';
            $tanggalMulai = $produksi->tanggal_mulai ? \Carbon\Carbon::parse($produksi->tanggal_mulai)->format('Y-m-d') : '';
          @endphp
          <option value="{{ $produksi->id }}"
                  data-batch="{{ $batchLabel }}"
                  data-kandang="{{ $kandangLabel }}"
                  data-tanggal-mulai="{{ $tanggalMulai }}"
                  data-telur-tersedia="{{ $telurSiap }}"
                  data-total-tercatat="{{ $produksi->total_telur_tercatat ?? 0 }}"
                  data-total-terpakai="{{ $produksi->total_telur_sudah_dialihkan ?? 0 }}"
                  data-status="{{ $produksi->status }}"
                  {{ $disabledAttr }}
                  {{ old('produksi_sumber_id') == $produksi->id ? 'selected' : '' }}>
            {{ $batchLabel }} • {{ $kandangLabel }} • Telur siap: {{ $formattedReady }} butir
            @if($disabledAttr) (stok habis) @endif
          </option>
        @empty
          <option value="" disabled>Belum ada produksi puyuh aktif dengan stok telur</option>
        @endforelse
      </select>
      <div class="form-text">
        <small class="text-muted">Menampilkan produksi puyuh berstatus <strong>aktif</strong> beserta stok telur yang belum dialihkan.</small>
      </div>
    </div>
  </div>
</div>