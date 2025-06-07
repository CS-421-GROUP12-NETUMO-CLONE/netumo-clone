<?php

use Livewire\Volt\Component;
use App\Models\Target;

new class extends Component {
    public function with(): array
    {
        return [
            'expiredCertificates' => Target::query()
                ->whereHas('certificates', function ($query) {
                    $query->whereNotNull('days_to_ssl_expiry')
                        ->where('days_to_ssl_expiry', '<=', 0);
                })
                ->get(),

            'expiredDomains' => Target::query()
                ->whereHas('certificates', function ($query) {
                    $query->whereNotNull('days_to_domain_expiry')
                        ->where('days_to_domain_expiry', '<=', 0);
                })
                ->get()
        ];
    }
}; ?>

<div>
    <div class="space-y-4">
        <div>
            <h3 class="font-medium text-2xl text-gray-800">Certificates</h3>
            @if($expiredCertificates->isEmpty())
                <div class="text-green-700 text-sm">
                    All Certificates up to date!
                </div>
            @else
                <div class="text-rad">
                    @foreach($expiredCertificates as $target)
                        <div class="text-sm">
                            {{ $target->name }} ({{ $target->url }})
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
        <div>
            <h3 class="font-medium text-2xl text-gray-800">Domains</h3>
            @if($expiredDomains->isEmpty())
                <div class="text-green-700 text-sm">
                    All Domains up to date!
                </div>
            @else
                <div class="text-rad">
                    @foreach($expiredDomains as $target)
                        <div class="text-sm">
                            {{ $target->name }} ({{ $target->url }})
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
