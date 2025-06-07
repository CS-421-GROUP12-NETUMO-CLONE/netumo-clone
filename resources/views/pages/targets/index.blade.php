<?php

use App\Models\Target;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public string $search = '';
    public int $perPage = 10;

    /* Automatically reset to page 1 when the search term changes */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /* ------------------------------------------------------------------ */
    /*  Query: targets + latest status                                    */
    /* ------------------------------------------------------------------ */
    public function getTargetsProperty()
    {
        return Target::query()
            ->with(['statuses' => fn($q) => $q->latest('checked_at')->limit(1)])
            ->when($this->search, fn($q, $term) => $q->where('name', 'like', "%{$term}%")
                ->orWhere('url', 'like', "%{$term}%"))
            ->orderByDesc('updated_at')
            ->paginate($this->perPage);
    }

    public function with()
    {
        return ['targets' => $this->targets];
    }
}; ?>

<div class="bg-white shadow rounded-xl p-6 space-y-4">
    <!-- Card header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-lg font-semibold text-slate-800">Manage Monitored Websites / Hosts</h2>
            <p class="text-sm text-slate-500">Add your websites to this section so that Netumo-clone monitors them for
                expiry.</p>
        </div>

        <!-- Button & Search -->
        <div class="flex flex-col sm:flex-row sm:items-center gap-2 w-full sm:w-auto">
            <a href="{{ route('targets.create') }}"
               class="flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-sky-600 hover:bg-sky-700">
                Add Monitor
            </a>

            <input type="text" wire:model.live.debounce.150ms="search"
                   placeholder="Search name or URL…"
                   class="flex-1 sm:w-60 px-3 py-2 border rounded-md text-sm focus:ring focus:ring-sky-200"/>
        </div>
    </div>

    <!-- Responsive table -->
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-slate-700">
            <thead>
            <tr class="border-b bg-slate-50 whitespace-nowrap">
                <th class="px-4 py-2 text-left font-medium">#</th>
                <th class="px-4 py-2 text-left font-medium">Name</th>
                <th class="px-4 py-2 text-left font-medium">URL</th>
                <th class="px-4 py-2 text-left font-medium">Last Check</th>
                <th class="px-4 py-2 text-left font-medium">Last Status</th>
                <th class="px-4 py-2 text-left font-medium">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($targets as $target)
                @php
                    $latest = $target->statuses->first();
                    $isUp   = $latest && $latest->status_code >= 200 && $latest->status_code < 400;
                @endphp
                <tr class="border-b last:border-0 whitespace-nowrap">
                    <td class="px-4 py-2">{{ $loop->iteration ?? '—' }}</td>
                    <td class="px-4 py-2 font-medium">{{ $target->name }}</td>
                    <td class="px-4 py-2">
                        <a href="{{ $target->url }}" class="text-sky-600 hover:underline" target="_blank">
                            {{ Str::limit($target->url, 30) }}
                        </a>
                    </td>
                    <td class="px-4 py-2">
                        {{ $latest?->checked_at?->diffForHumans() ?? 'Never' }}
                    </td>
                    <td class="px-4 py-2">
                        @if (!$latest)
                            <span class="px-2 py-0.5 bg-slate-300 text-white rounded">—</span>
                        @elseif ($isUp)
                            <span class="px-2 py-0.5 bg-green-500 text-white rounded">🟢 Up</span>
                        @else
                            <span class="px-2 py-0.5 bg-red-500 text-white rounded">🔴 Down</span>
                        @endif
                    </td>
                    <td class="flex items-center px-4 py-2 space-x-1">
                        <a href="{{ route('targets.status', encrypt($target->id)) }}" class="p-1 hover:bg-slate-100 rounded"
                           title="Latest Status">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </a>
                        <a href="{{ route('targets.history', encrypt($target->id)) }}" class="p-1 hover:bg-slate-100 rounded"
                           title="History">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </a>
                        <a href="" class="p-1 hover:bg-slate-100 rounded" title="Edit">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M16.862 3.487a3.375 3.375 0 014.776 4.776L7.5 22.5 2 23l.5-5.5 14.362-14.362z"/>
                            </svg>
                        </a>
                        <a href="" class="p-1 hover:bg-slate-100 rounded" title="Delete">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6"/>
                            </svg>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-slate-400">
                        No monitors found. Click “Add New Monitor” to get started.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="pt-2">
        {{ $targets->links(data: ['scrollTo' => false]) }}
    </div>
</div>

