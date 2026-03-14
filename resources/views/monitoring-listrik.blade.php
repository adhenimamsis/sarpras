<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-xl text-slate-800 leading-tight tracking-tighter uppercase">
                ⚡ Monitoring Kelistrikan & Utilitas
            </h2>
            <div class="flex items-center gap-3">
                <span class="hidden md:inline-block bg-blue-100 text-blue-800 text-[10px] font-black px-3 py-1 rounded-full border border-blue-200 uppercase tracking-widest">
                    {{ \App\Models\Setting::getValue('nama_puskesmas', 'UPT PUSKESMAS BENDAN') }}
                </span>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                    {{ now()->translatedFormat('d F Y') }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-[2rem] shadow-sm border-l-8 border-emerald-500 shadow-blue-900/5 transition-all hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[10px] text-slate-400 font-black uppercase tracking-[0.2em]">Sumber Utama (PLN)</p>
                            <h3 class="text-2xl font-black text-slate-800 mt-1">
                                {{ $listrikTerakhir ? 'CONNECTED' : 'DISCONNECTED' }}
                            </h3>
                        </div>
                        <div class="bg-emerald-50 p-4 rounded-2xl">
                            <i class="fas fa-plug text-emerald-600 text-2xl"></i>
                        </div>
                    </div>
                    <p class="text-[10px] text-emerald-600 mt-4 flex items-center gap-2 font-black uppercase">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        Tegangan: {{ $listrikTerakhir->voltase ?? '220' }}V (Stabil)
                    </p>
                </div>

                <div class="bg-white p-6 rounded-[2rem] shadow-sm border-l-8 border-amber-500 shadow-blue-900/5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[10px] text-slate-400 font-black uppercase tracking-[0.2em]">Genset Cadangan</p>
                            <h3 class="text-2xl font-black text-slate-800 mt-1">STANDBY</h3>
                        </div>
                        <div class="bg-amber-50 p-4 rounded-2xl">
                            <i class="fas fa-charging-station text-amber-600 text-2xl"></i>
                        </div>
                    </div>
                    <div class="mt-4">
                        <p class="text-[10px] text-slate-500 font-black uppercase flex justify-between">
                            <span><i class="fas fa-gas-pump mr-1 text-amber-500"></i> BBM Solar</span>
                            <span>{{ $bbmGenset->angka_meteran ?? 0 }}%</span>
                        </p>
                        <div class="w-full bg-slate-100 rounded-full h-1 mt-1">
                            <div class="bg-amber-500 h-1 rounded-full" style="width: {{ $bbmGenset->angka_meteran ?? 0 }}%"></div>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-[2rem] shadow-sm border-l-8 border-blue-500 shadow-blue-900/5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[10px] text-slate-400 font-black uppercase tracking-[0.2em]">Beban Saat Ini</p>
                            <h3 class="text-2xl font-black text-slate-800 mt-1">{{ number_format($currentLoad / 1000, 1) }} kW</h3>
                        </div>
                        <div class="bg-blue-50 p-4 rounded-2xl">
                            <i class="fas fa-bolt text-blue-600 text-2xl"></i>
                        </div>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-1.5 mt-5">
                        <div class="bg-blue-600 h-1.5 rounded-full" style="width: 45%"></div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <div class="lg:col-span-3 bg-white overflow-hidden shadow-sm sm:rounded-[2rem] border border-slate-100">
                    <div class="p-8 border-b border-slate-50 flex justify-between items-center bg-slate-50/50">
                        <h3 class="font-black text-slate-800 uppercase tracking-tighter flex items-center gap-2">
                            <i class="fas fa-building text-blue-500"></i> Kontrol Panel & Suhu Area
                        </h3>
                    </div>
                    <div class="p-8">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                            @foreach($areas as $area)
                            <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100 transition-transform hover:scale-105">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">{{ $area['name'] }}</p>
                                <p class="text-xl font-black text-slate-700 mb-2">{{ $area['temp'] }}°C</p>
                                <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase {{ $area['status'] == 'Alert' ? 'bg-rose-100 text-rose-700 animate-pulse' : 'bg-emerald-100 text-emerald-700' }}">
                                    {{ $area['status'] }}
                                </span>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="p-8 border-t border-slate-50">
                        <h4 class="text-xs font-black text-slate-800 uppercase mb-4 tracking-widest">Riwayat Servis Terakhir</h4>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-[11px]">
                                <thead class="text-slate-400 font-black uppercase tracking-widest border-b border-slate-100">
                                    <tr>
                                        <th class="pb-3">Tanggal</th>
                                        <th class="pb-3">Komponen</th>
                                        <th class="pb-3 text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="text-slate-600">
                                    @foreach($maintenanceLogs as $log)
                                    <tr>
                                        <td class="py-3">{{ $log->created_at->format('d/m/Y') }}</td>
                                        <td class="py-3 font-bold uppercase">{{ $log->asset->nama_alat }}</td>
                                        <td class="py-3 text-center">
                                            <span class="bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded text-[9px] font-bold">DONE</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-slate-900 rounded-[2rem] p-8 text-white shadow-xl shadow-slate-200">
                        <h4 class="font-black uppercase text-[10px] tracking-[0.2em] mb-6 text-slate-400">Quick Actions</h4>
                        <div class="space-y-4">
                            
                            <a href="{{ route('monitoring.form.kosong') }}" target="_blank" 
                               class="flex items-center justify-between w-full p-4 bg-emerald-600/20 border border-emerald-500/30 rounded-2xl hover:bg-emerald-600 transition-all group">
                                <div class="text-left">
                                    <span class="block text-xs font-black uppercase tracking-widest text-white">Cetak Log MFK</span>
                                    <span class="block text-[9px] text-emerald-400 group-hover:text-emerald-100 uppercase mt-0.5">Manual Lapangan</span>
                                </div>
                                <i class="fas fa-print text-emerald-400 group-hover:text-white text-xl"></i>
                            </a>

                            @can('reports.export.operational')
                            <a href="{{ route('monitoring.mfk.excel') }}" class="flex items-center justify-between w-full p-4 bg-white/10 rounded-2xl hover:bg-white/20 transition group">
                                <span class="text-xs font-bold uppercase tracking-widest text-slate-300 group-hover:text-white">Export Excel</span>
                                <i class="fas fa-file-excel text-slate-400 group-hover:text-white"></i>
                            </a>
                            @endcan
                            
                            <a href="{{ route('utility.chart') }}" class="flex items-center justify-between w-full p-4 bg-blue-600 rounded-2xl hover:bg-blue-500 transition shadow-lg shadow-blue-900/50">
                                <span class="text-xs font-black uppercase tracking-widest text-white">Analisa Beban</span>
                                <i class="fas fa-chart-line text-white"></i>
                            </a>
                        </div>
                    </div>

                    <div class="bg-white rounded-[2rem] p-8 border-2 border-rose-500/20 shadow-sm">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="p-2 bg-rose-100 rounded-lg">
                                <i class="fas fa-shield-alt text-rose-600"></i>
                            </div>
                            <h4 class="font-black text-slate-800 uppercase text-[10px] tracking-widest">Safety Alert</h4>
                        </div>
                        <p class="text-[11px] text-slate-500 leading-relaxed font-bold">
                            Pastikan sistem pendingin Server Room tetap menyala. Suhu ideal: <span class="text-blue-600">18°C - 24°C</span>.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
