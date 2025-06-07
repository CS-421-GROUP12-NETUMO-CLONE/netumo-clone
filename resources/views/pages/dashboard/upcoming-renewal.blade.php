<?php

use Livewire\Volt\Component;
use App\Models\Certificate;

new class extends Component {
    public function with(): array
    {
        return [
            'expiringSslCertificates' => Certificate::whereNotNull('ssl_expiry_date')
                ->where('days_to_ssl_expiry', '<=', 14)
                ->where('days_to_ssl_expiry', '>', 0)
                ->orderBy('days_to_ssl_expiry')
                ->get(),

            'expiringDomains' => Certificate::whereNotNull('domain_expiry_date')
                ->where('days_to_domain_expiry', '<=', 14)
                ->where('days_to_domain_expiry', '>', 0)
                ->orderBy('days_to_domain_expiry')
                ->get()
        ];
    }
}; ?>

<div>
    <div class="space-y-4">
        <div>
            <h3 class="font-medium text-2xl text-gray-800">Certificates</h3>
            @if($expiringSslCertificates->count() > 0)
                <div class="space-y-2">
                    @foreach($expiringSslCertificates as $certificate)
                        <div class="text-yellow-700 text-sm">
                            {{ $certificate->target->name }} - Expires in {{ $certificate->days_to_ssl_expiry }} days
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-green-700 text-sm">
                    All Certificates up to date!
                </div>
            @endif
        </div>
        <div>
            <h3 class="font-medium text-2xl text-gray-800">Domains</h3>
            @if($expiringDomains->count() > 0)
                <div class="space-y-2">
                    @foreach($expiringDomains as $domain)
                        <div class="text-rad text-sm">
                            {{ $domain->target->name }} - Expires in {{ $domain->days_to_domain_expiry }} days
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-green-700 text-sm">
                    All Domains up to date!
                </div>
            @endif
        </div>
    </div>
</div>
