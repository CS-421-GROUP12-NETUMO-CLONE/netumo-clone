<x-layouts.app :title="__('Dashboard')">
    <div class="bg-gray-50 min-h-screen font-sans text-gray-800">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold mb-2">Neturno Clone Overview</h1>
        </div>

        <!-- Main Grid -->
        <div class="space-y-6">
            <!-- Top Cards -->
            <livewire:dashboard.cards />

            <!-- Second Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Availability Widget -->
                <div class="bg-white p-6 rounded shadow space-y-4 text-center">
                    <div class="text-green-600 font-semibold text-lg">Availability</div>
                    <livewire:dashboard.status />
                </div>

                <!-- Recent Checks -->
                <div class="bg-white p-6 rounded shadow">
                    <div class="text-lg font-semibold text-gray-700 mb-2">Recent Checks</div>
                    <livewire:dashboard.recent-check />
                </div>
            </div>

            <!-- Availability Graph -->
            <div class="bg-white p-6 rounded shadow space-y-4">
                <div class="text-gray-700 font-semibold text-lg">Availability</div>

                <livewire:dashboard.availability />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 shadow-lg">
                <!-- Availability Widget -->

                <div class="bg-white p-6 rounded shadow space-y-4">
                    <div class="text-gray-600 font-medium text-2xl">Upcoming Renewals</div>
                    <livewire:dashboard.upcoming-renewal />
                </div>

                <!-- Recent Checks -->
                <div class="bg-white p-6 rounded shadow">
                    <div class="text-2xl font-medium text-gray-600 mb-2">Failed</div>
                    <livewire:dashboard.failed />
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
