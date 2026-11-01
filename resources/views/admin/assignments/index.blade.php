@extends('layout.app')

@section('title', 'Penugasan Tim')
@section('page_title', 'Penugasan Karyawan ke Projek')

@section('content')
<div class="space-y-6" x-data="{ createModal: false }">

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
        <div>
            <h3 class="text-base font-bold text-slate-900">Daftar Penugasan Tim Projek</h3>
            <p class="text-xs text-slate-500">Petakan karyawan dan teknisi ke projek aktif agar dapat melakukan presensi di lokasi.</p>
        </div>
        <button @click="createModal = true" class="px-4 py-2.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white text-xs font-bold shadow-xs transition flex items-center gap-2">
            <span>➕</span>
            <span>Tugaskan Karyawan</span>
        </button>
    </div>

    <!-- Table Assignments -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 uppercase tracking-wider font-semibold">
                    <tr>
                        <th class="px-5 py-3.5">Nama Karyawan</th>
                        <th class="px-5 py-3.5">Projek Ditugaskan</th>
                        <th class="px-5 py-3.5">Peran Dalam Projek</th>
                        <th class="px-5 py-3.5">Tanggal Penugasan</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($assignments as $item)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-5 py-4">
                                <p class="font-bold text-slate-900">{{ $item->user?->name }}</p>
                                <p class="text-[11px] text-slate-400">{{ $item->user?->department ?? 'Teknisi' }}</p>
                            </td>
                            <td class="px-5 py-4 font-semibold text-primary-600">
                                {{ $item->projek?->name }}
                            </td>
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $item->role_in_project === 'leader' ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-700' }}">
                                    {{ ucfirst($item->role_in_project) }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-slate-600 font-mono text-[11px]">
                                {{ $item->assigned_at ? $item->assigned_at->format('d M Y') : '-' }}
                            </td>
                            <td class="px-5 py-4 text-right">
                                <form action="{{ route('admin.assignments.destroy', $item) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan penugasan ini?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Hapus">
                                        🗑️
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-xs text-slate-400">
                                Belum ada karyawan yang ditugaskan ke projek.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($assignments->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $assignments->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Penugasan -->
    <div x-show="createModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.outside="createModal = false" class="bg-white rounded-2xl max-w-lg w-full p-6 space-y-4 shadow-xl border border-slate-100">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-sm font-bold text-slate-900">Penugasan Karyawan ke Projek</h3>
                <button @click="createModal = false" class="text-slate-400 hover:text-slate-600 text-base">✕</button>
            </div>
            
            <form action="{{ route('admin.assignments.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Pilih Projek *</label>
                    <select name="projek_id" required class="w-full rounded-xl border border-slate-300 p-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="">Pilih Projek</option>
                        @foreach($projects as $proj)
                            <option value="{{ $proj->id }}">{{ $proj->name }} ({{ $proj->code }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Pilih Karyawan *</label>
                    <select name="user_id" required class="w-full rounded-xl border border-slate-300 p-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="">Pilih Pengguna / Karyawan</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->role }} - {{ $u->department ?? 'Staff' }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Peran Dalam Projek *</label>
                        <select name="role_in_project" required class="w-full rounded-xl border border-slate-300 p-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="member">Member (Teknisi)</option>
                            <option value="leader">Project Leader</option>
                            <option value="supervisor">Supervisor</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Tanggal Mulai Tugas *</label>
                        <input type="date" name="assigned_at" value="{{ now()->toDateString() }}" required
                            class="w-full rounded-xl border border-slate-300 p-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="createModal = false" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 font-semibold hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-primary-600 text-white font-bold hover:bg-primary-700 shadow-xs">
                        Simpan Penugasan
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

