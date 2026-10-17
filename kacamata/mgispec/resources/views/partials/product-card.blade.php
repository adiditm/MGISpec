@php
    $badgeColors = [
        'pink' => 'bg-brand-600', 'orange' => 'bg-sun-500', 'blue' => 'bg-sky-600',
        'green' => 'bg-emerald-600', 'purple' => 'bg-violet-600', 'dark' => 'bg-ink',
    ];
    $url = route('catalog.show', $product);
@endphp
<article class="product-card group relative flex h-full flex-col overflow-hidden rounded-2xl bg-white ring-1 ring-gray-200/80 transition duration-500 hover:shadow-[0_24px_50px_-24px_rgba(17,12,34,.35)] hover:ring-gray-300">
    <a href="{{ $url }}" class="img-wrap relative block aspect-square overflow-hidden bg-gray-100">
        <img src="{{ \App\Support\Media::url($product->thumbnail) }}" alt="{{ $product->t('name') }}" loading="lazy" class="h-full w-full object-cover">
        @if ($product->badge)
            <span class="absolute top-3 left-3 rounded-full px-2.5 py-1 text-[10px] font-bold tracking-wide text-white uppercase shadow {{ $badgeColors[$product->badge_color] ?? $badgeColors['pink'] }}">
                {{ $product->badge }}
            </span>
        @endif
    </a>
    <div class="flex flex-1 flex-col p-4">
        @if ($product->category)
            <span class="text-[11px] font-medium tracking-[.14em] text-gray-400 uppercase">{{ $product->category->t('name') }}</span>
        @endif
        <h3 class="mt-1 line-clamp-2 text-sm leading-snug font-semibold text-gray-900 sm:text-[15px]">
            <a href="{{ $url }}" class="after:absolute after:inset-0">{{ $product->t('name') }}</a>
        </h3>
        <span class="more mt-auto flex items-center gap-1.5 pt-3 text-xs font-semibold text-gray-500">
            {{ __('Lihat Detail') }} <i class="fa-solid fa-arrow-right text-[10px]"></i>
        </span>
    </div>
</article>
