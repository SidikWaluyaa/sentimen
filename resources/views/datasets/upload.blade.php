<x-app-layout>
    <div class="max-w-5xl mx-auto space-y-6">

        <!-- Header -->
        <div class="pb-2">
            <a href="{{ route('dashboard') }}" class="text-xs font-semibold text-shopee-light hover:underline inline-flex items-center gap-1 mb-2">
                &larr; Kembali ke Dashboard
            </a>
            <h1 class="text-3xl font-extrabold text-white text-gradient-shopee">Upload Dataset Ulasan Baru</h1>
            <p class="text-gray-400 text-sm mt-1">Unggah berkas spreadsheet Excel (.xlsx) atau CSV untuk menjalankan klasifikasi sentimen batch otomatis.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Form Card (2 Cols) -->
            <div class="md:col-span-2 glass-panel rounded-3xl p-6 sm:p-8 space-y-6">
                <form method="POST" action="{{ route('datasets.store') }}" enctype="multipart/form-data" class="space-y-6" x-data="{ fileName: '' }">
                    @csrf

                    <!-- Judul Dataset -->
                    <div>
                        <label for="judul_dataset" class="block text-xs font-bold uppercase tracking-wider text-gray-300 mb-2">
                            Nama / Judul Dataset <span class="text-rose-400">*</span>
                        </label>
                        <input type="text" id="judul_dataset" name="judul_dataset" value="{{ old('judul_dataset') }}"
                               placeholder="Contoh: Evaluasi Ulasan ShopeeFood Q1 2026"
                               required
                               class="glass-input w-full rounded-xl px-4 py-3 text-sm text-white focus:ring-shopee">
                        @error('judul_dataset')
                            <p class="text-rose-400 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- File Drop Area -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-300 mb-2">
                            Pilih Berkas Excel atau CSV <span class="text-rose-400">*</span>
                        </label>

                        <div class="relative border-2 border-dashed border-white/20 hover:border-shopee/50 transition-colors rounded-2xl p-8 text-center bg-cosmos-800/40">
                            <input type="file" id="file_dataset" name="file_dataset" accept=".xlsx,.csv" required
                                   @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''"
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                            <div class="space-y-2 pointer-events-none">
                                <div class="w-12 h-12 rounded-xl bg-shopee/10 text-shopee-light border border-shopee/30 flex items-center justify-center mx-auto">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                </div>
                                <p class="text-sm font-semibold text-white">
                                    <span class="text-shopee-light">Klik untuk memilih</span> atau seret berkas ke sini
                                </p>
                                <p class="text-xs text-gray-400">Mendukung format: <strong class="text-gray-300">.xlsx, .csv</strong> (Maksimal 20 MB)</p>

                                <div x-show="fileName" class="pt-2">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-emerald-500/20 text-emerald-300 text-xs font-medium border border-emerald-500/30">
                                        📄 Berkas terpilih: <span x-text="fileName"></span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        @error('file_dataset')
                            <p class="text-rose-400 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button type="submit" class="btn-shopee w-full py-3.5 rounded-xl text-sm font-bold flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Mulai Proses Analisis Sentimen
                        </button>
                    </div>
                </form>
            </div>

            <!-- Guidelines Card (1 Col) -->
            <div class="glass-panel rounded-3xl p-6 space-y-4">
                <h3 class="text-sm font-bold uppercase tracking-wider text-gray-300 flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Aturan Format Data
                </h3>

                <ul class="text-xs text-gray-400 space-y-2.5">
                    <li class="flex items-start gap-2">
                        <span class="text-shopee-light font-bold">•</span>
                        <span>Dataset <strong>wajib</strong> memiliki kolom bernama <code class="text-white bg-white/10 px-1 py-0.5 rounded">ulasan</code> dan <code class="text-white bg-white/10 px-1 py-0.5 rounded">rating</code> (huruf kecil/besar tidak sensitif).</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-shopee-light font-bold">•</span>
                        <span>Kolom opsional yang didukung: <code class="text-gray-300">id_review</code>, <code class="text-gray-300">nama_pengguna</code>, <code class="text-gray-300">relevan_shopeefood</code>.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-shopee-light font-bold">•</span>
                        <span>Sistem otomatis <strong>menghapus rating 3 (netral)</strong> sesuai standarisasi model biner Naive Bayes.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-shopee-light font-bold">•</span>
                        <span>Rating 1 dan 2 dikategorikan sebagai <strong class="text-rose-400">Negatif</strong>, sedangkan rating 4 dan 5 dikategorikan sebagai <strong class="text-emerald-400">Positif</strong>.</span>
                    </li>
                </ul>

                <div class="pt-3 border-t border-white/5">
                    <p class="text-[11px] text-gray-500">
                        ⚡ Didukung oleh Pure PHP Inference Engine dengan stemmer Sastrawi & vectorizer TF-IDF yang telah diuji akurasinya.
                    </p>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
