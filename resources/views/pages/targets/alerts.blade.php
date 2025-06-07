<?php

use App\Models\Alert;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    /* ── UI State ────────── */
    public string $search = '';
    public string $filterType = '';
    public array $selected = [];   // IDs of checked alerts

    /* paginate 10 per page */
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterType()
    {
        $this->resetPage();
    }

    /* ── Single-row actions ──────── */
    public function markAsRead(int $id): void
    {
        Alert::where('id', $id)->whereHas('target', fn($q) => $q->where('user_id', auth()->id()))->update(['read_at' => now()]);
        $this->dispatch('toast', type: 'success', message: 'Alert marked as read.');
    }

    public function deleteAlert(int $id): void
    {
        Alert::where('id', $id)->delete();
        $this->dispatch('toast', type: 'success', message: 'Alert deleted.');
    }

    public function exportCsv()
    {
        $alerts = Alert::with('target')
            ->whereHas('target', fn($q) => $q->where('user_id', auth()->id()))
            ->when($this->filterType, fn($q) => $q->where('type', $this->filterType))
            ->when($this->search, fn($q) =>
                $q->whereHas('target', fn($query) =>
                $query->where('name', 'like', '%' . $this->search . '%')
                )
            )
            ->latest()->get();

        $csv = "ID,Type,Target,Message,Read,Created At\n";
        foreach ($alerts as $alert) {
            $csv .= implode(',', [
                    $alert->id,
                    $alert->type,
                    $alert->target->name,
                    '"' . str_replace('"', '""', $alert->message) . '"',
                    $alert->read_at ? 'Yes' : 'No',
                    $alert->created_at->toDateTimeString(),
                ]) . "\n";
        }

        $filename = 'alerts-' . now()->format('Ymd_His') . '.csv';
        Storage::disk('local')->put($filename, $csv);

        return Response::download(storage_path("app/private/$filename"))->deleteFileAfterSend();
    }

    /* ── Bulk actions ───────────────────────── */
    public function bulkMarkRead(): void
    {
        if (!$this->selected) {
            $this->dispatch('toast', type: 'error', message: 'Nothing selected.');
            return;
        }

        Alert::whereIn('id', $this->selected)->whereNull('read_at')
            ->whereHas('target', fn($q) => $q->where('user_id', auth()->id()))
            ->update(['read_at' => now()]);
        $this->reset('selected');
        $this->dispatch('toast', type: 'success', message: 'Selected alerts marked as read.');
    }

    public function bulkDelete(): void
    {
        if (!$this->selected) {
            $this->dispatch('toast', type: 'error', message: 'Nothing selected.');
            return;
        }

        Alert::whereIn('id', $this->selected)
            ->whereHas('target', fn($q) => $q->where('user_id', auth()->id()))
            ->delete();
        $this->reset('selected');
        $this->dispatch('toast', type: 'success', message: 'Selected alerts deleted.');
    }

    /* ── Data fetch ─────────────────────────── */
    public function getAlertsProperty()
    {
        return Alert::with('target')
            ->whereHas('target', fn($q) => $q->where('user_id', auth()->id()))
            ->when($this->filterType, fn($q) => $q->where('type', $this->filterType))
            ->when($this->search, fn($q, $t) =>
            $q->whereHas('target', fn($sub) => $sub->where('name', 'like', "%$t%"))
            )
            ->latest()
            ->paginate(10);
    }

    public function getGroupedAlertsProperty()
    {
        return $this->alerts->groupBy(fn($a) => $a->created_at->toDateString());
    }

    /* expose to Blade */
    public function with()
    {
        return [
            'alerts' => $this->alerts,
            'groupedAlerts' => $this->groupedAlerts,
        ];
    }
};
?>

