<div class="modal fade" id="modalAlasanTolak" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Alasan Penolakan Izin</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-info small mb-3">
          <strong id="namaSiswaTolak"></strong>
        </div>
        <form id="formAlasanTolak">
          <div class="mb-3">
            <label for="alasanTolakGuru" class="form-label">Masukkan Alasan Penolakan <span class="text-danger">*</span></label>
            <textarea class="form-control" id="alasanTolakGuru" name="alasan" rows="4"
                      placeholder="Contoh: Dokumen tidak lengkap..." required></textarea>
            <small class="text-muted d-block mt-2">Alasan ini akan dikirimkan ke orang tua siswa</small>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-danger" id="btnKonfirmasiTolak">Tolak Izin</button>
      </div>
    </div>
  </div>
</div>
