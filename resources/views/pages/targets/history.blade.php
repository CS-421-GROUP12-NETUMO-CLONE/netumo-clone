<?php

use App\Models\Target;
use Illuminate\Support\Facades\Log;
use Livewire\Volt\Component;

new class extends Component {
    use Livewire\WithPagination;

    public $target;

    /* ───────── UI state ───────── */
    public string $tab = 'statuses'; // statuses | certificates | alerts
    public string $search = '';
    public ?string $date = null;     // YYYY-MM-DD
    public int $perPage = 10;

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

    /* reset page on filters */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingDate()
    {
        $this->resetPage();
    }

    public function updatingTab()
    {
        $this->resetPage();
    }

    /* ───────── Data getters ───────── */
    public function getRowsProperty()
    {
        return match ($this->tab) {

            'certificates' => $this->target->certificates()
                ->when($this->date, fn($q, $d) => $q->whereDate('checked_at', $d))
                ->when($this->search, fn($q, $term) => $q->where('ssl_expiry_date', 'like', "%{$term}%")
                    ->orWhere('domain_expiry_date', 'like', "%{$term}%"))
                ->orderByDesc('checked_at')
                ->paginate($this->perPage),

            'alerts' => $this->target->alerts()
                ->when($this->date, fn($q, $d) => $q->whereDate('created_at', $d))
                ->when($this->search, fn($q, $term) => $q->where('message', 'like', "%{$term}%")
                    ->orWhere('type', 'like', "%{$term}%"))
                ->orderByDesc('created_at')
                ->paginate($this->perPage),

            /* default → statuses */
            default => $this->target->statuses()
                ->when($this->date, fn($q, $d) => $q->whereDate('checked_at', $d))
                ->when($this->search, fn($q, $term) => $q->where('status_code', 'like', "%{$term}%"))
                ->orderByDesc('checked_at')
                ->paginate($this->perPage),
        };
    }

    public function with()
    {
        return ['rows' => $this->rows];
    }
}; ?>

<div class="bg-white shadow rounded-xl p-6 space-y-6" wire:poll.30s>

    <!-- Title -->
    <h2 class="text-lg font-semibold text-slate-800">History – {{ $target->name }}</h2>

    <!-- Tabs -->
    <div class="flex flex-wrap gap-2 text-sm">
        @foreach (['statuses','certificates','alerts'] as $t)
            <button wire:click="$set('tab','{{ $t }}')"
                    class="px-3 py-1 rounded-md
                           {{ $tab === $t ? 'bg-sky-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                {{ ucfirst($t) }}
            </button>
        @endforeach
    </div>

    <!-- Filters -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <div class="flex gap-2">
            <input type="date" wire:model.live.debounce.500ms="date"
                   class="border rounded px-2 py-1 text-sm"/>
            <input type="text" placeholder="Search…" wire:model..live.debounce.500ms="search"
                   class="border rounded px-2 py-1 text-sm w-48"/>
        </div>
        <select wire:model.live="perPage" class="border rounded px-2 py-1 text-sm">
            <option value="10">10 / page</option>
            <option value="25">25</option>
            <option value="50">50</option>
        </select>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left border rounded-md">
            <thead class="bg-slate-100 text-slate-700">
            <tr>
                @if ($tab === 'statuses')
                    <th class="px-3 py-2">Checked At</th>
                    <th class="px-3 py-2">Code</th>
                    <th class="px-3 py-2">Latency&nbsp;(s)</th>
                @elseif ($tab === 'certificates')
                    <th class="px-3 py-2">Checked At</th>
                    <th class="px-3 py-2">SSL&nbsp;Expiry</th>
                    <th class="px-3 py-2">Domain&nbsp;Expiry</th>
                    <th class="px-3 py-2">Days&nbsp;Left</th>
                @else
                    {{-- alerts --}}
                    <th class="px-3 py-2">Date</th>
                    <th class="px-3 py-2">Type</th>
                    <th class="px-3 py-2">Message</th>
                @endif
            </tr>
            </thead>

            <tbody class="divide-y">
            @forelse ($rows as $row)
                @if ($tab === 'statuses')
                    @php
                        $badge = match(true) {
                            $row->status_code >= 200 && $row->status_code < 300 => 'bg-green-100 text-green-800',
                            $row->status_code >= 300 && $row->status_code < 400 => 'bg-blue-100 text-blue-800',
                            $row->status_code >= 400 && $row->status_code < 500 => 'bg-yellow-100 text-yellow-800',
                            $row->status_code >= 500 => 'bg-red-100 text-red-800',
                            default => 'bg-gray-100 text-gray-800',
                        };
                    @endphp
                    <tr>
                        <td class="px-3 py-2">{{ $row->checked_at->format('Y-m-d H:i:s') }}</td>
                        <td class="px-3 py-2">
                            <span class="inline-block px-2 py-0.5 rounded {{ $badge }} font-semibold">
                                {{ $row->status_code }}
                            </span>
                        </td>
                        <td class="px-3 py-2">{{ number_format($row->latency, 3) }}</td>
                    </tr>
                @elseif ($tab === 'certificates')
                    @php
                        $days = $row->days_to_ssl_expiry ?? $row->days_to_domain_expiry;
                        $colour = $days <= 7  ? 'text-red-600'
                                : ($days <= 30 ? 'text-yellow-600' : 'text-green-600');
                    @endphp
                    <tr>
                        <td class="px-3 py-2">{{ $row->checked_at->format('Y-m-d') }}</td>
                        <td class="px-3 py-2">{{ $row->ssl_expiry_date?->format('Y-m-d') ?? '—' }}</td>
                        <td class="px-3 py-2">{{ $row->domain_expiry_date?->format('Y-m-d') ?? '—' }}</td>
                        <td class="px-3 py-2 font-semibold {{ $colour }}">{{ $days ?? '—' }}</td>
                    </tr>
                @else
                    {{-- alerts --}}
                    @php
                        $colour = [
                            'downtime' => 'bg-red-100 text-red-800',
                            'ssl'      => 'bg-yellow-100 text-yellow-800',
                            'domain'   => 'bg-blue-100 text-blue-800',
                        ][$row->type] ?? 'bg-gray-100 text-gray-800';
                    @endphp
                    <tr>
                        <td class="px-3 py-2">{{ $row->created_at->format('Y-m-d H:i') }}</td>
                        <td class="px-3 py-2">
                            <span class="inline-block px-2 py-0.5 rounded {{ $colour }} capitalize">
                                {{ $row->type }}
                            </span>
                        </td>
                        <td class="px-3 py-2">{{ Str::limit($row->message, 60) }}</td>
                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="4" class="px-3 py-6 text-center text-slate-400">No results.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div>
        {{ $rows->links() }}
    </div>
</div>

