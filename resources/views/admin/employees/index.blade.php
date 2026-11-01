@extends('layout.app')

@section('title', 'Manajemen Karyawan')
@section('page_title', 'Kelola Pengguna & Karyawan')

@section('content')
<div class="space-y-6" x-data="{ createModal: false }">

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
        <div>
            <h3 class="text-base font-bold text-slate-900">Daftar Pengguna Sistem</h3>
            <p class="text-xs text-slate-500">Atur hak akses pengguna (Admin, Project Manager, Teknisi / Karyawan).</p>
        </div>
        <button @click="createModal = true" class="px-4 py-2.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white text-xs font-bold shadow-xs transition flex items-center gap-2">
            <span>➕</span>
            <span>Tambah Karyawan Baru</span>
        </button>
    </div>

    <!-- Table Users -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 uppercase tracking-wider font-semibold">
                    <tr>
                        <th class="px-5 py-3.5">Nama & Email</th>
                        <th class="px-5 py-3.5">Role Akses</th>
                        <th class="px-5 py-3.5">Departemen</th>
                        <th class="px-5 py-3.5">No. Telepon</th>
                        <th class="px-5 py-3.5">Status Akun</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($employees as $user)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-9 w-9 rounded-xl bg-primary-100 text-primary-700 font-bold text-xs flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900">{{ $user->name }}</p>
                                        <p class="text-[11px] text-slate-400">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider 
                                    @if($user->isAdmin()) bg-rose-100 text-rose-700
                                    @elseif($user->isManager()) bg-indigo-100 text-indigo-700
                                    @else bg-emerald-100 text-emerald-700 @endif">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td class="px-5 py-4 font-medium text-slate-700">
                                {{ $user->department ?? '-' }}
                            </td>
                            <td class="px-5 py-4 font-mono text-[11px] text-slate-600">
                                {{ $user->phone ?? '-' }}
                            </td>
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $user->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                    {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                @if($user->id !== auth()->id())
                                    <form action="{{ route('admin.employees.destroy', $user) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Hapus">
                                            🗑️
                                        </button>
                                    </form>
                                @else
                                    <span class="text-[10px] text-slate-400 italic">Akun Anda</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-xs text-slate-400">
                                Belum ada data pengguna yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($employees->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $employees->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Tambah Karyawan -->
    <div x-show="createModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.outside="createModal = false" class="bg-white rounded-2xl max-w-lg w-full p-6 space-y-4 shadow-xl border border-slate-100">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-sm font-bold text-slate-900">Tambah Akun Karyawan Baru</h3>
                <button @click="createModal = false" class="text-slate-400 hover:text-slate-600 text-base">✕</button>
            </div>
            
            <form action="{{ route('admin.employees.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nama Lengkap *</label>
                    <input type="text" name="name" required placeholder="Budi Pratama"
                        class="w-full rounded-xl border border-slate-300 p-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Alamat Email *</label>
                        <input type="email" name="email" required placeholder="budi@technodrives.com"
                            class="w-full rounded-xl border border-slate-300 p-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Kata Sandi Awal *</label>
                        <input type="password" name="password" required placeholder="Min 8 karakter"
                            class="w-full rounded-xl border border-slate-300 p-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Role Akses *</label>
                        <select name="role" required class="w-full rounded-xl border border-slate-300 p-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="karyawan">Karyawan / Teknisi Lapangan</option>
                            <option value="manager">Project Manager</option>
                            <option value="admin">Administrator / HR</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Departemen</label>
                        <input type="text" name="department" placeholder="Field Engineering"
                            class="w-full rounded-xl border border-slate-300 p-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nomor WhatsApp / HP</label>
                        <input type="text" name="phone" placeholder="081234567890"
                            class="w-full rounded-xl border border-slate-300 p-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div class="flex items-center pt-5">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" checked class="h-4 w-4 rounded text-primary-600 focus:ring-primary-500">
                            <span class="font-semibold text-slate-700">Akun Aktif</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="createModal = false" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 font-semibold hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-primary-600 text-white font-bold hover:bg-primary-700 shadow-xs">
                        Simpan Akun
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

