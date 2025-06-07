<?php

use App\Models\Status;
use Livewire\Volt\Component;

new class extends Component {
    public string $range = '24h';        // 24h | 7d | 30d

    /* --------------------------------------------------------------------- */
    /*  DATA AGGREGATION                                                     */
    /* --------------------------------------------------------------------- */

    /** Hourly stacked-bar counts */
    public function getStatusSummaryProperty()
    {
        $from = $this->range === '7d'
            ? now()->subDays(7)
            : ($this->range === '30d' ? now()->subDays(30) : now()->subDay());

        return Status::where('checked_at', '>=', $from)
            ->get()
            ->groupBy(fn($s) => $s->checked_at->format('Y-m-d H'))   // by hour
            ->map(function ($g) {
                return [
                    'ok' => $g->whereBetween('status_code', [200, 399])->count(),
                    'failed' => $g->where('status_code', '>=', 400)->count(),
                    'timeout' => $g->where('status_code', 0)->count(),
                    'nomatch' => $g->whereNull('status_code')->count(),
                ];
            });
    }

    /** Latest status **per target** – used for tool-tips */
    public function getSiteListsProperty(): array
    {
        $from = $this->range === '7d'
            ? now()->subDays(7)
            : ($this->range === '30d' ? now()->subDays(30) : now()->subDay());

        // get the most-recent status row inside the window for each target
        $latest = Status::where('checked_at', '>=', $from)
            ->with('target:id,name')
            ->orderByDesc('checked_at')
            ->get()
            ->unique('target_id');

        $lists = ['ok' => [], 'failed' => [], 'timeout' => [], 'nomatch' => []];

        foreach ($latest as $row) {
            $name = $row->target->name ?? 'unknown';
            if ($row->status_code === 0) $lists['timeout'][] = $name;
            elseif (is_null($row->status_code)) $lists['nomatch'][] = $name;
            elseif ($row->status_code >= 400) $lists['failed'][] = $name;
            else                                   $lists['ok'][] = $name;
        }

        return $lists;
    }

    /* expose to Blade */
    public function with()
    {
        return [
            'statusSummary' => $this->statusSummary,
            'siteLists' => $this->siteLists,
        ];
    }
};
?>
<div class="space-y-2">

    <!-- Range selector ----->
    <div class="flex gap-4 text-sm text-gray-600">
        <span wire:click="$set('range','24h')"
              class="cursor-pointer {{ $range==='24h'  ? 'text-green-600 font-semibold' : '' }}">Last 24 hours</span>
        <span wire:click="$set('range','7d')"
              class="cursor-pointer {{ $range==='7d'   ? 'text-green-600 font-semibold' : '' }}">Last 7 days</span>
        <span wire:click="$set('range','30d')"
              class="cursor-pointer {{ $range==='30d'  ? 'text-green-600 font-semibold' : '' }}">Last 30 days</span>
    </div>

    <!-- Quick “overall” banner with tool-tips ------------------- -->
    @php
        $hasFailure = !empty($siteLists['failed']);
        $hasTimeout = !empty($siteLists['timeout']);
        $hasNomatch = !empty($siteLists['nomatch']);
        $allOk      = !($hasFailure || $hasTimeout || $hasNomatch);
    @endphp

    <div class="grid gap-2">
        {{-- ALL OK -------- --}}
        @if ($allOk)
            <div class="relative" x-data="{open:false}">
                <div @mouseenter="open=true" @mouseleave="open=false"
                     class="w-full h-48 p-2 bg-green-300 text-black rounded cursor-default flex items-center justify-center relative overflow-hidden">

                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         x-cloak
                         class="absolute top-2 left-1/2 -translate-x-1/2 bg-white text-gray-800 text-xs rounded shadow p-2 max-h-40 overflow-auto w-56 z-10">
                        <strong class="block mb-1">OK Sites ({{ count($siteLists['ok']) }})</strong>
                        <ul class="list-disc pl-4 space-y-0.5 text-left">
                            @foreach ($siteLists['ok'] as $site)
                                <li>{{ $site }}</li>
                            @endforeach
                        </ul>
                    </div>

                    <span>All sites are up 🟢</span>
                </div>
            </div>
        @else
            {{-- FAILED ---- --}}
            @if ($hasFailure)
                <div class="relative" x-data="{open:false}">
                    <div @mouseenter="open=true" @mouseleave="open=false"
                         class="w-full h-48 p-2 bg-rad text-white rounded cursor-default flex items-center justify-center relative overflow-hidden">

                        <!-- Tooltip with fade and dynamic height -->
                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-200"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             x-cloak
                             class="absolute top-2 left-1/2 -translate-x-1/2 bg-white text-gray-800 text-xs rounded shadow p-2 max-h-40 overflow-auto w-56 z-10">
                            <strong class="block mb-1">Down Sites ({{ count($siteLists['failed']) }})</strong>
                            <ul class="list-disc pl-4 space-y-0.5 text-left">
                                @foreach ($siteLists['failed'] as $site)
                                    <li>{{ $site }}</li>
                                @endforeach
                            </ul>
                        </div>

                        <span>Some sites are down 🔴</span>
                    </div>
                </div>
            @endif

            {{-- TIMEOUT --- --}}
            @if ($hasTimeout)
                <div class="relative" x-data="{open:false}">
                    <div @mouseenter="open=true" @mouseleave="open=false"
                         class="w-full h-48 p-2 bg-purple-500 text-white rounded cursor-default flex items-center justify-center relative overflow-hidden">

                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-200"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             x-cloak
                             class="absolute top-2 left-1/2 -translate-x-1/2 bg-white text-gray-800 text-xs rounded shadow p-2 max-h-40 overflow-auto w-56 z-10">
                            <strong class="block mb-1">Timeout Sites ({{ count($siteLists['timeout']) }})</strong>
                            <ul class="list-disc pl-4 space-y-0.5 text-left">
                                @foreach ($siteLists['timeout'] as $site)
                                    <li>{{ $site }}</li>
                                @endforeach
                            </ul>
                        </div>

                        <span>Some sites timed out ⏱</span>
                    </div>
                </div>
            @endif

            {{-- NO-MATCH -- --}}
            @if ($hasNomatch)
                <div class="relative" x-data="{open:false}">
                    <div @mouseenter="open=true" @mouseleave="open=false"
                         class="w-full h-48 p-2 bg-yellow-400 text-white rounded cursor-default flex items-center justify-center relative overflow-hidden">

                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-200"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             x-cloak
                             class="absolute top-2 left-1/2 -translate-x-1/2 bg-white text-gray-800 text-xs rounded shadow p-2 max-h-40 overflow-auto w-56 z-10">
                            <strong class="block mb-1">No Match Sites ({{ count($siteLists['nomatch']) }})</strong>
                            <ul class="list-disc pl-4 space-y-0.5 text-left">
                                @foreach ($siteLists['nomatch'] as $site)
                                    <li>{{ $site }}</li>
                                @endforeach
                            </ul>
                        </div>

                        <span>Some sites had no match ❔</span>
                    </div>
                </div>
            @endif

        @endif
    </div>

    <!-- Legend ---------- -->
    <div class="flex gap-4 text-xs text-gray-500 mt-3">
        <div><span class="inline-block w-3 h-3 bg-green-500  rounded mr-1"></span>OK</div>
        <div><span class="inline-block w-3 h-3 bg-rad    rounded mr-1"></span>Failed</div>
        <div><span class="inline-block w-3 h-3 bg-yellow-400 rounded mr-1"></span>No-Match</div>
        <div><span class="inline-block w-3 h-3 bg-purple-500 rounded mr-1"></span>Timeout</div>
    </div>
</div>

