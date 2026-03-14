<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Aset - {{ $asset->nama_alat }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-card { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); }
        .bg-bendan { background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); }
    </style>
</head>
<body class="bg-[#F8FAFC] min-h-screen">

    <nav class="bg-bendan p-4 shadow-lg sticky top-0 z-50">
        <div class="max-w-md mx-auto flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="bg-white/20 p-1.5 rounded-lg">
                    <i class="fas fa-hospital-alt text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-white font-extrabold text-sm leading-none uppercase tracking-tighter">SIM-SARPRAS</h1>
                    <p class="text-blue-100 text-[9px] font-medium opacity-80 uppercase tracking-widest">UPT Puskesmas Bendan</p>
                </div>
            </div>
            <span class="text-white text-[10px] font-bold bg-white/20 px-2 py-1 rounded border border-white/30 uppercase">Public Access</span>
        </div>
    </nav>

    <div class="max-w-md mx-auto p-4 pb-20">
        
        <div class="mb-6 text-center">
            @if($asset->kondisi == 'Baik' || $asset->kondisi == 'baik')
                <div class="inline-flex items-center gap-2 bg-emerald-50 text-emerald-700 px-6 py-3 rounded-full border border-emerald-100 shadow-sm">
                    <span class="relative flex h-3 w-3">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                    </span>
                    <span class="text-[11px] font-black tracking-[0.15em] uppercase">Layak Operasional</span>
                </div>
            @else
                <div class="inline-flex items-center gap-2 bg-rose-50 text-rose-700 px-6 py-3 rounded-full border border-rose-100 shadow-sm">
                    <i class="fas fa-exclamation-triangle animate-pulse"></i>
                    <span class="text-[11px] font-black tracking-[0.15em] uppercase">Perlu Perbaikan / Rusak</span>
                </div>
            @endif
        </div>

        <div class="bg-white rounded-[2rem] shadow-xl shadow-blue-900/5 overflow-hidden mb-6 border border-slate-100">
            <div class="relative">
                @if($asset->foto)
                    <img src="{{ asset('storage/' . $asset->foto) }}" alt="Foto Alat" class="w-full h-80 object-cover">
                @else
                    <div class="w-full h-64 bg-slate-50 flex flex-col items-center justify-center text-slate-300">
                        <i class="fas fa-microchip text-7xl mb-4 opacity-10"></i>
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-40">No Image Available</p>
                    </div>
                @endif
                
                <div class="absolute bottom-4 left-4 right-4 flex justify-between items-center gap-2">
                    <span class="glass-card text-slate-800 text-[9px] px-3 py-1.5 rounded-xl font-bold border border-white/50 shadow-sm truncate">
                        SN: {{ $asset->no_seri ?? 'N/A' }}
                    </span>
                    <span class="bg-blue-600 text-white text-[9px] px-3 py-1.5 rounded-xl font-bold shadow-lg uppercase tracking-tighter shrink-0">
                        ASPAK: {{ $asset->kode_aspak ?? '-' }}
                    </span>
                </div>
            </div>
            
            <div class="p-6">
                <h2 class="text-2xl font-extrabold text-slate-900 leading-tight mb-2 uppercase tracking-tighter">
                    {{ $asset->nama_alat }}
                </h2>
                <div class="flex items-center gap-2 text-slate-500">
                    <i class="fas fa-map-marker-alt text-rose-500 text-sm"></i>
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-700">
                        {{ $asset->ruangan->nama_ruangan ?? 'Lokasi Belum Diatur' }} 
                        <span class="bg-slate-100 px-2 py-0.5 rounded text-blue-600 font-black">[{{ $asset->ruangan->kode_ruangan ?? '-' }}]</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4">
            
            <div class="bg-white p-5 rounded-3xl shadow-sm border border-slate-100">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-1 h-4 bg-blue-600 rounded-full"></div>
                    <h3 class="text-slate-400 text-[10px] uppercase font-black tracking-[0.15em]">Informasi Teknis</h3>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-slate-50 p-3 rounded-2xl border border-slate-100">
                        <p class="text-[9px] text-slate-400 uppercase font-black mb-1">Merk / Brand</p>
                        <p class="font-bold text-slate-800 text-sm truncate uppercase">{{ $asset->merk ?? '-' }}</p>
                    </div>
                    <div class="bg-slate-50 p-3 rounded-2xl border border-slate-100">
                        <p class="text-[9px] text-slate-400 uppercase font-black mb-1">Model / Tipe</p>
                        <p class="font-bold text-slate-800 text-sm truncate uppercase">{{ $asset->tipe ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-blue-700 to-indigo-800 p-6 rounded-3xl shadow-lg shadow-blue-200 relative overflow-hidden">
                <div class="absolute -right-8 -bottom-8 opacity-20 transform -rotate-12">
                    <i class="fas fa-shield-check text-9xl text-white"></i>
                </div>
                <div class="relative z-10">
                    <h3 class="text-blue-100 text-[10px] uppercase font-black tracking-[0.2em] mb-4 opacity-80">Jadwal Kalibrasi Berikutnya</h3>
                    <div class="flex justify-between items-center">
                        <p class="font-black text-white text-2xl tracking-tight">
                            @if($asset->tgl_kalibrasi_selanjutnya)
                                {{ \Carbon\Carbon::parse($asset->tgl_kalibrasi_selanjutnya)->translatedFormat('d M Y') }}
                            @else
                                <span class="opacity-50 text-lg">TIDAK ADA JADWAL</span>
                            @endif
                        </p>
                        @php
                            $isExpired = $asset->tgl_kalibrasi_selanjutnya && \Carbon\Carbon::parse($asset->tgl_kalibrasi_selanjutnya)->isPast();
                        @endphp
                        @if($isExpired)
                            <div class="bg-rose-500 text-white text-[9px] px-3 py-1.5 rounded-full font-black animate-bounce shadow-lg">
                                PERLU RE-KALIBRASI
                            </div>
                        @elseif($asset->tgl_kalibrasi_selanjutnya)
                            <div class="bg-white/20 text-white text-[9px] px-3 py-1.5 rounded-full font-black border border-white/30 uppercase tracking-widest">
                                Validated
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-white p-5 rounded-3xl shadow-sm border border-slate-100">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-1 h-4 bg-emerald-500 rounded-full"></div>
                    <h3 class="text-slate-400 text-[10px] uppercase font-black tracking-[0.15em]">Riwayat Pemeliharaan</h3>
                </div>
                <div class="space-y-4">
                    {{-- Sesuaikan dengan relasi di Model Asset Bos --}}
                    @forelse($asset->maintenances->sortByDesc('tgl_realisasi')->take(2) as $pem)
                        <div class="flex gap-4 p-3 rounded-2xl hover:bg-slate-50 transition-colors">
                            <div class="bg-blue-50 text-blue-600 w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-tools text-sm"></i>
                            </div>
                            <div class="flex-1 border-b border-slate-50 pb-2">
                                <div class="flex justify-between">
                                    <p class="text-xs font-bold text-slate-800">{{ $pem->kegiatan }}</p>
                                    <span class="text-[8px] font-black text-emerald-600 uppercase">{{ $pem->status }}</span>
                                </div>
                                <p class="text-[10px] text-slate-400 mt-0.5">
                                    {{ $pem->tgl_realisasi ? \Carbon\Carbon::parse($pem->tgl_realisasi)->format('d M Y') : 'Dalam Proses' }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <i class="fas fa-folder-open text-slate-100 text-4xl mb-2"></i>
                            <p class="text-xs italic text-slate-300">Belum ada riwayat pemeliharaan.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="pt-6 space-y-4">
                @if($asset->manual_book)
                <a href="{{ asset('storage/' . $asset->manual_book) }}" target="_blank" 
                   class="flex items-center justify-center gap-3 w-full bg-white text-slate-700 border border-slate-200 font-black py-4 rounded-2xl transition-all active:scale-95 text-xs uppercase tracking-widest shadow-sm">
                    <i class="fas fa-file-pdf text-rose-500 text-lg"></i> Download Manual Book
                </a>
                @endif

                @php
                    $waLink = "https://wa.me/6285875840001?text=" . urlencode("🚨 LAPOR KERUSAKAN\n━━━━━━━━━━━━━━\nAlat: {$asset->nama_alat}\nSN: {$asset->no_seri}\nLokasi: " . ($asset->ruangan->nama_ruangan ?? '-') . " [{$asset->ruangan->kode_ruangan}]\n\nKeterangan Kerusakan: ");
                @endphp

                <a href="{{ $waLink }}" class="group relative flex items-center justify-center gap-3 w-full bg-slate-900 hover:bg-black text-white font-black py-5 rounded-3xl shadow-2xl transition-all transform active:scale-95 text-xs uppercase tracking-[0.2em]">
                    <i class="fab fa-whatsapp text-xl text-emerald-400 group-hover:scale-110 transition-transform"></i>
                    Lapor Kerusakan Alat
                </a>
            </div>
        </div>

        <footer class="mt-12 text-center">
            <div class="flex justify-center gap-4 mb-4">
                <div class="h-[1px] w-8 bg-slate-200 self-center"></div>
                <p class="text-slate-400 text-[9px] font-black uppercase tracking-[0.3em]">&copy; {{ date('Y') }} SIM-SARPRAS BENDAN</p>
                <div class="h-[1px] w-8 bg-slate-200 self-center"></div>
            </div>
        </footer>
    </div>

</body>
</html>