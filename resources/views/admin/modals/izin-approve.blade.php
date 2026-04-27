<div class="modal fade" id="modalKonfirmasiSetujui" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title">Konfirmasi Persetujuan Izin</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-warning small mb-3">
          <i class="fas fa-exclamation-triangle me-2"></i>
          <strong>Apakah Anda yakin ingin menyetujui izin ini?</strong>
        </div>
        <div class="alert alert-info small">
          <strong id="namaSiswaSetujui"></strong>
        </div>
        <p class="small text-muted mb-0">Setelah disetujui, izin tidak dapat dibatalkan.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-success" id="btnKonfirmasiSetujui">Ya, Setujui</button>
      </div>
    </div>
  </div>
</div>