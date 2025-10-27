@php
    $totalPeople = \App\Models\Person::count();
    $maleCount = \App\Models\Person::where('gender', 'Male')->count();
    $femaleCount = \App\Models\Person::where('gender', 'Female')->count();
    $recentAdditions = \App\Models\Person::where('created_at', '>=', now()->subDays(7))->count();
    $averageAge = \App\Models\Person::whereNotNull('date_of_birth')
        ->get()
        ->avg(function($person) {
            return $person->date_of_birth ? $person->date_of_birth->age : 0;
        });
    
    $departmentStats = \App\Models\Person::selectRaw('department, COUNT(*) as count')
        ->whereNotNull('department')
        ->groupBy('department')
        ->orderByDesc('count')
        ->limit(5)
        ->get();
    
    $rankStats = \App\Models\Person::selectRaw('`rank`, COUNT(*) as count')
        ->whereNotNull('rank')
        ->groupBy('rank')
        ->orderByDesc('count')
        ->limit(5)
        ->get();
@endphp

<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <a href="{{ route('people.create') }}" class="absolute inset-0 size-full bg-black/50 text-white flex items-center justify-center">
                    <span class="text-2xl font-bold">
                        {{ __('Add Person') }}
                    </span>
                </a>
            </div>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <a href="{{ route('people.index') }}" class="absolute inset-0 size-full bg-black/50 text-white flex items-center justify-center">
                    <span class="text-2xl font-bold">
                        {{ __('People') }}
                    </span>
                </a>
            </div>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <a href="{{ route('people.import') }}" class="absolute inset-0 size-full bg-black/50 text-white flex items-center justify-center">
                    <span class="text-2xl font-bold">
                        {{ __('Import People') }}
                    </span>
                </a>
            </div>
        </div>

        <!-- Statistics Section -->
        <div class="relative h-full flex-1 overflow-auto rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Overview Statistics</h2>
            
            <!-- Key Metrics -->
            <div class="grid gap-4 md:grid-cols-4 mb-8">
                <div class="rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 p-6 text-white shadow-lg">
                    <div class="text-sm font-medium opacity-90">Total People</div>
                    <div class="mt-2 text-4xl font-bold">{{ number_format($totalPeople) }}</div>
                    <div class="mt-2 text-xs opacity-75">Registered in system</div>
                </div>
                
                <div class="rounded-lg bg-gradient-to-br from-green-500 to-green-600 p-6 text-white shadow-lg">
                    <div class="text-sm font-medium opacity-90">Recent Additions</div>
                    <div class="mt-2 text-4xl font-bold">{{ $recentAdditions }}</div>
                    <div class="mt-2 text-xs opacity-75">Last 7 days</div>
                </div>
                
                <div class="rounded-lg bg-gradient-to-br from-purple-500 to-purple-600 p-6 text-white shadow-lg">
                    <div class="text-sm font-medium opacity-90">Average Age</div>
                    <div class="mt-2 text-4xl font-bold">{{ number_format($averageAge, 0) }}</div>
                    <div class="mt-2 text-xs opacity-75">Years old</div>
                </div>
                
                <div class="rounded-lg bg-gradient-to-br from-orange-500 to-orange-600 p-6 text-white shadow-lg">
                    <div class="text-sm font-medium opacity-90">Departments</div>
                    <div class="mt-2 text-4xl font-bold">{{ $departmentStats->count() }}</div>
                    <div class="mt-2 text-xs opacity-75">Active departments</div>
                </div>
            </div>

            <!-- Gender Distribution & Department Breakdown -->
            <div class="grid gap-6 md:grid-cols-2 mb-8">
                <!-- Gender Distribution -->
                <div class="rounded-lg border border-neutral-200 bg-neutral-50 p-6 dark:border-neutral-700 dark:bg-neutral-800">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Gender Distribution</h3>
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Male</span>
                                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $maleCount }} ({{ $totalPeople > 0 ? number_format(($maleCount / $totalPeople) * 100, 1) : 0 }}%)</span>
                            </div>
                            <div class="h-3 w-full rounded-full bg-gray-200 dark:bg-gray-700">
                                <div class="h-3 rounded-full bg-blue-500" style="width: {{ $totalPeople > 0 ? ($maleCount / $totalPeople) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Female</span>
                                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $femaleCount }} ({{ $totalPeople > 0 ? number_format(($femaleCount / $totalPeople) * 100, 1) : 0 }}%)</span>
                            </div>
                            <div class="h-3 w-full rounded-full bg-gray-200 dark:bg-gray-700">
                                <div class="h-3 rounded-full bg-pink-500" style="width: {{ $totalPeople > 0 ? ($femaleCount / $totalPeople) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top Departments -->
                <div class="rounded-lg border border-neutral-200 bg-neutral-50 p-6 dark:border-neutral-700 dark:bg-neutral-800">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Top Departments</h3>
                    @if($departmentStats->count() > 0)
                        <div class="space-y-3">
                            @foreach($departmentStats as $dept)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="h-8 w-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-xs font-bold">
                                            {{ substr($dept->department, 0, 1) }}
                                        </div>
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $dept->department ?? 'N/A' }}</span>
                                    </div>
                                    <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $dept->count }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">No department data available</p>
                    @endif
                </div>
            </div>

            <!-- Rank Distribution -->
            <div class="rounded-lg border border-neutral-200 bg-neutral-50 p-6 dark:border-neutral-700 dark:bg-neutral-800">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Rank Distribution</h3>
                @if($rankStats->count() > 0)
                    <div class="grid gap-3 md:grid-cols-5">
                        @php
                            $colors = ['bg-red-500', 'bg-yellow-500', 'bg-green-500', 'bg-blue-500', 'bg-indigo-500'];
                        @endphp
                        @foreach($rankStats as $index => $rank)
                            <div class="rounded-lg border border-neutral-300 bg-white p-4 dark:border-neutral-600 dark:bg-neutral-900">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="h-3 w-3 rounded-full {{ $colors[$index % 5] }}"></div>
                                    <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ $rank->count }}</span>
                                </div>
                                <div class="text-sm font-medium text-gray-700 dark:text-gray-300 truncate">{{ $rank->rank ?? 'N/A' }}</div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400">No rank data available</p>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
