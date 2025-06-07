<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component {
    public $targets, $certificates;

    public function mount()
    {
        $this->targets = Auth::user()->targets()->count();
        $this->certificates = Auth::user()->targets->sum(function ($target) {
            return $target->certificates()->count();
        });
    }
}; ?>

<div>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 shadow rounded border-l-4 border-blue-500">
            <div class="text-gray-600 text-sm">🌐 Websites</div>
            <div class="text-xl font-bold">{{ $targets }}</div>
        </div>
        <div class="bg-white p-4 shadow rounded border-l-4 border-green-500">
            <div class="text-gray-600 text-sm">🛡️ Certificates</div>
            <div class="text-xl font-bold">{{ $certificates }}</div>
        </div>
        <div class="bg-white p-4 shadow rounded border-l-4 border-purple-500">
            <div class="text-gray-600 text-sm">🏢 Domains</div>
            <div class="text-xl font-bold">{{ $targets }}</div>
        </div>
        <div class="bg-white p-4 shadow rounded border-l-4 border-orange-400">
            <div class="text-gray-600 text-sm">🔗 RESTful</div>
            <div class="text-xl font-bold">0</div>
        </div>
    </div>
</div>

