<?php

use App\Models\Target;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Livewire\Volt\Component;

new class extends Component {
    public $target;
    public ?string $filterDate = null;
    public int $perPage = 5;

    public function mount(string $id)
    {
        try {
            $id = decrypt($id);
            $this->target = Target::findOrFail($id);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            abort(403);
        }
    }

    public function getStatusesProperty()
    {
        return $this->target
            ->statuses()
            ->when($this->filterDate, fn ($q, $d) => $q->whereDate('checked_at', Carbon::parse($d)))
            ->orderByDesc('checked_at')
            ->paginate($this->perPage);
    }

    public function getStatusGroupsProperty()
    {
        return $this->statuses->groupBy(fn ($row) => $row->checked_at->toDateString());
    }

    public function getDailyUptimeProperty()
    {
        // last 7 days for this target
        $rows = $this->target->statuses()
            ->where('checked_at', '>=', now()->subDays(7))
            ->get()
            ->groupBy(fn ($r) => $r->checked_at->toDateString());

        return $rows->map(fn ($g) => [
            'date'    => $g->first()->checked_at->toDateString(),
            'uptime'  => $g->count() ? round(
                $g->whereBetween('status_code', [200,399])->count() / $g->count() * 100 , 1
            ) : 0,
        ])->sortBy('date')->values();
    }

    public function with()
    {
        return [
            'statuses'      => $this->statuses,       // for pagination links
            'statusGroups'  => $this->statusGroups,   // for grouped table
            'labels'        => $this->dailyUptime->pluck('date'),
            'values'        => $this->dailyUptime->pluck('uptime'),
        ];
    }
}; ?>

<div class="bg-white shadow rounded-xl p-6 space-y-6" wire:poll.10s
     x-data="{
        labels: @js($labels),
        values: @js($values),
        init() {
            const ctx = this.$refs.uptime.getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: { labels: this.labels, datasets: [{ data: this.values, borderWidth:2, tension:.4 }] },
                options: { plugins:{legend:{display:false}}, scales:{ y:{ beginAtZero:true, max:100 } } }
            });
        }
     }">

    <!-- HEADER + Filter row -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-lg font-semibold text-slate-800">
                Latest Status – <span class="text-sky-600">{{ $target->name }}</span>
            </h2>
            <p class="text-xs text-slate-500">Auto-refreshes every 10 seconds</p>
        </div>

        <div class="flex items-center gap-2">
            <input type="date" wire:model.live.debounce.500ms="filterDate"
                   class="border rounded px-2 py-1 text-sm" />
            <select wire:model.live="perPage" class="border rounded px-2 py-1 text-sm">
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="25">25</option>
            </select>
        </div>
    </div>

    <!-- 7-day uptime chart -->
    <div class="w-full h-48">
        <canvas x-ref="uptime" class="w-full h-full"></canvas>
    </div>

    <!-- GROUPED TABLE -->
    @forelse ($statusGroups as $date => $rows)
        <h3 class="mt-4 text-sm font-semibold text-slate-700">{{ Carbon::parse($date)->format('F j, Y') }}</h3>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left border rounded-md mb-6">
                <thead class="bg-slate-100 text-slate-700">
                <tr>
                    <th class="px-3 py-2">Time</th>
                    <th class="px-3 py-2">Status Code</th>
                    <th class="px-3 py-2">Latency&nbsp;(s)</th>
                    <th class="px-3 py-2">Message</th>
                </tr>
                </thead>
                <tbody class="divide-y">
                @foreach ($rows as $status)
                    @php
                        $badge = match(true) {
                            $status->status_code >= 200 && $status->status_code < 300 => 'bg-green-100 text-green-800',
                            $status->status_code >= 300 && $status->status_code < 400 => 'bg-blue-100 text-blue-800',
                            $status->status_code >= 400 && $status->status_code < 500 => 'bg-yellow-100 text-yellow-800',
                            $status->status_code >= 500 => 'bg-red-100 text-red-800',
                            default => 'bg-gray-100 text-gray-800',
                        };
                    @endphp
                    <tr>
                        <td class="px-3 py-2">{{ $status->checked_at->format('H:i:s') }}</td>
                        <td class="px-3 py-2">
                                <span class="inline-block px-2 py-0.5 rounded text-xs font-semibold {{ $badge }}">
                                    {{ $status->status_code }}
                                </span>
                        </td>
                        <td class="px-3 py-2">{{ number_format($status->latency, 3) }}</td>
                        <td class="px-3 py-2 text-slate-600">{{ $status->message ?? '—' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @empty
        <p class="text-slate-500 text-sm">No status checks found{{ $filterDate ? ' on '.$filterDate : '' }}.</p>
    @endforelse

    <!-- Pagination links -->
    <div>
        {{ $statuses->links() }}
    </div>

</div>

@scripts
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endscripts

