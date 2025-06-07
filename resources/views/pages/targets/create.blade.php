<?php

use App\Models\Target;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component {
    public string $name = '';
    public string $url = '';

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:100', 'unique:targets'],
            'url' => ['required', 'url', 'max:255', 'unique:targets'],
        ];
    }

    public function save()
    {
        $this->validate();

        Target::create([
            'user_id' => Auth::id(),
            'name' => $this->name,
            'url' => $this->url,
        ]);

        $this->reset(['name', 'url']);
        session()->flash('success', 'Monitor added successfully!');
    }
}; ?>

<div>
    <div class="bg-white shadow rounded-xl p-6 max-w-5xl mx-auto space-y-6">

        <h2 class="text-lg font-semibold text-slate-800">Add New Monitor</h2>

        @if (session('success'))
            <div class="text-green-700 bg-green-100 border border-green-300 px-4 py-2 rounded">
                {{ session('success') }}
            </div>
        @endif

        <form wire:submit.prevent="save" class="space-y-4">

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Website Name</label>
                <input id="name" type="text" wire:model.defer="name"
                       placeholder="e.g. Netumo Main Site"
                       class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-sky-200 text-sm" />
                @error('name')
                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- URL -->
            <div>
                <label for="url" class="block text-sm font-medium text-slate-700 mb-1">Website URL</label>
                <input id="url" type="url" wire:model.defer="url"
                       placeholder="https://example.com"
                       class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-sky-200 text-sm" />
                @error('url')
                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit -->
            <div class="pt-2">
                <button type="submit"
                        class="inline-flex items-center px-5 py-2 bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium rounded-md transition">
                    Add Monitor
                </button>
            </div>
        </form>
    </div>
</div>
