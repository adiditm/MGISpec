@extends('layouts.app', ['title' => $current ? $current->t('name') : __('Katalog')])

@section('content')
    <section class="relative overflow-hidden bg-ink text-white">
        <div class="pointer-events-none absolute -top-32 -right-20 h-96 w-96 rounded-full bg-brand-600/40 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-40 left-10 h-80 w-80 rounded-full bg-sun-500/25 blur-3xl"></div>
        <div class="relative mx-auto max-w-[1440px] px-4 py-14 sm:px-6 lg:px-10 lg:py-20">
            <nav class="text-xs text-white/50" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="hover:text-white">{{ __('Home') }}</a>
                <span class="mx-2">/</span><span class="text-white/80">{{ __('Katalog') }}</span>
            </nav>
            <h1 class="mt-4 text-4xl font-extrabold tracking-tight lg:text-6xl">
                {{ $current ? $current->t('name') : __('Katalog Produk') }}
            </h1>
            <p class="mt-4 max-w-2xl text-white/70 lg:text-lg">{{ __('Koleksi frame & lensa MGISpec — teknologi kesehatan mata bersertifikat FDA.') }}</p>
        </div>
    </section>

    <section class="bg-gray-50 py-10 lg:py-14">
        <div class="mx-auto max-w-[1440px] px-4 sm:px-6 lg:px-10">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between lg:gap-8">
                <div class="hide-scrollbar -mx-4 order-2 flex gap-2 overflow-x-auto px-4 pb-2 sm:mx-0 sm:flex-wrap sm:px-0 lg:order-1">
                    <a href="{{ route('catalog.index') }}"
                       class="shrink-0 rounded-full px-5 py-2.5 text-sm font-semibold transition {{ $current ? 'bg-white text-gray-700 ring-1 ring-gray-200 hover:ring-gray-400' : 'brand-gradient text-white shadow-lg shadow-brand-500/25' }}">
                        {{ __('Semua') }} <span class="ml-1 opacity-60">{{ $categories->sum('products_count') }}</span>
                    </a>
                    @foreach ($categories as $category)
                        @php $active = $current && $current->is($category); @endphp
                        <a href="{{ route('catalog.index', ['kategori' => $category->slug]) }}"
                           class="shrink-0 rounded-full px-5 py-2.5 text-sm font-semibold transition {{ $active ? 'brand-gradient text-white shadow-lg shadow-brand-500/25' : 'bg-white text-gray-700 ring-1 ring-gray-200 hover:ring-gray-400' }}">
                            {{ $category->t('name') }} <span class="ml-1 opacity-60">{{ $category->products_count }}</span>
                        </a>
                    @endforeach
                </div>

                {{-- Instant search by model name or SKU, filters the products already on the page --}}
                <form role="search" data-catalog-search onsubmit="return false" class="order-1 w-full shrink-0 lg:order-2 lg:w-80">
                    <label class="relative block">
                        <span class="sr-only">{{ __('Cari model atau SKU') }}</span>
                        <i class="fa-solid fa-magnifying-glass pointer-events-none absolute top-1/2 left-4 -translate-y-1/2 text-sm text-gray-400"></i>
                        <input type="search" data-catalog-search-input autocomplete="off" placeholder="{{ __('Cari model atau SKU') }}"
                               class="w-full rounded-full border-0 bg-white py-3 pr-5 pl-11 text-sm text-gray-900 ring-1 ring-gray-200 transition placeholder:text-gray-400 focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    </label>
                </form>
            </div>

            @if ($products->isEmpty())
                <div class="mt-16 rounded-3xl bg-white p-14 text-center ring-1 ring-gray-200">
                    <i class="fa-solid fa-glasses text-4xl text-gray-300"></i>
                    <p class="mt-4 text-gray-500">{{ __('Belum ada produk di kategori ini.') }}</p>
                </div>
            @else
                <div class="mt-8 grid grid-cols-2 gap-3 sm:gap-5 md:grid-cols-3 lg:grid-cols-4 lg:gap-6 xl:grid-cols-5" data-catalog-grid>
                    @foreach ($products as $product)
                        <div data-animate data-product data-search="{{ \Illuminate\Support\Str::lower(collect([$product->sku, ...array_values((array) $product->name)])->filter()->implode(' ')) }}">@include('partials.product-card')</div>
                    @endforeach
                </div>
                <div data-catalog-empty hidden class="mt-16 rounded-3xl bg-white p-14 text-center ring-1 ring-gray-200">
                    <i class="fa-solid fa-magnifying-glass text-4xl text-gray-300"></i>
                    <p class="mt-4 text-gray-500">{{ __('Tidak ada produk yang cocok dengan pencarian Anda.') }}</p>
                </div>
            @endif
        </div>
    </section>
@endsection
