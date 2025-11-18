<nav x-data="{ open: false }" class="bg-white/95 backdrop-blur-md border-b border-gray-200/50 shadow-sm sticky top-0 z-50">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 group">
                        <x-application-logo class="block h-9 w-auto fill-current text-indigo-600 group-hover:text-indigo-700 transition-colors" />
                        <span class="hidden md:block text-xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">TheTradeVisor</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    <!-- Statistics Dropdown -->
                    <div class="hidden sm:flex sm:items-center">
                        <x-dropdown align="left" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                    <div>Statistics</div>
                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('performance')" :active="request()->routeIs('performance')">
                                    {{ __('My Performance') }}
                                </x-dropdown-link>

                                <x-dropdown-link :href="route('account.health')" :active="request()->routeIs('account.health') || request()->routeIs('account.snapshots')">
                                    {{ __('Account Health') }}
                                </x-dropdown-link>

                                <x-dropdown-link :href="route('analytics')" :active="request()->routeIs('analytics')">
                                    {{ __('Global Analytics') }}
                                </x-dropdown-link>

                                <x-dropdown-link :href="route('broker.analytics')" :active="request()->routeIs('broker.analytics')">
                                    {{ __('Broker Analytics') }}
                                </x-dropdown-link>

                                <x-dropdown-link :href="route('digest.show')" :active="request()->routeIs('digest.show')">
                                    {{ __('My Digest') }}
                                </x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                    </div>

                    @if(Auth::user()->is_admin)
                        <!-- Admin Dropdown -->
                        <div class="hidden sm:flex sm:items-center sm:ms-6">
                            <x-dropdown align="left" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                        <div>Admin</div>

                                        <div class="ms-1">
                                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <x-dropdown-link :href="route('admin.dashboard')">
                                        {{ __('Admin Dashboard') }}
                                    </x-dropdown-link>

                                    <x-dropdown-link :href="route('admin.users.index')">
                                        {{ __('User Management') }}
                                    </x-dropdown-link>

                                    <x-dropdown-link :href="route('admin.trades.index')">
                                        {{ __('Trade Management') }}
                                    </x-dropdown-link>

                                    <x-dropdown-link :href="route('admin.accounts.index')">
                                        {{ __('Accounts Management') }}
                                    </x-dropdown-link>

                                    <x-dropdown-link :href="route('admin.rate-limits.index')">
                                        {{ __('Rate Limits') }}
                                    </x-dropdown-link>

                                    <x-dropdown-link :href="route('admin.circuit-breakers.index')">
                                        {{ __('Circuit Breakers') }}
                                    </x-dropdown-link>

                                    <x-dropdown-link :href="route('admin.digest-control.index')">
                                        {{ __('Digest Control') }}
                                    </x-dropdown-link>

                                    <!-- Admin Wiki -->
                                    <div class="border-t border-gray-100"></div>
                                    
                                    <x-dropdown-link :href="route('admin.wiki')">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                            </svg>
                                            {{ __('📚 Admin Wiki') }}
                                        </span>
                                    </x-dropdown-link>

                                    <!-- Monitoring & Debug Tools -->
                                    <div class="border-t border-gray-100"></div>

                                    <x-dropdown-link href="/horizon" target="_blank">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                            </svg>
                                            {{ __('Queue Monitor (Horizon)') }}
                                        </span>
                                    </x-dropdown-link>

                                    @if(config('telescope.enabled'))
                                    <x-dropdown-link href="/telescope" target="_blank">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                            </svg>
                                            {{ __('Debug Assistant (Telescope)') }}
                                        </span>
                                    </x-dropdown-link>
                                    @endif

                                    <div class="border-t border-gray-100"></div>

                                    <x-dropdown-link :href="route('settings.currency')">
                                        {{ __('Currency Settings') }}
                                    </x-dropdown-link>

                                    <x-dropdown-link :href="route('admin.symbols.index')">
                                        {{ __('Symbol Management') }}
                                    </x-dropdown-link>

                                    <x-dropdown-link :href="route('admin.logs')">
                                        {{ __('System Logs') }}
                                    </x-dropdown-link>

                                    <x-dropdown-link :href="route('admin.services')">
                                        {{ __('Service Management') }}
                                    </x-dropdown-link>

                                </x-slot>
                            </x-dropdown>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('accounts.index')" :active="request()->routeIs('accounts.*')">
                            {{ __('Accounts') }}
                        </x-dropdown-link>

                        <x-dropdown-link :href="route('settings.api-key')" :active="request()->routeIs('settings.*')">
                            {{ __('API Key') }}
                        </x-dropdown-link>

                        <x-dropdown-link :href="route('download')" :active="request()->routeIs('download')">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                {{ __('Download EA') }}
                            </span>
                        </x-dropdown-link>

                        <div class="border-t border-gray-100"></div>

                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            <!-- Statistics Section -->
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800">Statistics</div>
                </div>
                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('performance')" :active="request()->routeIs('performance')">
                        {{ __('My Performance') }}
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('account.health')" :active="request()->routeIs('account.health') || request()->routeIs('account.snapshots')">
                        {{ __('Account Health') }}
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('analytics')" :active="request()->routeIs('analytics')">
                        {{ __('Global Analytics') }}
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('broker.analytics')" :active="request()->routeIs('broker.analytics')">
                        {{ __('Brokers Analytics') }}
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('digest.show')" :active="request()->routeIs('digest.show')">
                        {{ __('My Digest') }}
                    </x-responsive-nav-link>
                </div>
            </div>

            @if(Auth::user()->is_admin)
                <div class="pt-4 pb-1 border-t border-gray-200">
                    <div class="px-4">
                        <div class="font-medium text-base text-gray-800">Admin Panel</div>
                    </div>

                    <div class="mt-3 space-y-1">
                        <x-responsive-nav-link :href="route('admin.dashboard')">
                            {{ __('Admin Dashboard') }}
                        </x-responsive-nav-link>

			    <x-responsive-nav-link :href="route('admin.users.index')">
			        {{ __('User Management') }}
			    </x-responsive-nav-link>

                        <x-responsive-nav-link :href="route('admin.logs')">
                            {{ __('System Logs') }}
                        </x-responsive-nav-link>

                        <x-responsive-nav-link :href="route('admin.services')">
                            {{ __('Service Management') }}
                        </x-responsive-nav-link>
                        
                        <x-responsive-nav-link :href="route('admin.wiki')">
                            {{ __('📚 Admin Wiki') }}
                        </x-responsive-nav-link>

                        <x-responsive-nav-link href="/horizon" target="_blank">
                            {{ __('Queue Monitor (Horizon)') }}
                        </x-responsive-nav-link>

                        @if(config('telescope.enabled'))
                        <x-responsive-nav-link href="/telescope" target="_blank">
                            {{ __('Debug Assistant (Telescope)') }}
                        </x-responsive-nav-link>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('accounts.index')" :active="request()->routeIs('accounts.*')">
                    {{ __('Accounts') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('settings.api-key')" :active="request()->routeIs('settings.*')">
                    {{ __('API Key') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('download')" :active="request()->routeIs('download')">
                    {{ __('📥 Download EA') }}
                </x-responsive-nav-link>

                <div class="border-t border-gray-200"></div>

                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
