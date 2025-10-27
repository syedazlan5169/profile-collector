<x-layouts.app :title="__('Person Details')">
    <div class="max-w-5xl mx-auto space-y-6">
        <!-- Back Button and Edit Button -->
        <div class="flex justify-between items-center">
            <a href="{{ route('people.index') }}" class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 transition-colors duration-200">
                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to People
            </a>
            <a href="{{ route('people.edit', $person) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition-colors duration-200 shadow-sm">
                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Person
            </a>
        </div>

        <!-- Profile Header Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg dark:shadow-gray-900/30 overflow-hidden border border-gray-200 dark:border-gray-700">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700 h-32"></div>
            <div class="px-8 pb-8">
                <div class="flex items-end -mt-16 mb-6">
                    <div class="h-32 w-32 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 dark:from-blue-500 dark:to-blue-700 flex items-center justify-center text-white text-4xl font-bold shadow-xl ring-4 ring-white dark:ring-gray-800">
                        {{ strtoupper(substr($person->name, 0, 1)) }}
                    </div>
                    <div class="ml-6 mb-4">
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $person->name }}</h1>
                        @if($person->rank)
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $person->rank }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Personal Information Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md dark:shadow-gray-900/30 border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center">
                    <svg class="h-5 w-5 mr-2 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <!-- user-circle -->
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14a5 5 0 00-5 5v1h10v-1a5 5 0 00-5-5zm0-2a4 4 0 100-8 4 4 0 000 8z"/>
                    </svg>
                    Personal Information
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- NRIC -->
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <!-- identification card -->
                                    <rect x="3" y="4" width="18" height="14" rx="2" ry="2"></rect>
                                    <circle cx="8" cy="10" r="2"></circle>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9h6M12 13h6"/>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">NRIC</p>
                            <p class="mt-1 text-sm font-mono text-gray-900 dark:text-gray-100">{{ $person->nric }}</p>
                        </div>
                    </div>

                    <!-- Date of Birth -->
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                <svg class="h-5 w-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <!-- calendar -->
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2zM3 11h18"/>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Date of Birth</p>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ \Carbon\Carbon::parse($person->date_of_birth)->format('F d, Y') }}</p>
                        </div>
                    </div>

                    <!-- Gender -->
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                                <svg class="h-5 w-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <!-- user -->
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Gender</p>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ ucfirst($person->gender) }}</p>
                        </div>
                    </div>

                    <!-- Phone -->
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-lg bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center">
                                <svg class="h-5 w-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <!-- phone -->
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.3a1 1 0 01.95.69l1.5 4.5a1 1 0 01-.5 1.2l-2.26 1.13a11 11 0 005.51 5.51l1.13-2.26a1 1 0 011.2-.5l4.5 1.5a1 1 0 01.69.95V19a2 2 0 01-2 2h-1C9.72 21 3 14.28 3 6V5z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Phone</p>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $person->phone }}</p>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="flex items-start space-x-3 md:col-span-2">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-lg bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                                <svg class="h-5 w-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <!-- envelope -->
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l9 6 9-6M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Email</p>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100 break-all">{{ $person->email }}</p>
                        </div>
                    </div>

                    <!-- Address -->
                    <div class="flex items-start space-x-3 md:col-span-2">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                                <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <!-- map pin -->
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21s-6-5.373-6-10a6 6 0 1112 0c0 4.627-6 10-6 10z"/>
                                    <circle cx="12" cy="11" r="2" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Address</p>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $person->address }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Professional Information Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md dark:shadow-gray-900/30 border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center">
                    <svg class="h-5 w-5 mr-2 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <!-- briefcase -->
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6V4a2 2 0 012-2h0a2 2 0 012 2v2m-8 0h12a2 2 0 012 2v9a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2z"/>
                    </svg>
                    Professional Information
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Department -->
                    @if($person->department)
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                                <svg class="h-5 w-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <!-- office building -->
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18M6 21V5a2 2 0 012-2h8a2 2 0 012 2v16M9 8h2m2 0h2M9 12h2m2 0h2M9 16h2m2 0h2"/>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Department</p>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $person->department }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Rank -->
                    @if($person->rank)
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-lg bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
                                <svg class="h-5 w-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <!-- medal -->
                                    <circle cx="12" cy="8" r="3" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 11l-2 7 5-3 5 3-2-7"/>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Rank</p>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $person->rank }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- PK Number -->
                    @if($person->pk_number)
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-lg bg-sky-100 dark:bg-sky-900/30 flex items-center justify-center">
                                <svg class="h-5 w-5 text-sky-600 dark:text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <!-- id tag -->
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h6l7 7-6 6-7-7V7z"/>
                                    <circle cx="9.5" cy="9.5" r="1"/>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">PK Number</p>
                            <p class="mt-1 text-sm font-mono text-gray-900 dark:text-gray-100">{{ $person->pk_number }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Union Number -->
                    @if($person->union_number)
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-lg bg-violet-100 dark:bg-violet-900/30 flex items-center justify-center">
                                <svg class="h-5 w-5 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <!-- document -->
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4h7l4 4v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 4v4h4M9 13h6M9 17h6M9 9h2"/>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Union Number</p>
                            <p class="mt-1 text-sm font-mono text-gray-900 dark:text-gray-100">{{ $person->union_number }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Vehicle Information Card -->
        @if($person->car)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md dark:shadow-gray-900/30 border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center">
                    <svg class="h-5 w-5 mr-2 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <!-- steering wheel -->
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3a9 9 0 110 18 9 9 0 010-18zm0 7a7 7 0 016.93 6H5.07A7 7 0 0112 10zm0 0v4"/>
                    </svg>
                    Vehicle Information
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Make -->
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-lg bg-cyan-100 dark:bg-cyan-900/30 flex items-center justify-center">
                                <svg class="h-5 w-5 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <!-- wrench -->
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.7 6.3a4 4 0 01-5.66 5.66L3 18v3h3l6.04-6.04a4 4 0 005.66-5.66l-3-3z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Make</p>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $person->car['make'] }}</p>
                        </div>
                    </div>

                    <!-- Model -->
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-lg bg-pink-100 dark:bg-pink-900/30 flex items-center justify-center">
                                <svg class="h-5 w-5 text-pink-600 dark:text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <!-- cube -->
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3l7 4v10l-7 4-7-4V7l7-4zM12 3v10m7-6l-7 4m-7-4l7 4"/>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Model</p>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $person->car['model'] }}</p>
                        </div>
                    </div>

                    <!-- Color -->
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-lg bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
                                <svg class="h-5 w-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <!-- droplet -->
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3C9 7 6 9.5 6 13a6 6 0 0012 0c0-3.5-3-6-6-10z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Color</p>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ ucfirst($person->car['color']) }}</p>
                        </div>
                    </div>

                    <!-- Plate -->
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-lg bg-teal-100 dark:bg-teal-900/30 flex items-center justify-center">
                                <svg class="h-5 w-5 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <!-- license plate -->
                                    <rect x="3" y="7" width="18" height="10" rx="2" ry="2"></rect>
                                    <circle cx="6" cy="10" r="1"></circle>
                                    <circle cx="18" cy="10" r="1"></circle>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 13h8"/>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">License Plate</p>
                            <p class="mt-1 text-sm font-mono font-semibold text-gray-900 dark:text-gray-100">{{ strtoupper($person->car['plate']) }}</p>
                        </div>
                    </div>
                </div>
            </div>
    </div>
        @endif
    </div>
</x-layouts.app>
