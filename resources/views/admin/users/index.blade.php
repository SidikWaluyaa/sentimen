<x-app-layout>
    <div class="w-full max-w-[1700px] mx-auto space-y-6">

        <!-- Header -->
        <div class="pb-2">
            <a href="{{ route('dashboard') }}" class="text-xs font-semibold text-shopee-light hover:underline inline-flex items-center gap-1 mb-2">
                &larr; Kembali ke Dashboard
            </a>
            <h1 class="text-3xl font-extrabold text-white text-gradient-purple">Manajemen Pengguna Sistem</h1>
            <p class="text-gray-400 text-sm mt-1">Kelola hak akses pengguna, tambahkan analis baru, dan atur peran (Admin / Analis) dalam sistem analisis sentimen.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Add User Form (1 Col) -->
            <div class="glass-panel rounded-3xl p-6 sm:p-8 space-y-4 h-fit">
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    Tambah Pengguna Baru
                </h3>

                <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="name" class="block text-xs font-bold uppercase tracking-wider text-gray-300 mb-1.5">Nama Lengkap</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="Nama pengguna..."
                               class="glass-input w-full rounded-xl px-3.5 py-2.5 text-xs text-white">
                        @error('name') <p class="text-rose-400 text-[11px] mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-bold uppercase tracking-wider text-gray-300 mb-1.5">Alamat Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="email@contoh.id"
                               class="glass-input w-full rounded-xl px-3.5 py-2.5 text-xs text-white">
                        @error('email') <p class="text-rose-400 text-[11px] mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="role" class="block text-xs font-bold uppercase tracking-wider text-gray-300 mb-1.5">Peran / Role</label>
                        <select id="role" name="role" required class="glass-input w-full rounded-xl px-3.5 py-2.5 text-xs text-white">
                            <option value="analyst" {{ old('role') === 'analyst' ? 'selected' : '' }} class="bg-cosmos-800 text-white">Analis Data (Bisa Upload & Analisis)</option>
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }} class="bg-cosmos-800 text-white">Administrator (Akses Penuh)</option>
                        </select>
                        @error('role') <p class="text-rose-400 text-[11px] mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-bold uppercase tracking-wider text-gray-300 mb-1.5">Kata Sandi</label>
                        <input type="password" id="password" name="password" required placeholder="Minimal 8 karakter"
                               class="glass-input w-full rounded-xl px-3.5 py-2.5 text-xs text-white">
                        @error('password') <p class="text-rose-400 text-[11px] mt-1">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" class="w-full py-3 rounded-xl bg-gradient-to-r from-purple-500 to-indigo-600 hover:from-purple-600 hover:to-indigo-700 text-white text-xs font-bold shadow-lg shadow-purple-500/25 transition-all">
                        Simpan Pengguna Baru
                    </button>
                </form>
            </div>

            <!-- Users List Table (2 Cols) -->
            <div class="lg:col-span-2 glass-panel rounded-3xl p-6 sm:p-8 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        Daftar Pengguna Terdaftar
                    </h3>
                    <span class="text-xs text-gray-400 font-mono">Total: {{ $users->total() }} Pengguna</span>
                </div>

                <div class="overflow-x-auto rounded-xl border border-white/5">
                    <table class="w-full text-left text-xs text-gray-300">
                        <thead class="bg-cosmos-800/80 text-[11px] uppercase tracking-wider text-gray-400 border-b border-white/10">
                            <tr>
                                <th class="px-4 py-3">Nama</th>
                                <th class="px-4 py-3">Email</th>
                                <th class="px-4 py-3 text-center">Peran</th>
                                <th class="px-4 py-3 text-center">Terdaftar</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @foreach($users as $user)
                                <tr class="hover:bg-white/[0.02]">
                                    <td class="px-4 py-3 font-semibold text-white">
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-lg bg-gradient-to-tr {{ $user->isAdmin() ? 'from-amber-500 to-rose-500' : 'from-blue-500 to-indigo-500' }} flex items-center justify-center font-bold text-[10px] text-white">
                                                {{ substr($user->name, 0, 2) }}
                                            </div>
                                            <span>{{ $user->name }}</span>
                                            @if(Auth::id() === $user->id)
                                                <span class="text-[9px] text-emerald-400 font-mono">(Anda)</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-gray-400 font-mono">{{ $user->email }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $user->isAdmin() ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/20' }}">
                                            {{ $user->role }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center text-gray-500 whitespace-nowrap">
                                        {{ $user->created_at->translatedFormat('d/m/Y H:i') }} WIB
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if(Auth::id() !== $user->id)
                                            <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" onsubmit="return confirm('Hapus akun pengguna {{ $user->name }}?')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 rounded-lg text-rose-400 hover:bg-rose-500/10 transition-colors" title="Hapus Pengguna">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-gray-600 text-[11px]">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="pt-2">
                    {{ $users->links() }}
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
