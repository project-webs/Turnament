@extends('layouts.app')

@section('title', 'Data Iuran Pemain')
@section('page-title', 'Data Iuran Pemain')

@section('topbar-actions')
    <div style="display:flex;gap:12px;align-items:center">
        <a href="{{ route('iuran.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Tambah Iuran
        </a>
    </div>
@endsection

@section('content')
    <div class="card" style="margin-bottom:24px">
        <form method="GET" action="{{ route('iuran.index') }}" style="display:flex;gap:12px;align-items:center">
            <div style="position:relative;flex:1;max-width:400px">
                <i class="fa-solid fa-search" style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--text-muted)"></i>
                <input type="text" name="search" class="form-control" placeholder="Cari nama pemain..." value="{{ request('search') }}" style="padding-left:40px">
            </div>
            <button type="submit" class="btn btn-secondary">Cari</button>
            @if(request('search'))
                <a href="{{ route('iuran.index') }}" class="btn btn-secondary" style="border:none;background:none;color:var(--text-muted)">Reset</a>
            @endif
        </form>
    </div>

    <div class="card">
        @if($iurans->count() > 0)
            <div style="overflow-x:auto">
                <table style="width:100%;border-collapse:collapse;font-size:14px;min-width:600px">
                    <thead>
                        <tr style="border-bottom:2px solid var(--border);color:var(--text-muted);text-align:left">
                            <th style="padding:16px;font-weight:600;width:15%">Tanggal</th>
                            <th style="padding:16px;font-weight:600;width:25%">Nama Pemain</th>
                            <th style="padding:16px;font-weight:600;width:15%">Periode</th>
                            <th style="padding:16px;font-weight:600;width:20%">Nominal</th>
                            <th style="padding:16px;font-weight:600;width:15%">Catatan</th>
                            <th style="padding:16px;font-weight:600;text-align:right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($iurans as $iuran)
                        <tr style="border-bottom:1px solid var(--border)">
                            <td style="padding:16px;color:var(--text-secondary)">
                                {{ \Carbon\Carbon::parse($iuran->tanggal)->format('d M Y') }}
                            </td>
                            <td style="padding:16px;font-weight:600;color:var(--text-primary)">
                                {{ $iuran->player->name ?? '-' }}
                            </td>
                            <td style="padding:16px;color:var(--text-secondary)">
                                <span class="badge badge-blue">{{ \Carbon\Carbon::parse($iuran->period)->translatedFormat('F Y') }}</span>
                            </td>
                            <td style="padding:16px;font-weight:600;color:var(--green)">
                                Rp {{ number_format($iuran->amount, 0, ',', '.') }}
                            </td>
                            <td style="padding:16px;color:var(--text-secondary);font-size:12px">
                                {{ Str::limit($iuran->notes, 30) }}
                            </td>
                            <td style="padding:16px;text-align:right">
                                <div style="display:flex;gap:8px;justify-content:flex-end">
                                    <a href="{{ route('iuran.edit', $iuran) }}" class="btn btn-secondary btn-icon" title="Edit">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <form method="POST" action="{{ route('iuran.destroy', $iuran) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data iuran ini?')">
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
                {{ $iurans->links('pagination::bootstrap-5') }}
            </div>
        @else
            <div class="empty-state">
                <i class="fa-solid fa-money-bill-wave"></i>
                <h3>Belum Ada Data Iuran</h3>
                <p>Anda belum mencatat pembayaran iuran dari pemain manapun.</p>
                <a href="{{ route('iuran.create') }}" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i> Tambah Iuran
                </a>
            </div>
        @endif
    </div>
@endsection
