@extends('layouts.app')

@section('title', 'Master Data Pemain')
@section('page-title', 'Master Data Pemain')

@section('topbar-actions')
    <div style="display:flex;gap:12px;align-items:center">
        <form method="POST" action="{{ route('players.calculate-rating') }}" onsubmit="return confirm('Apakah Anda yakin ingin menghitung ulang rating dari seluruh pertandingan yang belum terhitung? Pertandingan yang sudah dihitung tidak bisa diedit lagi.')">
            @csrf
            <button type="submit" class="btn btn-secondary" style="color:var(--yellow);border-color:rgba(245,158,11,0.5)" {{ $unprocessedCount == 0 ? 'disabled' : '' }}>
                <i class="fa-solid fa-bolt"></i> Hitung Rating ITR Baru
                @if($unprocessedCount > 0)
                    <span class="badge badge-yellow" style="margin-left:8px;padding:2px 6px;font-size:10px">{{ $unprocessedCount }} baru</span>
                @endif
            </button>
        </form>
        <a href="{{ route('players.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-user-plus"></i> Tambah Pemain
        </a>
    </div>
@endsection

@section('content')
    <div class="card" style="margin-bottom:24px">
        <form method="GET" action="{{ route('players.index') }}" style="display:flex;gap:12px;align-items:center">
            <div style="position:relative;flex:1;max-width:400px">
                <i class="fa-solid fa-search" style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--text-muted)"></i>
                <input type="text" name="search" class="form-control" placeholder="Cari nama Pemain..." value="{{ request('search') }}" style="padding-left:40px">
            </div>
            <button type="submit" class="btn btn-secondary">Cari</button>
            @if(request('search'))
                <a href="{{ route('players.index') }}" class="btn btn-secondary" style="border:none;background:none;color:var(--text-muted)">Reset</a>
            @endif
        </form>
    </div>

    <div class="card">
        @if($players->count() > 0)
            <div style="overflow-x:auto">
                <table style="width:100%;border-collapse:collapse;font-size:14px;min-width:600px">
                    <thead>
                        <tr style="border-bottom:2px solid var(--border);color:var(--text-muted);text-align:left">
                            <th style="padding:16px;font-weight:600;width:30%">Nama Pemain</th>
                            <th style="padding:16px;font-weight:600;width:20%">Info Diri</th>
                            <th style="padding:16px;font-weight:600;width:20%">Divisi</th>
                            <th style="padding:16px;font-weight:600;width:15%">ITR Rating</th>
                            <th style="padding:16px;font-weight:600;text-align:right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($players as $player)
                        <tr style="border-bottom:1px solid var(--border)">
                            <td style="padding:16px;font-weight:600;color:var(--text-primary)">
                                {{ $player->name }}
                            </td>
                            <td style="padding:16px;color:var(--text-secondary)">
                                <div style="font-size:12px;margin-bottom:4px"><i class="fa-solid fa-venus-mars" style="width:14px;text-align:center"></i> {{ $player->gender ?: '-' }}</div>
                                <div style="font-size:12px"><i class="fa-solid fa-id-card" style="width:14px;text-align:center"></i> {{ $player->nik ?: '-' }}</div>
                            </td>
                            <td style="padding:16px;color:var(--text-secondary)">
                                @if($player->division)
                                    <span class="badge badge-gray">{{ $player->division }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td style="padding:16px">
                                <span class="badge badge-blue">
                                    <i class="fa-solid fa-star" style="font-size:10px"></i> {{ $player->itr_rating }}
                                </span>
                            </td>
                            <td style="padding:16px;text-align:right">
                                <div style="display:flex;gap:8px;justify-content:flex-end">
                                    <a href="{{ route('players.edit', $player) }}" class="btn btn-secondary btn-icon" title="Edit">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <form method="POST" action="{{ route('players.destroy', $player) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus Pemain ini? (Tidak akan menghapus riwayat mereka di turnamen yang sudah berjalan)')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-icon" title="Hapus">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="margin-top:24px">
                {{ $players->links('pagination::bootstrap-5') }}
            </div>
        @else
            <div class="empty-state">
                <i class="fa-solid fa-users-slash"></i>
                <h3>Belum Ada Data Pemain</h3>
                <p>Anda belum menambahkan Pemain satupun ke dalam master data.</p>
                <a href="{{ route('players.create') }}" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i> Tambah Pemain Pertama
                </a>
            </div>
        @endif
    </div>
@endsection
