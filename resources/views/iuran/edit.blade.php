@extends('layouts.app')

@section('title', 'Edit Iuran')
@section('page-title', 'Edit Data Iuran')

@section('topbar-actions')
    <a href="{{ route('iuran.index') }}" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left"></i> Kembali
    </a>
@endsection

@section('content')
    <div style="max-width:600px">
        <div class="card">
            <div class="card-header" style="border-bottom:1px solid var(--border);padding-bottom:16px;margin-bottom:24px">
                <div class="card-title">Informasi Iuran</div>
            </div>

            <form method="POST" action="{{ route('iuran.update', $iuran) }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="player_id" class="form-label">Nama Pemain <span style="color:var(--red)">*</span></label>
                    <select name="player_id" id="player_id" class="form-control" required>
                        <option value="">-- Pilih Pemain --</option>
                        @foreach($players as $player)
                            <option value="{{ $player->id }}" {{ old('player_id', $iuran->player_id) == $player->id ? 'selected' : '' }}>
                                {{ $player->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('player_id') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="tanggal" class="form-label">Tanggal Pembayaran <span style="color:var(--red)">*</span></label>
                    <input type="date" name="tanggal" id="tanggal" class="form-control" required value="{{ old('tanggal', $iuran->tanggal->format('Y-m-d')) }}">
                    @error('tanggal') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="period" class="form-label">Periode <span style="color:var(--red)">*</span></label>
                    <input type="date" name="period" id="period" class="form-control" required value="{{ old('period', $iuran->period->format('Y-m-d')) }}">
                    @error('period') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="amount" class="form-label">Nominal (Rp) <span style="color:var(--red)">*</span></label>
                    <input type="number" name="amount" id="amount" class="form-control" required value="{{ old('amount', $iuran->amount) }}" placeholder="Contoh: 50000" min="0">
                    @error('amount') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="notes" class="form-label">Catatan (Opsional)</label>
                    <textarea name="notes" id="notes" class="form-control" placeholder="Tambahkan keterangan tambahan jika ada">{{ old('notes', $iuran->notes) }}</textarea>
                    @error('notes') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div style="margin-top:32px;display:flex;justify-content:flex-end;gap:12px">
                    <a href="{{ route('iuran.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save"></i> Perbarui Data
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
