<?php

use App\Models\Target;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    public string $filter = 'all'; // all, up, down
    public $perPage = 5;

    use WithPagination;

    public function updatingFilter()
    {
        $this->resetPage();
    }

    public function getFilteredTargetsProperty()
    {
        $query = Target::with(['statuses' => fn($q) => $q->latest('checked_at')->limit(5)])
            ->where('user_id', auth()->id());

        if ($this->filter === 'up') {
            $query->whereHas('statuses', fn($q) => $q->latest('checked_at')
                ->whereBetween('status_code', [200, 399])
            );
        }

        if ($this->filter === 'down') {
            $query->whereHas('statuses', fn($q) => $q->latest('checked_at')
                ->where('status_code', '>=', 400)
            );
        }

        return $query->paginate($this->perPage);
    }
}; ?>

<div class="space-y-4">
    <!-- Filter Buttons -->
    <div class="flex gap-2 justify-end text-sm">
        <button wire:click="$set('filter', 'all')"
                class="px-3 py-1 rounded border cursor-pointer"
                :class="{ 'bg-blue-100 text-blue-800': filter === 'all' }">All</button>
        <button wire:click="$set('filter', 'up')"
                class="px-3 py-1 rounded border cursor-pointer"
                :class="{ 'bg-green-100 text-green-800': filter === 'up' }">Up 🟢</button>
        <button wire:click="$set('filter', 'down')"
                class="px-3 py-1 rounded border cursor-pointer"
                :class="{ 'bg-red-100 text-red-800': filter === 'down' }">Down 🔴</button>
    </div>

    <!-- Table -->
    <table class="w-full text-sm text-left text-gray-600">
        <thead>
        <tr class="border-b">
            <th class="py-2">Monitor</th>
            <th class="py-2">Status</th>
            <th class="py-2">Latency</th>
            <th class="py-2">Checked At</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($this->filteredTargets as $target)
            @php
                $status = $target->statuses->first();
                $isUp = $status && $status->status_code >= 200 && $status->status_code < 400;
            @endphp
            <tr class="border-t">
                <td class="py-2">{{ $target->name }}</td>
                <td class="py-2">
                    @if ($status)
                        @if ($isUp)
                            🟢 <span class="ml-1 px-2 py-1 bg-green-100 text-green-700 text-xs rounded">Up</span>
                        @else
                            🔴 <span class="ml-1 px-2 py-1 bg-red-100 text-red-700 text-xs rounded">Down</span>
                        @endif
                    @else
                        <span class="text-gray-400">No check</span>
                    @endif
                </td>
                <td class="py-2">{{ $status ? round($status->latency * 1000, 1) . ' ms' : '-' }}</td>
                <td class="py-2">{{ $status ? $status->checked_at->diffForHumans(null, true, true) : '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center py-4 text-gray-400">No results found.</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $this->filteredTargets->links() }}
    </div>
</div>


