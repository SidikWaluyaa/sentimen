<x-app-layout>
    <div class="max-w-5xl mx-auto space-y-6">

        <!-- Header -->
        <div class="pb-2">
            <a href="{{ route('dashboard') }}" class="text-xs font-semibold text-shopee-light hover:underline inline-flex items-center gap-1 mb-2">
                &larr; Kembali ke Dashboard
            </a>
            <h1 class="text-3xl font-extrabold text-white text-gradient-shopee">Simulasi Prediksi Ulasan Tunggal</h1>
            <p class="text-gray-400 text-sm mt-1">Uji coba instan performa model Naive Bayes dan text preprocessor Sastrawi untuk kalimat keluhan atau testimoni pelanggan.</p>
        </div>

        <!-- Input Box Card -->
        <div class="glass-panel rounded-3xl p-6 sm:p-8 space-y-6">
            <form method="POST" action="{{ route('prediction.single.post') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="ulasan" class="block text-xs font-bold uppercase tracking-wider text-gray-300 mb-2">
                        Ketikkan Kalimat Ulasan / Komentar Pelanggan:
                    </label>
                    <textarea id="ulasan" name="ulasan" rows="4" required
                              placeholder="Masukkan ulasan pelanggan di sini..."
                              class="glass-input w-full rounded-2xl p-4 text-sm text-white focus:ring-shopee">{{ old('ulasan', $inputUlasan) }}</textarea>
                    @error('ulasan')
                        <p class="text-rose-400 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Example Pills -->
                <div class="space-y-1.5">
                    <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Coba Contoh Kalimat:</span>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" onclick="document.getElementById('ulasan').value = 'Driver sangat ramah, makanan cepat sampai dan masih hangat enak banget!'"
                                class="px-2.5 py-1 rounded-lg bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 text-xs border border-emerald-500/20 transition-colors text-left">
                            👍 Driver ramah, makanan cepat sampai enak
                        </button>
                        <button type="button" onclick="document.getElementById('ulasan').value = 'Pesanan dibatalkan sepihak driver tidak mau antar dan saldo terpotong sangat kecewa'"
                                class="px-2.5 py-1 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 text-xs border border-rose-500/20 transition-colors text-left">
                            👎 Pesanan dibatalkan sepihak driver tidak mau antar
                        </button>
                        <button type="button" onclick="document.getElementById('ulasan').value = 'Pengiriman terlambat makanan sudah dingin dan kemasannya tumpah berantakan'"
                                class="px-2.5 py-1 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 text-xs border border-rose-500/20 transition-colors text-left">
                            👎 Pengiriman terlambat makanan dingin tumpah
                        </button>
                        <button type="button" onclick="document.getElementById('ulasan').value = 'Promo potongan voucher diskon shopeefood murah mantap puas belanja di sini'"
                                class="px-2.5 py-1 rounded-lg bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 text-xs border border-emerald-500/20 transition-colors text-left">
                            👍 Promo potongan diskon murah mantap puas
                        </button>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="btn-shopee w-full sm:w-auto px-8 py-3.5 rounded-xl text-sm font-bold inline-flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Klasifikasikan Sentimen Sekarang
                    </button>
                </div>
            </form>
        </div>

        <!-- Result Display Card -->
        @if($result)
            <div class="glass-panel rounded-3xl p-6 sm:p-8 space-y-6 border-l-4 {{ $result['prediction'] === 'Positif' ? 'border-l-emerald-500' : 'border-l-rose-500' }}">
                <div class="flex items-center justify-between pb-4 border-b border-white/10">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        Hasil Klasifikasi Algoritma Naive Bayes
                    </h3>
                    <span class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-black uppercase tracking-wider {{ $result['prediction'] === 'Positif' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 shadow-lg shadow-emerald-500/10' : 'bg-rose-500/20 text-rose-300 border border-rose-500/40 shadow-lg shadow-rose-500/10' }}">
                        Sentimen: {{ $result['prediction'] }}
                    </span>
                </div>

                <!-- Confidence Meter -->
                <div class="space-y-2">
                    <div class="flex justify-between text-xs font-semibold">
                        <span class="text-gray-400">Tingkat Keyakinan (Confidence Score):</span>
                        <span class="text-white font-bold">{{ number_format($result['confidence'] * 100, 1) }}%</span>
                    </div>
                    <div class="w-full h-3 rounded-full bg-cosmos-800 overflow-hidden border border-white/10">
                        <div class="h-full rounded-full transition-all duration-500 {{ $result['prediction'] === 'Positif' ? 'bg-gradient-to-r from-emerald-500 to-teal-400' : 'bg-gradient-to-r from-rose-500 to-orange-500' }}"
                             style="width: {{ $result['confidence'] * 100 }}%"></div>
                    </div>
                    <div class="flex justify-between text-[11px] text-gray-500">
                        <span>P(Positif): {{ number_format(($result['probabilities']['Positif'] ?? 0) * 100, 1) }}%</span>
                        <span>P(Negatif): {{ number_format(($result['probabilities']['Negatif'] ?? 0) * 100, 1) }}%</span>
                    </div>
                </div>

                <!-- Pipeline Breakdown -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div class="p-4 rounded-2xl bg-cosmos-800/60 border border-white/5 space-y-1">
                        <span class="text-[10px] uppercase font-bold text-gray-400">Teks Masukan Asli:</span>
                        <p class="text-xs text-gray-200">{{ $result['raw_text'] }}</p>
                    </div>

                    <div class="p-4 rounded-2xl bg-cosmos-800/60 border border-white/5 space-y-1">
                        <span class="text-[10px] uppercase font-bold text-shopee-light">Hasil Preprocessing (Sastrawi Stemmed):</span>
                        <p class="text-xs text-white font-mono bg-black/30 p-2 rounded-lg">{{ $result['cleaned_text'] ?: '(Kosong setelah stopword filtering)' }}</p>
                    </div>
                </div>
            </div>
        @endif

    </div>
</x-app-layout>
