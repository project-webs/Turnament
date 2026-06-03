@extends('layouts.app')

@section('title', 'Manajemen User')
@section('page-title', 'Manajemen User')

@section('topbar-actions')
    <a href="{{ route('users.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-user-plus"></i> Tambah User
    </a>
@endsection

@section('content')
    <div class="card" style="margin-bottom:24px">
        <form method="GET" action="{{ route('users.index') }}" style="display:flex;gap:12px;align-items:center">
            <div style="position:relative;flex:1;max-width:400px">
                <i class="fa-solid fa-search" style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--text-muted)"></i>
                <input type="text" name="search" class="form-control" placeholder="Cari nama atau email..." value="{{ request('search') }}" style="padding-left:40px">
            </div>
            <button type="submit" class="btn btn-secondary">Cari</button>
            @if(request('search'))
                <a href="{{ route('users.index') }}" class="btn btn-secondary" style="border:none;background:none;color:var(--text-muted)">Reset</a>
            @endif
        </form>
    </div>

    <div class="card">
        @if($users->count() > 0)
            <div style="overflow-x:auto">
                <table style="width:100%;border-collapse:collapse;font-size:14px;min-width:600px">
                    <thead>
                        <tr style="border-bottom:2px solid var(--border);color:var(--text-muted);text-align:left">
                            <th style="padding:16px;font-weight:600;width:35%">Nama</th>
                            <th style="padding:16px;font-weight:600;width:40%">Email</th>
                            <th style="padding:16px;font-weight:600;text-align:right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr style="border-bottom:1px solid var(--border)">
                            <td style="padding:16px;font-weight:600;color:var(--text-primary)">
                                {{ $user->name }}
                                @if(Auth::id() === $user->id)
                                    <span class="badge badge-green" style="margin-left:8px;font-size:10px">Anda</span>
                                @endif
                            </td>
                            <td style="padding:16px;color:var(--text-secondary)">
                                {{ $user->email }}
                            </td>
                            <td style="padding:16px;text-align:right">
                                <div style="display:flex;gap:8px;justify-content:flex-end">
                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-secondary btn-icon" title="Edit">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    @if(Auth::id() !== $user->id)
                                    <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini? Seluruh data yang terkait dengan user ini juga akan ikut terhapus.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-icon" title="Hapus">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="margin-top:24px">
                {{ $users->links('pagination::bootstrap-5') }}
            </div>
        @else
            <div class="empty-state">
                <i class="fa-solid fa-users-slash"></i>
                <h3>User Tidak Ditemukan</h3>
                <p>Data user tidak ditemukan dengan kata kunci tersebut.</p>
                <a href="{{ route('users.create') }}" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i> Tambah User Baru
                </a>
            </div>
        @endif
    </div>
@endsection