<div class="bg-white shadow rounded-xl p-6 space-y-6" wire:poll.5s
     x-data="{
        show:false,type:'',msg:'',
        init(){window.addEventListener('toast',e=>{this.type=e.detail.type;this.msg=e.detail.message;this.show=true;setTimeout(()=>this.show=false,3000);});}
     }">

    <!-- Toast -->
    <div x-cloak x-show="show"
         x-transition
         :class="type==='success' ? 'bg-green-600' : 'bg-red-600'"
         class="fixed top-4 right-4 text-white px-4 py-2 rounded shadow-lg z-50">
        <span x-text="msg"></span>
    </div>

    <!-- Header -------------------------------------------------------------------->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h2 class="text-xl font-semibold text-gray-800">System Alerts</h2>

        <div class="flex flex-wrap gap-2">
            <input type="text" wire:model.live.debounce.500ms="search"
                   placeholder="Search target..."
                   class="border rounded px-3 py-1 text-sm w-48"/>

            <select wire:model.live="filterType" class="border rounded px-2 py-1 text-sm">
                <option value="">All</option>
                <option value="downtime">Downtime</option>
                <option value="ssl">SSL</option>
                <option value="domain">Domain</option>
            </select>

            <!-- Bulk buttons -->
            <button wire:click="bulkMarkRead"
                    class="px-3 py-1 text-xs rounded bg-green-600 text-white hover:bg-green-700">
                Mark Selected Read
            </button>
            <button wire:click="bulkDelete"
                    class="px-3 py-1 text-xs rounded bg-red-600 text-white hover:bg-red-700">
                Delete Selected
            </button>

            <!-- Export -->
            <button wire:click="exportCsv"
                    class="px-3 py-1 text-xs rounded bg-blue-600 text-white hover:bg-blue-700">
                Export CSV
            </button>
        </div>
    </div>

    <!-- Alerts grouped by day ------------------------------------------------------->
    <div class="divide-y space-y-4">
        @forelse ($groupedAlerts as $date => $alertsForDate)
            <div>
                <h3 class="text-sm font-bold text-gray-700 mb-1">{{ \Carbon\Carbon::parse($date)->format('F j, Y') }}</h3>

                @foreach ($alertsForDate as $alert)
                    @php
                        $badgeColour = [
                            'downtime' => 'bg-red-100 text-red-700',
                            'ssl'      => 'bg-yellow-100 text-yellow-700',
                            'domain'   => 'bg-blue-100 text-blue-700',
                        ][$alert->type] ?? 'bg-gray-100 text-gray-700';
                    @endphp

                    <div class="flex items-start justify-between px-2 py-2 bg-gray-50 rounded hover:bg-gray-100">
                        <div class="flex gap-2">
                            <input type="checkbox" wire:model.live="selected" value="{{ $alert->id }}"/>

                            <div>
                                <div class="flex items-center gap-2 text-sm">
                                    <span class="px-2 py-0.5 text-xs rounded-full {{ $badgeColour }}">
                                        {{ ucfirst($alert->type) }}
                                    </span>
                                    <span class="font-medium">{{ $alert->target->name }}</span>
                                    <span class="text-xs {{ $alert->read_at ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $alert->read_at ? 'Read' : 'Unread' }}
                                    </span>
                                </div>
                                <p class="text-gray-600 text-sm">{{ $alert->message }}</p>
                            </div>
                        </div>

                        <div class="flex gap-1 items-center">
                            @unless($alert->read_at)
                                <button wire:click="markAsRead({{ $alert->id }})"
                                        class="text-xs px-2 py-1 rounded border border-green-600 text-green-600 hover:bg-green-100">
                                    Mark Read
                                </button>
                            @endunless
                            <button wire:click="deleteAlert({{ $alert->id }})"
                                    class="text-xs px-2 py-1 rounded border border-red-600 text-red-600 hover:bg-red-100">
                                Delete
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @empty
            <div class="text-center text-gray-500 py-6">No alerts found.</div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div>
        {{ $alerts->links() }}
    </div>
</div>


