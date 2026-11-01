@extends('layout.app')

@section('title', 'Manajemen Projek')
@section('page_title', 'Kelola Data Projek')

@section('content')
<div class="space-y-6" x-data="{ createModal: false }">

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
        <div>
            <h3 class="text-base font-bold text-slate-900">Daftar Seluruh Projek</h3>
            <p class="text-xs text-slate-500">Kelola informasi proyek, klien, dan periode pengerjaan lapangan.</p>
        </div>
        <button @click="createModal = true" class="px-4 py-2.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white text-xs font-bold shadow-xs transition flex items-center gap-2">
            <span>➕</span>
            <span>Tambah Projek Baru</span>
        </button>
    </div>

    <!-- Table List -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 uppercase tracking-wider font-semibold">
                    <tr>
                        <th class="px-5 py-3.5">Kode & Nama Projek</th>
                        <th class="px-5 py-3.5">Klien</th>
                        <th class="px-5 py-3.5">Periode</th>
                        <th class="px-5 py-3.5">Tim & GPS</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($projects as $item)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-5 py-4">
                                <span class="font-mono text-[10px] font-bold text-primary-600 bg-primary-50 px-2 py-0.5 rounded">
                                    {{ $item->code }}
                                </span>
                                <p class="font-bold text-slate-900 mt-1">{{ $item->name }}</p>
                                <p class="text-[11px] text-slate-400 line-clamp-1">{{ $item->description }}</p>
                            </td>
                            <td class="px-5 py-4 font-medium text-slate-700">
                                {{ $item->client_name ?? '-' }}
                            </td>
                            <td class="px-5 py-4 text-slate-600 text-[11px]">
                                {{ $item->start_date ? $item->start_date->format('d M Y') : '-' }} s/d<br>
                                {{ $item->end_date ? $item->end_date->format('d M Y') : '-' }}
                            </td>
                            <td class="px-5 py-4">
                                <span class="font-semibold text-slate-800">{{ $item->users_count }} Teknisi</span>
                                <p class="text-[11px] text-slate-400">{{ $item->attendance_points_count }} Titik GPS</p>
                            </td>
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold 
                                    @if($item->status === 'aktif') bg-emerald-100 text-emerald-700
                                    @elseif($item->status === 'selesai') bg-blue-100 text-blue-700
                                    @else bg-amber-100 text-amber-700 @endif">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <form action="{{ route('admin.projects.destroy', $item) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus projek ini?');" class="inline">
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
                            <td colspan="6" class="p-8 text-center text-xs text-slate-400">
                                Belum ada data projek yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($projects->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $projects->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Create Projek -->
    <div x-show="createModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.outside="createModal = false" class="bg-white rounded-2xl max-w-lg w-full p-6 space-y-4 shadow-xl border border-slate-100">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-sm font-bold text-slate-900">Tambah Projek Baru</h3>
                <button @click="createModal = false" class="text-slate-400 hover:text-slate-600 text-base">✕</button>
            </div>
            
            <form action="{{ route('admin.projects.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Kode Projek *</label>
                        <input type="text" name="code" required placeholder="PRJ-2026-002"
                            class="w-full rounded-xl border border-slate-300 p-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Status Projek *</label>
                        <select name="status" required class="w-full rounded-xl border border-slate-300 p-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="aktif">Aktif</option>
                            <option value="tertunda">Tertunda</option>
                            <option value="selesai">Selesai</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nama Projek *</label>
                    <input type="text" name="name" required placeholder="Instalasi BTS & Transmisi"
                        class="w-full rounded-xl border border-slate-300 p-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nama Klien / Perusahaan</label>
                    <input type="text" name="client_name" placeholder="PT Telekomunikasi Indonesia"
                        class="w-full rounded-xl border border-slate-300 p-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Tanggal Mulai</label>
                        <input type="date" name="start_date" value="{{ now()->toDateString() }}"
                            class="w-full rounded-xl border border-slate-300 p-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Tanggal Selesai</label>
                        <input type="date" name="end_date" value="{{ now()->addMonths(3)->toDateString() }}"
                            class="w-full rounded-xl border border-slate-300 p-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Deskripsi Projek</label>
                    <textarea name="description" rows="2" placeholder="Cakupan pekerjaan proyek lapangan..."
                        class="w-full rounded-xl border border-slate-300 p-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="createModal = false" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 font-semibold hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-primary-600 text-white font-bold hover:bg-primary-700 shadow-xs">
                        Simpan Projek
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

