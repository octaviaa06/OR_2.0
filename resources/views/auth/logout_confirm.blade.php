<form method="POST" action="{{ route('logout.process') }}" class="d-grid gap-3 px-4">
    @csrf
    <input type="hidden" name="from" value="{{ $from }}">

    <button type="submit" name="confirm_logout" class="btn btn-keluar text-white">
        Ya, Keluar Sekarang
    </button>

    <button type="submit" name="cancel_logout" class="btn btn-batal">
        Batal, Kembali
    </button>
</form>