<x-app-layout>
    <div class="w-full max-w-[1700px] mx-auto space-y-8 pb-10">

        @if(!$evaluation)
            <!-- Empty State -->
            <div class="glass-panel rounded-3xl p-12 text-center max-w-xl mx-auto border border-dashed border-white/20 mt-12">
                <div class="w-16 h-16 rounded-2xl bg-shopee/10 border border-shopee/30 flex items-center justify-center mx-auto text-shopee-light mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Belum Ada Dataset Tersedia</h3>
                <p class="text-gray-400 text-sm mb-6">Unggah berkas Excel (.xlsx) atau CSV yang berisi kolom 'ulasan' dan 'rating' untuk memulai analisis sentimen.</p>
                <a href="{{ route('datasets.create') }}" class="btn-shopee inline-flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-bold shadow-lg shadow-shopee/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Unggah Dataset Sekarang
                </a>
            </div>
        @else

            <!-- ============================================== -->
            <!-- 1. UNIFIED EXECUTIVE HERO BANNER               -->
            <!-- ============================================== -->
            <div class="glass-panel rounded-3xl p-6 lg:p-8 relative overflow-hidden border border-white/10 shadow-2xl">
                <!-- Ambient Glow Orb -->
                <div class="absolute -right-24 -top-24 w-96 h-96 bg-shopee/15 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute -left-20 -bottom-20 w-80 h-80 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 flex flex-col xl:flex-row xl:items-center justify-between gap-6">
                    <!-- Left: Brand Context & Active Dataset Title -->
                    <div class="space-y-3 max-w-4xl">
                        <div class="flex flex-wrap items-center gap-2.5">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-gradient-to-r from-shopee to-shopee-light text-white shadow-md shadow-shopee/25">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                Multinomial Naive Bayes
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-white/5 border border-white/10 text-gray-300">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                TF-IDF Vectorizer (1.522 Kosakata)
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-white/5 border border-white/10 text-gray-300">
                                <svg class="w-3.5 h-3.5 text-shopee-light" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ now()->translatedFormat('l, d F Y') }} <strong class="text-shopee-light">WIB</strong>
                            </span>
                        </div>

                        <div>
                            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-white leading-tight">
                                Analisis Sentimen Keluhan <span class="text-gradient-shopee">ShopeeFood</span>
                            </h1>
                            <p class="text-gray-300 text-sm mt-1.5 flex flex-wrap items-center gap-2">
                                <span class="text-gray-400">Dataset Aktif:</span>
                                <strong class="text-white font-bold">{{ $evaluation->judul_dataset }}</strong>
                                <span class="text-gray-600">•</span>
                                <span class="text-xs text-gray-400">Berkas: <span class="font-mono text-gray-300">{{ $evaluation->file_name ?? 'Dataset ShopeeFood' }}</span></span>
                                <span class="text-gray-600">•</span>
                                <span class="text-xs text-gray-400">Diunggah oleh: <span class="text-gray-300 font-medium">{{ $evaluation->user->name ?? 'Administrator' }}</span> pada {{ $evaluation->created_at->translatedFormat('d F Y, H:i') }} WIB</span>
                            </p>
                        </div>
                    </div>

                    <!-- Right: Dataset Selector & Quick Actions Toolbar -->
                    <div class="flex flex-col sm:flex-row xl:flex-col items-start xl:items-end gap-3.5 flex-shrink-0">
                        @if($evaluations->isNotEmpty())
                            <form method="GET" action="{{ route('dashboard') }}" class="w-full sm:w-auto">
                                <div class="flex items-center gap-2">
                                    <label for="eval_select" class="text-xs font-bold uppercase tracking-wider text-gray-400 whitespace-nowrap">Pilih Dataset:</label>
                                    <select id="eval_select" name="evaluation_id" onchange="this.form.submit()" class="glass-input rounded-xl px-3.5 py-2 text-xs text-white focus:ring-shopee w-full sm:w-64 bg-cosmos-800 border-white/10">
                                        @foreach($evaluations as $ev)
                                            <option value="{{ $ev->id }}" {{ $evaluation && $evaluation->id == $ev->id ? 'selected' : '' }} class="bg-cosmos-800 text-white">
                                                {{ $ev->judul_dataset }} ({{ $ev->created_at->translatedFormat('d/m/Y H:i') }} WIB)
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </form>
                        @endif

                        <!-- Action Buttons -->
                        <div class="flex flex-wrap items-center gap-2">
                            <a href="{{ route('reports.pdf', $evaluation->id) }}"
                               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white/5 border border-white/10 hover:border-white/20 text-xs font-semibold text-white transition-all hover:bg-white/10 shadow-sm">
                                <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>Unduh PDF</span>
                            </a>

                            <a href="{{ route('reports.csv', $evaluation->id) }}"
                               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white/5 border border-white/10 hover:border-white/20 text-xs font-semibold text-white transition-all hover:bg-white/10 shadow-sm">
                                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                <span>Unduh CSV</span>
                            </a>

                            <a href="{{ route('prediction.single') }}"
                               class="btn-shopee inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold shadow-lg shadow-shopee/25 hover:scale-105 transition-transform">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                <span>Uji Kalimat</span>
                            </a>

                            @if(Auth::user()->isAdmin())
                                <form method="POST" action="{{ route('datasets.destroy', $evaluation->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus dataset ini beserta seluruh riwayat prediksinya?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Hapus Dataset" class="inline-flex items-center p-2 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 hover:bg-rose-500/20 transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- ============================================== -->
            <!-- 2. HIGH-IMPACT METRICS CARDS (5-COLUMN FLUID)  -->
            <!-- ============================================== -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 lg:gap-5">
                <!-- Card 1: Total Ulasan -->
                <div class="glass-panel glass-panel-hover rounded-2xl p-5 relative overflow-hidden border-t-4 border-t-shopee group">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Total Ulasan</span>
                        <div class="w-8 h-8 rounded-lg bg-shopee/10 flex items-center justify-center text-shopee-light">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                        </div>
                    </div>
                    <div class="text-3xl lg:text-4xl font-black text-white mt-2 tracking-tight">{{ number_format($evaluation->total_ulasan) }}</div>
                    <div class="flex items-center gap-2 mt-3 pt-2.5 border-t border-white/5 text-xs text-gray-400">
                        <span class="text-emerald-400 font-bold flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                            {{ $evaluation->total_positif }} Pos
                        </span>
                        <span class="text-gray-600">•</span>
                        <span class="text-rose-400 font-bold flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span>
                            {{ $evaluation->total_negatif }} Neg
                        </span>
                    </div>
                </div>

                <!-- Card 2: Akurasi -->
                <div class="glass-panel glass-panel-hover rounded-2xl p-5 relative overflow-hidden border-t-4 border-t-blue-500 group">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-blue-400">Akurasi</span>
                        <div class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center text-blue-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <div class="text-3xl lg:text-4xl font-black text-white mt-2 tracking-tight">{{ number_format($evaluation->akurasi * 100, 1) }}%</div>
                    <div class="mt-3 pt-2.5 border-t border-white/5 text-xs text-gray-400 flex items-center justify-between">
                        <span>Ketepatan prediksi</span>
                        <span class="text-[11px] font-mono text-blue-400 font-semibold">Overall</span>
                    </div>
                </div>

                <!-- Card 3: Presisi -->
                <div class="glass-panel glass-panel-hover rounded-2xl p-5 relative overflow-hidden border-t-4 border-t-purple-500 group">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-purple-400">Presisi</span>
                        <div class="w-8 h-8 rounded-lg bg-purple-500/10 flex items-center justify-center text-purple-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        </div>
                    </div>
                    <div class="text-3xl lg:text-4xl font-black text-white mt-2 tracking-tight">{{ number_format($evaluation->presisi * 100, 1) }}%</div>
                    <div class="mt-3 pt-2.5 border-t border-white/5 text-xs text-gray-400 flex items-center justify-between">
                        <span>Akurasi kelas positif</span>
                        <span class="text-[11px] font-mono text-purple-400 font-semibold">Precision</span>
                    </div>
                </div>

                <!-- Card 4: Recall -->
                <div class="glass-panel glass-panel-hover rounded-2xl p-5 relative overflow-hidden border-t-4 border-t-amber-500 group">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-amber-400">Recall</span>
                        <div class="w-8 h-8 rounded-lg bg-amber-500/10 flex items-center justify-center text-amber-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </div>
                    </div>
                    <div class="text-3xl lg:text-4xl font-black text-white mt-2 tracking-tight">{{ number_format($evaluation->recall * 100, 1) }}%</div>
                    <div class="mt-3 pt-2.5 border-t border-white/5 text-xs text-gray-400 flex items-center justify-between">
                        <span>Sensitivitas ulasan</span>
                        <span class="text-[11px] font-mono text-amber-400 font-semibold">True Pos Rate</span>
                    </div>
                </div>

                <!-- Card 5: F1-Score -->
                <div class="glass-panel glass-panel-hover rounded-2xl p-5 relative overflow-hidden border-t-4 border-t-emerald-500 sm:col-span-2 lg:col-span-1 group">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-emerald-400">F1-Score</span>
                        <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </div>
                    </div>
                    <div class="text-3xl lg:text-4xl font-black text-white mt-2 tracking-tight">{{ number_format($evaluation->f1_score * 100, 1) }}%</div>
                    <div class="mt-3 pt-2.5 border-t border-white/5 text-xs text-gray-400 flex items-center justify-between">
                        <span>Rata-rata harmonik</span>
                        <span class="text-[11px] font-mono text-emerald-400 font-semibold">Balanced</span>
                    </div>
                </div>
            </div>

            <!-- ============================================== -->
            <!-- 3. VISUALIZATIONS & INTELLIGENCE ROW (LUAS)    -->
            <!-- ============================================== -->
            <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
                <!-- Left: Donut Chart Distribusi (xl:col-span-4) -->
                <div class="xl:col-span-4 glass-panel rounded-3xl p-6 flex flex-col justify-between shadow-xl">
                    <div class="flex items-center justify-between pb-3 border-b border-white/5">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                            <h3 class="text-sm font-bold text-white uppercase tracking-wider">Distribusi Sentimen</h3>
                        </div>
                        <span class="text-[10px] uppercase font-bold text-gray-400 px-2 py-0.5 rounded-md bg-white/5 border border-white/10">Doughnut</span>
                    </div>

                    <div class="my-6 h-64 relative flex items-center justify-center">
                        <canvas id="sentimentPieChart"></canvas>
                        <!-- Center Text in Doughnut -->
                        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                            <span class="text-2xl font-black text-white">{{ number_format($evaluation->total_ulasan) }}</span>
                            <span class="text-[10px] text-gray-400 uppercase font-semibold">Ulasan</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 pt-3 border-t border-white/5">
                        <div class="p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-center">
                            <span class="block text-[11px] font-semibold text-gray-400 uppercase">Positif</span>
                            <span class="text-base font-black text-emerald-400 mt-0.5 block">
                                {{ $evaluation->total_positif }} 
                                <span class="text-xs font-normal">({{ round(($evaluation->total_positif / max(1, $evaluation->total_ulasan)) * 100, 1) }}%)</span>
                            </span>
                        </div>
                        <div class="p-3 rounded-xl bg-rose-500/10 border border-rose-500/20 text-center">
                            <span class="block text-[11px] font-semibold text-gray-400 uppercase">Negatif</span>
                            <span class="text-base font-black text-rose-400 mt-0.5 block">
                                {{ $evaluation->total_negatif }} 
                                <span class="text-xs font-normal">({{ round(($evaluation->total_negatif / max(1, $evaluation->total_ulasan)) * 100, 1) }}%)</span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Center: Bar Chart Metrik Performa (xl:col-span-4) -->
                <div class="xl:col-span-4 glass-panel rounded-3xl p-6 flex flex-col justify-between shadow-xl">
                    <div class="flex items-center justify-between pb-3 border-b border-white/5">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-400"></span>
                            <h3 class="text-sm font-bold text-white uppercase tracking-wider">Performa Klasifikasi</h3>
                        </div>
                        <span class="text-[10px] uppercase font-bold text-gray-400 px-2 py-0.5 rounded-md bg-white/5 border border-white/10">Bar Chart</span>
                    </div>

                    <div class="my-6 h-64 relative flex items-center justify-center">
                        <canvas id="metricsBarChart"></canvas>
                    </div>

                    <div class="p-3 rounded-xl bg-white/[0.02] border border-white/5 flex items-center justify-between text-xs text-gray-400">
                        <span>Metode Pembobotan</span>
                        <span class="font-mono font-semibold text-gray-200">TF-IDF Smooth L2</span>
                    </div>
                </div>

                <!-- Right: Voice of Customer / Top Negative Keywords (xl:col-span-4) -->
                <div class="xl:col-span-4 glass-panel rounded-3xl p-6 flex flex-col justify-between shadow-xl">
                    <div class="flex items-center justify-between pb-3 border-b border-white/5">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500 animate-pulse"></span>
                            <h3 class="text-sm font-bold text-white uppercase tracking-wider">Keluhan Terbanyak</h3>
                        </div>
                        <span class="text-[10px] text-rose-400 font-extrabold px-2.5 py-0.5 rounded-full bg-rose-500/10 border border-rose-500/20 uppercase">
                            Voice of Customer
                        </span>
                    </div>

                    <div class="my-3 py-2 flex flex-wrap gap-2 overflow-y-auto max-h-72 content-start pr-1">
                        @forelse($topNegativeWords as $word => $count)
                            @php
                                $badgeClass = $count >= 200 
                                    ? 'bg-rose-500/20 border-rose-500/40 text-rose-300 shadow-md shadow-rose-500/10' 
                                    : ($count >= 150 
                                        ? 'bg-amber-500/15 border-amber-500/30 text-amber-300' 
                                        : 'bg-white/5 border-white/10 text-gray-300');
                                $pillClass = $count >= 200 
                                    ? 'bg-rose-500 text-white' 
                                    : ($count >= 150 ? 'bg-amber-500 text-black font-extrabold' : 'bg-white/10 text-gray-300');
                            @endphp
                            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl border {{ $badgeClass }} hover:scale-105 transition-all cursor-default">
                                <span class="font-bold text-xs">{{ $word }}</span>
                                <span class="px-1.5 py-0.2 rounded-md text-[10px] {{ $pillClass }}">{{ $count }}</span>
                            </div>
                        @empty
                            <p class="text-xs text-gray-500 italic py-10 text-center w-full">Tidak ada kata keluhan negatif ditemukan.</p>
                        @endforelse
                    </div>

                    <div class="p-2.5 rounded-xl bg-white/[0.02] border border-white/5 text-[11px] text-gray-400 flex items-center justify-between">
                        <span>Fokus Utama:</span>
                        <span class="text-shopee-light font-semibold">Ongkir, Kurir, Driver, Batal</span>
                    </div>
                </div>
            </div>

            <!-- ============================================== -->
            <!-- 4. PREMIUM DATA GRID TABLE                     -->
            <!-- ============================================== -->
            <div class="glass-panel rounded-3xl overflow-hidden p-6 lg:p-8 space-y-6 shadow-2xl">
                <!-- Table Header with Search & Filter Controls -->
                <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-4 pb-4 border-b border-white/5">
                    <div>
                        <h3 class="text-xl font-extrabold text-white flex items-center gap-2">
                            <span>Daftar Hasil Prediksi Ulasan</span>
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-lg bg-white/5 border border-white/10 text-gray-400 font-mono">
                                {{ $predictions->total() }} Data
                            </span>
                        </h3>
                        <p class="text-xs text-gray-400 mt-1">Perbandingan ulasan asli, hasil stemming NLP Sastrawi, label rating aktual, dan prediksi model Naive Bayes.</p>
                    </div>

                    <!-- Sentiment Filter Tabs & Search Form -->
                    <form method="GET" action="{{ route('dashboard') }}" class="flex flex-wrap items-center gap-3">
                        <input type="hidden" name="evaluation_id" value="{{ $evaluation->id }}">

                        <!-- Filter Tabs -->
                        <div class="flex items-center rounded-xl bg-cosmos-800 p-1 border border-white/10 text-xs font-bold shadow-inner">
                            <a href="{{ route('dashboard', ['evaluation_id' => $evaluation->id, 'sentiment' => 'all', 'search' => $searchQuery]) }}"
                               class="px-3.5 py-1.5 rounded-lg transition-all {{ $filterSentiment === 'all' ? 'bg-shopee text-white shadow-md shadow-shopee/20' : 'text-gray-400 hover:text-white' }}">
                                Semua
                            </a>
                            <a href="{{ route('dashboard', ['evaluation_id' => $evaluation->id, 'sentiment' => 'Positif', 'search' => $searchQuery]) }}"
                               class="px-3.5 py-1.5 rounded-lg transition-all {{ $filterSentiment === 'Positif' ? 'bg-emerald-500 text-white shadow-md shadow-emerald-500/20' : 'text-gray-400 hover:text-white' }}">
                                Positif
                            </a>
                            <a href="{{ route('dashboard', ['evaluation_id' => $evaluation->id, 'sentiment' => 'Negatif', 'search' => $searchQuery]) }}"
                               class="px-3.5 py-1.5 rounded-lg transition-all {{ $filterSentiment === 'Negatif' ? 'bg-rose-500 text-white shadow-md shadow-rose-500/20' : 'text-gray-400 hover:text-white' }}">
                                Negatif
                            </a>
                        </div>

                        <!-- Search Box -->
                        <div class="relative">
                            <input type="text" name="search" value="{{ $searchQuery }}" placeholder="Cari isi ulasan / nama..."
                                   class="glass-input rounded-xl pl-9 pr-4 py-2 text-xs w-56 sm:w-72 focus:ring-shopee">
                            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>

                        <button type="submit" class="btn-shopee px-4 py-2 rounded-xl text-xs font-bold">Cari</button>

                        @if(!empty($searchQuery) || $filterSentiment !== 'all')
                            <a href="{{ route('dashboard', ['evaluation_id' => $evaluation->id]) }}" class="px-3 py-2 rounded-xl bg-white/5 text-gray-400 hover:text-white text-xs border border-white/10 transition-colors">Reset</a>
                        @endif
                    </form>
                </div>

                <!-- Table Container -->
                <div class="overflow-x-auto rounded-2xl border border-white/5">
                    <table class="w-full text-left text-xs text-gray-300">
                        <thead class="bg-cosmos-800/90 text-[11px] uppercase tracking-wider text-gray-400 border-b border-white/10 font-bold">
                            <tr>
                                <th class="px-5 py-4 w-48">Pengguna</th>
                                <th class="px-4 py-4 w-24 text-center">Rating</th>
                                <th class="px-5 py-4 min-w-[280px]">Ulasan Asli</th>
                                <th class="px-5 py-4 min-w-[260px]">Ulasan Bersih (Stemmed)</th>
                                <th class="px-4 py-4 text-center w-28">Aktual</th>
                                <th class="px-4 py-4 text-center w-28">Prediksi</th>
                                <th class="px-4 py-4 text-center w-24">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($predictions as $pred)
                                <tr class="hover:bg-white/[0.03] transition-colors group">
                                    <!-- Pengguna with Avatar -->
                                    <td class="px-5 py-3.5 whitespace-nowrap">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded-lg bg-gradient-to-tr from-white/10 to-white/5 border border-white/10 flex items-center justify-center font-bold text-[11px] text-shopee-light flex-shrink-0">
                                                {{ strtoupper(substr($pred->nama_pengguna ?? 'P', 0, 1)) }}
                                            </div>
                                            <span class="font-semibold text-white truncate max-w-[140px]">{{ $pred->nama_pengguna ?? 'Pengguna Shopee' }}</span>
                                        </div>
                                    </td>

                                    <!-- Rating -->
                                    <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-400 font-extrabold text-xs">
                                            ★ {{ $pred->rating ?? '-' }}
                                        </span>
                                    </td>

                                    <!-- Ulasan Asli -->
                                    <td class="px-5 py-3.5 text-gray-200 text-xs leading-relaxed">
                                        <div class="line-clamp-2 group-hover:line-clamp-none transition-all duration-200">
                                            {{ $pred->ulasan_asli }}
                                        </div>
                                    </td>

                                    <!-- Ulasan Bersih -->
                                    <td class="px-5 py-3.5 text-gray-400 font-mono text-[11px] leading-relaxed">
                                        <div class="line-clamp-2 group-hover:line-clamp-none transition-all duration-200">
                                            {{ $pred->ulasan_bersih }}
                                        </div>
                                    </td>

                                    <!-- Sentimen Aktual -->
                                    <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                        <span class="inline-block px-3 py-1 rounded-full text-[11px] font-bold {{ $pred->sentimen_aktual === 'Positif' ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30' : 'bg-rose-500/15 text-rose-400 border border-rose-500/30' }}">
                                            {{ $pred->sentimen_aktual ?? '-' }}
                                        </span>
                                    </td>

                                    <!-- Sentimen Prediksi -->
                                    <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                        <span class="inline-block px-3 py-1 rounded-full text-[11px] font-extrabold shadow-sm {{ $pred->sentimen_prediksi === 'Positif' ? 'bg-emerald-500/25 text-emerald-300 border border-emerald-500/50 shadow-emerald-500/10' : 'bg-rose-500/25 text-rose-300 border border-rose-500/50 shadow-rose-500/10' }}">
                                            {{ $pred->sentimen_prediksi }}
                                        </span>
                                    </td>

                                    <!-- Status -->
                                    <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                        @if($pred->is_correct)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 text-[10px] font-bold border border-emerald-500/30" title="Prediksi Sesuai">
                                                ✓ Sesuai
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-rose-500/20 text-rose-400 text-[10px] font-bold border border-rose-500/30" title="Beda Prediksi">
                                                ✕ Beda
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-12 text-center text-gray-500 italic">
                                        Tidak ada ulasan yang cocok dengan kriteria pencarian Anda.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Links -->
                <div class="pt-2 flex items-center justify-between border-t border-white/5">
                    {{ $predictions->links() }}
                </div>
            </div>

            <!-- Scripts for Chart.js Initialization -->
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    // 1. Sentiment Doughnut Chart
                    const ctxPie = document.getElementById('sentimentPieChart');
                    if (ctxPie) {
                        new Chart(ctxPie, {
                            type: 'doughnut',
                            data: {
                                labels: ['Positif', 'Negatif'],
                                datasets: [{
                                    data: [{{ $evaluation->total_positif }}, {{ $evaluation->total_negatif }}],
                                    backgroundColor: ['#10b981', '#f43f5e'],
                                    borderColor: ['#0b0c10', '#0b0c10'],
                                    borderWidth: 3,
                                    hoverOffset: 8
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                cutout: '72%',
                                plugins: {
                                    legend: {
                                        position: 'bottom',
                                        labels: { color: '#cbd5e1', font: { family: 'Outfit', size: 12, weight: '600' }, padding: 15 }
                                    }
                                }
                            }
                        });
                    }

                    // 2. Performance Metrics Bar Chart
                    const ctxBar = document.getElementById('metricsBarChart');
                    if (ctxBar) {
                        new Chart(ctxBar, {
                            type: 'bar',
                            data: {
                                labels: ['Akurasi', 'Presisi', 'Recall', 'F1-Score'],
                                datasets: [{
                                    label: 'Persentase (%)',
                                    data: [
                                        {{ number_format($evaluation->akurasi * 100, 1) }},
                                        {{ number_format($evaluation->presisi * 100, 1) }},
                                        {{ number_format($evaluation->recall * 100, 1) }},
                                        {{ number_format($evaluation->f1_score * 100, 1) }}
                                    ],
                                    backgroundColor: ['#3b82f6', '#a855f7', '#f59e0b', '#10b981'],
                                    borderRadius: 10,
                                    barPercentage: 0.55
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        max: 100,
                                        grid: { color: 'rgba(255, 255, 255, 0.05)' },
                                        ticks: { color: '#94a3b8', callback: value => value + '%' }
                                    },
                                    x: {
                                        grid: { display: false },
                                        ticks: { color: '#e2e8f0', font: { family: 'Outfit', weight: '700' } }
                                    }
                                },
                                plugins: {
                                    legend: { display: false }
                                }
                            }
                        });
                    }
                });
            </script>
        @endif
    </div>
</x-app-layout>
