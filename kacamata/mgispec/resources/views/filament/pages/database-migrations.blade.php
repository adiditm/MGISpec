<x-filament-panels::page>
    @php($pending = $this->getPending())

    <x-filament::section>
        <x-slot name="heading">Migrasi yang belum jalan ({{ count($pending) }})</x-slot>

        @forelse ($pending as $name)
            <div class="font-mono text-sm">{{ $name }}</div>
        @empty
            <p class="text-sm text-gray-500">Database sudah up to date.</p>
        @endforelse
    </x-filament::section>

    @if ($output !== '')
        <x-filament::section>
            <x-slot name="heading">Output</x-slot>
            <pre class="overflow-x-auto whitespace-pre-wrap text-xs">{{ $output }}</pre>
        </x-filament::section>
    @endif
</x-filament-panels::page>
