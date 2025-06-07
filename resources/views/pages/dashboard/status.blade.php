<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use App\Models\Target;
use App\Models\Status;

new class extends Component {
    public $targets;
    public $statuses;

    public function mount()
    {
        $this->targets = Auth::user()->targets;
        $this->statuses = Status::latest('checked_at')->get();
    }

    public function availabilityPercentage()
    {
        if ($this->targets->isEmpty()) {
            return 0;
        }

        $availableCount = $this->statuses->where('status_code', 200)->groupBy('target_id')->count();
        return round(($availableCount / $this->targets->count()) * 100);
    }

    public function averageResponseTime()
    {
        if ($this->statuses->isEmpty()) {
            return 0;
        }

        return round($this->statuses->avg('latency'), 1);
    }
}; ?>

<div>
    <div class="flex justify-center">
        <svg class="w-full h-24" viewBox="0 0 36 36">
            <path class="text-gray-500" fill="none" stroke-width="3.8"
                  d="M18 2.0845
                   a 15.9155 15.9155 0 0 1 0 31.831
                   a 15.9155 15.9155 0 0 1 0 -31.831"/>
            <path class="text-green-500" fill="none" stroke-width="3.8"
                  stroke-dasharray="{{ $this->availabilityPercentage() }}, 100"
                  d="M18 2.0845
                   a 15.9155 15.9155 0 0 1 0 31.831
                   a 15.9155 15.9155 0 0 1 0 -31.831"/>
            <text x="18" y="20.35"
                  class="fill-current {{ $this->availabilityPercentage() == 100 ? 'text-green-600' : 'text-rad' }} text-xl font-bold"
                  text-anchor="middle">{{ $this->availabilityPercentage() }}%
            </text>
        </svg>
    </div>
    <div class="text-sm text-gray-600">
        <p><strong>Status:</strong> <span
                class="{{ $this->availabilityPercentage() == 100 ? 'text-green-600' : 'text-rad' }}">{{ $this->availabilityPercentage() === 100 ? 'Everything is OK' : 'Some services are down' }}</span>
        </p>
        <p><strong>Average Response Time:</strong> <span class="text-black font-medium">{{ $this->averageResponseTime() }}ms</span>
        </p>
        <p><strong>Average Incorrect:</strong> <span class="text-black font-medium">{{ 100 - $this->availabilityPercentage() }}%</span>
        </p>
    </div>
</div>
