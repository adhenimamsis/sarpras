<nav x-data="{ open: false }" class="theme-welcome-soft-panel sticky top-0 z-30 border-b border-slate-200/80 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 group">
                        <img src="https://www.svgrepo.com/show/422116/health-hospital-medical.svg" class="block h-8 w-auto opacity-80 transition-opacity group-hover:opacity-100" alt="Logo">
                        <span class="hidden lg:block text-sm font-semibold tracking-tight text-slate-800">
                            {{ \App\Models\Setting::getValue('nama_puskesmas', 'SimSarpras') }}
                        </span>
                    </a>
                </div>

                <div class="hidden space-x-6 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        <x-filament::icon icon="heroicon-m-squares-2x2" class="w-4 h-4 mr-1"/>
                        Dashboard
                    </x-nav-link>
                    
                    <x-nav-link href="/admin">
                        <x-filament::icon icon="heroicon-m-cog-6-tooth" class="w-4 h-4 mr-1"/>
                        Panel Admin
                    </x-nav-link>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm font-medium leading-4 text-slate-700 shadow-sm transition ease-in-out duration-150 hover:border-slate-400 hover:text-slate-900">
                            <div class="flex items-center gap-2">
                                <div class="flex h-7 w-7 items-center justify-center rounded-full bg-blue-800 text-[10px] text-white">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                </div>
                                {{ Auth::user()->name }}
                            </div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="border-b border-slate-200 px-4 py-2">
                            <p class="text-[10px] font-semibold tracking-[0.08em] text-slate-500">Akun Login</p>
                            <p class="truncate text-xs font-semibold text-slate-700">{{ Auth::user()->email }}</p>
                        </div>

                        <x-dropdown-link :href="route('profile.edit')">
                            Profil Saya
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    class="font-semibold text-rose-600 hover:text-rose-700"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                Keluar (Log Out)
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center rounded-md p-2 text-slate-500 transition duration-150 ease-in-out hover:bg-slate-100 hover:text-slate-700 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden border-t border-slate-200 bg-white sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                Dashboard
            </x-responsive-nav-link>
            <x-responsive-nav-link href="/admin">
                Panel Admin
            </x-responsive-nav-link>
        </div>

        <div class="border-t border-slate-200 pb-1 pt-4">
            <div class="px-4 flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-800 font-semibold text-white">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div>
                    <div class="text-base font-semibold text-slate-800">{{ Auth::user()->name }}</div>
                    <div class="text-sm text-slate-500">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    Profil Saya
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            class="text-rose-600"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                        Keluar (Log Out)
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>

