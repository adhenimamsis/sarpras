<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="font-black text-2xl text-slate-800 leading-tight tracking-tighter uppercase">
                    Pusat Kendali <span class="text-blue-600">SimSarpras</span>
                </h2>
                <p class="text-xs text-slate-500 font-medium uppercase tracking-widest mt-1">UPT Puskesmas Bendan Kota Pekalongan</p>
            </div>
            <div class="flex items-center gap-3 bg-white px-4 py-2 rounded-2xl shadow-sm border border-slate-100">
                <i class="fas fa-calendar-day text-blue-500"></i>
                <span class="text-sm font-bold text-slate-700">{{ date('l, d F Y') }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="relative overflow-hidden bg-white rounded-[2.5rem] shadow-xl shadow-blue-900/5 border border-white">
                <div class="absolute top-0 right-0 -mt-20 -mr-20 w-64 h-64 bg-blue-50 rounded-full opacity-50"></div>
                <div class="relative p-8 md:p-10 flex flex-col md:flex-row items-center gap-8">
                    <div class="relative">
                        <div class="bg-gradient-to-br from-blue-600 to-indigo-700 p-5 rounded-3xl shadow-xl shadow-blue-200">
                            <i class="fas fa-user-shield text-white text-4xl"></i>
                        </div>
                        <div class="absolute -bottom-2 -right-2 bg-emerald-500 w-6 h-6 rounded-full border-4 border-white"></div>
                    </div>
                    <div class="text-center md:text-left">
                        <h3 class="text-3xl font-black text-slate-900 leading-tight">Selamat Bertugas, {{ Auth::user()->name }}!</h3>
                        <p class="text-slate-500 mt-2 font-medium max-w-xl">Sistem mendeteksi <span class="text-rose-600 font-bold">{{ $active_reports ?? 0 }} laporan kerusakan</span> yang membutuhkan perhatian Anda pagi ini. Mari jaga fasilitas tetap prima.</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="group bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 hover:border-blue-500 transition-all duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-blue-50 p-4 rounded-2xl text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-all">
                            <i class="fas fa-microchip text-xl"></i>
                        </div>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Inventaris</span>
                    </div>
                    <h4 class="text-4xl font-black text-slate-800 leading-none">{{ $total_assets ?? '0' }}</h4>
                    <p class="text-xs text-slate-400 mt-2 font-bold uppercase tracking-tighter">Aset Medis & Non-Medis</p>
                </div>

                <div class="group bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 hover:border-rose-500 transition-all duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-rose-50 p-4 rounded-2xl text-rose-600 group-hover:bg-rose-600 group-hover:text-white transition-all">
                            <i class="fas fa-tools text-xl"></i>
                        </div>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Laporan Rusak</span>
                    </div>
                    <h4 class="text-4xl font-black text-rose-600 leading-none">{{ $active_reports ?? '0' }}</h4>
                    <p class="text-xs text-rose-400 mt-2 font-bold uppercase tracking-tighter">Butuh Tindakan Segera</p>
                </div>

                <div class="group bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 hover:border-amber-500 transition-all duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-amber-50 p-4 rounded-2xl text-amber-600 group-hover:bg-amber-600 group-hover:text-white transition-all">
                            <i class="fas fa-shield-virus text-xl"></i>
                        </div>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Overdue Kalibrasi</span>
                    </div>
                    <h4 class="text-4xl font-black text-amber-500 leading-none">{{ $due_calibration ?? '0' }}</h4>
                    <p class="text-xs text-amber-400 mt-2 font-bold uppercase tracking-tighter">Kepatuhan Standar Alat</p>
                </div>

                <div class="group bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 hover:border-emerald-500 transition-all duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-emerald-50 p-4 rounded-2xl text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition-all">
                            <i class="fas fa-lungs text-xl"></i>
                        </div>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Stok Oksigen</span>
                    </div>
                    <h4 class="text-4xl font-black text-emerald-600 leading-none">{{ $oxygen_stock ?? '0' }}</h4>
                    <p class="text-xs text-emerald-400 mt-2 font-bold uppercase tracking-tighter">Tabung Siap Pakai</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8">
                        <div class="flex justify-between items-center mb-8">
                            <h3 class="font-black text-slate-800 uppercase tracking-tighter flex items-center gap-2">
                                <i class="fas fa-broadcast-tower text-blue-500"></i> Monitoring Utilitas Puskesmas
                            </h3>
                            <a href="{{ route('monitoring.listrik') }}" class="text-[10px] font-black text-blue-600 border-b-2 border-blue-100 hover:border-blue-600 transition-all uppercase tracking-widest">Detail Monitoring</a>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="p-5 rounded-3xl bg-slate-50 border border-slate-100 flex items-center gap-5">
                                <div class="relative flex h-3 w-3">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                                </div>
                                <div class="flex-1">
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Kelistrikan (PLN)</p>
                                    <p class="font-bold text-slate-700 uppercase">Status Normal</p>
                                </div>
                                <i class="fas fa-bolt text-amber-400 text-lg"></i>
                            </div>

                            <div class="p-5 rounded-3xl bg-slate-50 border border-slate-100 flex items-center gap-5">
                                <div class="relative flex h-3 w-3">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                                </div>
                                <div class="flex-1">
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Suplai Air Bersih</p>
                                    <p class="font-bold text-slate-700 uppercase">Lancar</p>
                                </div>
                                <i class="fas fa-tint text-blue-400 text-lg"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8">
                        <h3 class="font-black text-slate-800 uppercase tracking-tighter mb-6 flex items-center gap-2">
                            <i class="fas fa-tasks text-emerald-500"></i> Kepatuhan Kalibrasi Tahunan
                        </h3>
                        <div class="relative pt-1">
                            <div class="flex mb-2 items-center justify-between">
                                <div><span class="text-xs font-black inline-block py-1 px-2 uppercase rounded-full text-blue-600 bg-blue-100">Progress</span></div>
                                <div class="text-right"><span class="text-xs font-black inline-block text-blue-600">85%</span></div>
                            </div>
                            <div class="overflow-hidden h-3 mb-4 text-xs flex rounded-full bg-slate-100">
                                <div style="width:85%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-600 transition-all duration-500"></div>
                            </div>
                            <p class="text-[10px] text-slate-400 font-bold italic">Sisa 15% alat sedang dalam pengajuan kalibrasi ke BPFK.</p>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-900 rounded-[2.5rem] shadow-2xl p-8 text-white relative overflow-hidden">
                    <div class="absolute -top-10 -left-10 w-32 h-32 bg-white/5 rounded-full"></div>
                    <div class="relative z-10">
                        <h3 class="font-black text-lg uppercase tracking-widest mb-8 border-b border-white/10 pb-4">Aksi Cepat</h3>
                        <div class="grid grid-cols-1 gap-4">
                            <a href="{{ route('assets.create') }}" class="group flex items-center justify-between bg-white/10 hover:bg-white text-white hover:text-slate-900 p-5 rounded-2xl transition-all border border-white/10">
                                <span class="text-xs font-black uppercase tracking-widest">Input Aset Baru</span>
                                <i class="fas fa-plus-circle group-hover:rotate-90 transition-transform"></i>
                            </a>
                            <a href="{{ route('laporan.index') }}" class="group flex items-center justify-between bg-white/10 hover:bg-emerald-500 text-white p-5 rounded-2xl transition-all border border-white/10">
                                <span class="text-xs font-black uppercase tracking-widest">Cetak Laporan</span>
                                <i class="fas fa-file-pdf group-hover:scale-110 transition-transform"></i>
                            </a>
                            <a href="{{ route('maintenance.log') }}" class="group flex items-center justify-between bg-white/10 hover:bg-blue-600 text-white p-5 rounded-2xl transition-all border border-white/10">
                                <span class="text-xs font-black uppercase tracking-widest">Riwayat Perbaikan</span>
                                <i class="fas fa-history group-hover:-rotate-12 transition-transform"></i>
                            </a>
                        </div>
                        
                        <div class="mt-12 bg-white/5 p-5 rounded-3xl border border-white/5">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Helpdesk IT Sarpras</p>
                            <a href="https://wa.me/6285875840001" class="text-xs font-bold text-emerald-400 hover:text-emerald-300 flex items-center gap-2">
                                <i class="fab fa-whatsapp"></i> Chat Support
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
