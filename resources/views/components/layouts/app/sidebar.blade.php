<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="">
    <head>
        @include('partials.head')
    </head>

    <body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:sidebar sticky stashable class="bg-zinc-50 dark:bg-zinc-900 border-r rtl:border-r-0 rtl:border-l border-zinc-200 dark:border-zinc-700">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />
        <flux:brand href="#" logo="https://fluxui.dev/img/demo/logo.png" name="Acme Inc." class="px-2 dark:hidden" />
        <flux:brand href="#" logo="https://fluxui.dev/img/demo/dark-mode-logo.png" name="Acme Inc." class="px-2 hidden dark:flex" />

        <flux:navlist variant="outline">
            <flux:navlist.item icon="home" href="{{ route('dashboard') }}" :current="request()->routeIs('dashboard')">
                Home
            </flux:navlist.item>
            <flux:navlist.item icon="computer-desktop" href="{{ route('targets.index') }}" :current="request()->routeIs('targets.index')">
                Manage Monitors
            </flux:navlist.item>
            <flux:navlist.item icon="plus-circle" href="{{ route('targets.create') }}" :current="request()->routeIs('targets.create')">
                Add Monitor
            </flux:navlist.item>
            <flux:navlist.item icon="bell" href="{{ route('alerts') }}" :current="request()->routeIs('alerts')">
                Alerts
            </flux:navlist.item>
        </flux:navlist>
        <flux:spacer />
        <flux:navlist variant="outline">
            <flux:navlist.item icon="cog-6-tooth" href="#">Settings</flux:navlist.item>
            <flux:navlist.item icon="information-circle" href="#">Help</flux:navlist.item>
        </flux:navlist>
        <flux:dropdown position="top" align="start" class="max-lg:hidden">
            <flux:profile avatar="https://fluxui.dev/img/demo/user.png" name="{{ auth()->user()->initials() }}" />
            <flux:menu>
                <flux:menu.radio.group>
                    <flux:menu.radio>{{ auth()->user()->name }}</flux:menu.radio>
                    <flux:menu.radio>{{ auth()->user()->email }}</flux:menu.radio>
                </flux:menu.radio.group>
                <flux:menu.separator />
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
            </flux:menu>
        </flux:dropdown>
    </flux:sidebar>
    <flux:header class="block! bg-white lg:bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
        <flux:navbar class="lg:hidden w-full">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
            <flux:spacer />
            <flux:dropdown position="top" align="start">
                <flux:profile avatar="https://fluxui.dev/img/demo/user.png" />
                <flux:menu>
                    <flux:menu.radio.group>
                        <flux:menu.radio>{{ auth()->user()->name }}</flux:menu.radio>
                        <flux:menu.radio>{{ auth()->user()->email }}</flux:menu.radio>
                    </flux:menu.radio.group>
                    <flux:menu.separator />
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:navbar>
        <flux:navbar scrollable>
            <flux:navbar.item href="{{ route('dashboard') }}" :current="request()->routeIs('dashboard')">Dashboard</flux:navbar.item>
        </flux:navbar>
    </flux:header>
    {{ $slot }}
    @fluxScripts
    </body>
</html>
