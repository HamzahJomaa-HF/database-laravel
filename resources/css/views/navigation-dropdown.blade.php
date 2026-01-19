<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <!-- Replace with your logo -->
                        <span class="text-xl font-bold text-blue-600">Hariri Foundation</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                @auth('employee')
                    @php
                        $employee = auth()->guard('employee')->user();
                        $hasFullAccess = $employee->hasFullAccess();
                        $canAccessDashboard = $employee->hasPermission('Dashboard') && $employee->hasPermission('Dashboard', 'view');
                    @endphp
                    
                    @if($hasFullAccess || $canAccessDashboard)
                    <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out {{ request()->routeIs('dashboard') ? 'border-blue-500 text-gray-900' : '' }}">
                            {{ __('Dashboard') }}
                        </a>
                    </div>
                    @endif
                @endauth
            </div>

            <!-- Settings Dropdown -->
            @auth('employee')
                @php
                    $employee = auth()->guard('employee')->user();
                @endphp
                <div class="hidden sm:flex sm:items-center sm:ml-6">
                    <div class="relative" x-data="{ open: false }">
                        <!-- Dropdown trigger -->
                        <button @click="open = !open" class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                            <div class="flex items-center">
                                <!-- Avatar/Icon -->
                                <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center text-white mr-2">
                                    {{ strtoupper(substr($employee->first_name, 0, 1) . substr($employee->last_name, 0, 1)) }}
                                </div>
                                <div>{{ $employee->first_name }} {{ $employee->last_name }}</div>
                                <div class="ml-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                        </button>

                        <!-- Dropdown content -->
                        <div x-show="open" @click.away="open = false" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-200">
                            
                            <!-- Account info -->
                            <div class="block px-4 py-2 text-xs text-gray-400 border-b border-gray-100">
                                {{ $employee->email }}
                            </div>

                            <!-- Profile -->
                            <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                {{ __('Profile') }}
                            </a>

                            <!-- Logout -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    {{ __('Logout') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endauth

            <!-- Hamburger (Mobile) -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu (Mobile) -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @auth('employee')
                @php
                    $employee = auth()->guard('employee')->user();
                    $hasFullAccess = $employee->hasFullAccess();
                    $canAccessDashboard = $employee->hasPermission('Dashboard') && $employee->hasPermission('Dashboard', 'view');
                @endphp
                
                @if($hasFullAccess || $canAccessDashboard)
                <a href="{{ route('dashboard') }}" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out {{ request()->routeIs('dashboard') ? 'border-blue-500 text-blue-700 bg-blue-50' : '' }}">
                    {{ __('Dashboard') }}
                </a>
                @endif
            @endauth
        </div>

        <!-- Responsive Settings Options -->
        @auth('employee')
            @php
                $employee = auth()->guard('employee')->user();
            @endphp
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="flex items-center px-4">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center text-white">
                            {{ strtoupper(substr($employee->first_name, 0, 1) . substr($employee->last_name, 0, 1)) }}
                        </div>
                    </div>
                    <div class="ml-3">
                        <div class="font-medium text-base text-gray-800">{{ $employee->first_name }} {{ $employee->last_name }}</div>
                        <div class="font-medium text-sm text-gray-500">{{ $employee->email }}</div>
                    </div>
                </div>

                <div class="mt-3 space-y-1">
                    <!-- Profile -->
                    <a href="{{ route('profile.show') }}" class="block pl-3 pr-4 py-2 text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 focus:outline-none focus:text-gray-800 focus:bg-gray-50 transition duration-150 ease-in-out">
                        {{ __('Profile') }}
                    </a>

                    <!-- Logout -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left pl-3 pr-4 py-2 text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 focus:outline-none focus:text-gray-800 focus:bg-gray-50 transition duration-150 ease-in-out">
                            {{ __('Logout') }}
                        </button>
                    </form>
                </div>
            </div>
        @endauth
    </div>
</nav>