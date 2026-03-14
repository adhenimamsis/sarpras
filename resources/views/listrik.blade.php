<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-2">
                    <span class="p-2 bg-yellow-400 rounded-lg shadow-sm">⚡</span>
                    Monitoring Kelistrikan & Utilitas
                </h2>
                <p class="text-sm text-gray-500 mt-1">Sistem Pemantauan Fasilitas Real-time</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="hidden md:inline-flex bg-blue-100 text-blue-800 text-xs font-black px-3 py-1 rounded-full border border-blue-200">
                    ID: PKM-BND-01
                </span>
                <button onclick="window.print()" class="p-2 bg-white border rounded-lg shadow-sm hover:bg-gray-50 transition">
                    <i class="fas fa-print text-gray-600"></i>
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 relative overflow-hidden group">
                    <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:scale-110 transition-transform">
                        <i class="fas fa-plug text-6xl text-emerald-600"></i>
                    </div>
                    <div class="relative z-10">
                        <p class="text-xs font-black text-emerald-600 uppercase tracking-widest">Main Power (PLN)</p>
                        <h3 class="text-3xl font-black text-gray-900 mt-2">CONNECTED</h3>
                        <div class="mt-4 flex items-center gap-2">
                            <span class="flex h-3 w-3 relative">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                            </span>
                            <p class="text-sm font-bold text-gray-600">221.5 Volts <span class="font-normal text-gray-400">/ 50Hz</span></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 relative overflow-hidden group">
                    <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:scale-110 transition-transform">
                        <i class="fas fa-charging-station text-6xl text-amber-600"></i>
                    </div>
                    <div class="relative z-10">
                        <p class="text-xs font-black text-amber-600 uppercase tracking-widest">Genset Backup</p>
                        <h3 class="text-3xl font-black text-gray-900 mt-2">STANDBY</h3>
                        <div class="mt-4 flex flex-col gap-1">
                            <div class="flex justify-between text-xs font-bold mb-1">
                                <span class="text-gray-500">BBM Level</span>
                                <span class="text-amber-600">85%</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div class="bg-amber-500 h-2 rounded-full" style="width: 85%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 relative overflow-hidden group">
                    <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:scale-110 transition-transform">
                        <i class="fas fa-bolt text-6xl text-blue-600"></i>
                    </div>
                    <div class="relative z-10">
                        <p class="text-xs font-black text-blue-600 uppercase tracking-widest">Current Load</p>
                        <h3 class="text-3xl font-black text-gray-900 mt-2">45.2 <span class="text-lg text-gray-400">kW</span></h3>
                        <p class="mt-4 text-xs font-bold text-gray-500">
                            <i class="fas fa-arrow-up text-rose-500 mr-1"></i> +2.4% dari jam sebelumnya
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-6 border-b border-gray-50 flex justify-between items-center bg-white">
                            <h3 class="font-black text-gray-800 uppercase tracking-tighter flex items-center gap-2">
                                <i class="fas fa-th-large text-blue-500"></i> Distribusi Daya Per Unit
                            </h3>
                            <span class="text-[10px] text-gray-400">Terakhir Update: {{ now()->format('H:i') }}</span>
                        </div>
                        <div class="p-6 grid grid-cols-2 md:grid-cols-4 gap-4">
                            @php
                                $areas = [
                                    ['name' => 'Poli Klinik', 'status' => 'Normal', 'load' => '12kW'],
                                    ['name' => 'Ruang UGD', 'status' => 'Normal', 'load' => '18kW'],
                                    ['name' => 'Laboratorium', 'status' => 'Normal', 'load' => '8kW'],
                                    ['name' => 'Server Room', 'status' => 'Alert', 'load' => '7.2kW'],
                                ];
                            @endphp

                            @foreach($areas as $area)
                            <div class="p-4 rounded-2xl border transition hover:shadow-md {{ $area['status'] == 'Alert' ? 'bg-rose-50 border-rose-100' : 'bg-gray-50 border-gray-100' }}">
                                <p class="text-[10px] font-black text-gray-400 uppercase mb-1">{{ $area['name'] }}</p>
                                <p class="text-lg font-black {{ $area['status'] == 'Alert' ? 'text-rose-600' : 'text-gray-800' }}">{{ $area['load'] }}</p>
                                <div class="mt-3">
                                    <span class="px-2 py-0.5 rounded-md text-[8px] font-black uppercase {{ $area['status'] == 'Alert' ? 'bg-rose-200 text-rose-700' : 'bg-emerald-200 text-emerald-700' }}">
                                        {{ $area['status'] }}
                                    </span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-6 border-b border-gray-50">
                            <h3 class="font-black text-gray-800 uppercase tracking-tighter">Log Pemeliharaan Terakhir</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead class="bg-gray-50/50 text-gray-400 uppercase text-[10px] font-black tracking-widest">
                                    <tr>
                                        <th class="px-6 py-4">Komponen</th>
                                        <th class="px-6 py-4">Pekerjaan</th>
                                        <th class="px-6 py-4 text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    <tr class="hover:bg-gray-50/50 transition">
                                        <td class="px-6 py-4">
                                            <p class="font-bold text-gray-800">Genset 50kVA</p>
                                            <p class="text-[10px] text-gray-400">15 Feb 2026</p>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600">Periodic Maintenance & Oil Change</td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-lg text-[10px] font-black">COMPLETED</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-blue-600 rounded-3xl p-6 text-white shadow-lg shadow-blue-200">
                        <h4 class="font-black uppercase text-sm tracking-widest mb-4">Quick Actions</h4>
                        <div class="grid grid-cols-1 gap-3">
                            <button class="w-full py-3 bg-white/10 hover:bg-white/20 rounded-xl flex items-center justify-between px-4 transition border border-white/10">
                                <span class="text-sm font-bold">Lapor Gangguan</span>
                                <i class="fas fa-exclamation-circle"></i>
                            </button>
                            <button class="w-full py-3 bg-white/10 hover:bg-white/20 rounded-xl flex items-center justify-between px-4 transition border border-white/10">
                                <span class="text-sm font-bold">Cetak Log MFK</span>
                                <i class="fas fa-file-pdf"></i>
                            </button>
                            <button class="w-full py-3 bg-white text-blue-600 rounded-xl flex items-center justify-between px-4 transition font-black shadow-sm">
                                <span class="text-sm">Uji Fungsi Genset</span>
                                <i class="fas fa-play"></i>
                            </button>
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 bg-rose-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-shield-alt text-rose-600"></i>
                            </div>
                            <h4 class="font-black uppercase text-xs tracking-tighter">Safety Alert (MFK)</h4>
                        </div>
                        <p class="text-xs text-gray-500 leading-relaxed mb-4">
                            Server room terdeteksi <strong>Overheat (32°C)</strong>. Harap cek sistem pendingin AC atau kurangi beban server sementara.
                        </p>
                        <button class="w-full py-2 bg-gray-900 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-gray-800 transition">
                            Matikan Alarm
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
