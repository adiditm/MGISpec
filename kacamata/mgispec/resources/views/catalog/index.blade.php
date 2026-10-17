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
            <div class="hide-scrollbar -mx-4 flex gap-2 overflow-x-auto px-4 pb-2 sm:mx-0 sm:flex-wrap sm:px-0">
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

            @if ($products->isEmpty())
                <div class="mt-16 rounded-3xl bg-white p-14 text-center ring-1 ring-gray-200">
                    <i class="fa-solid fa-glasses text-4xl text-gray-300"></i>
                    <p class="mt-4 text-gray-500">{{ __('Belum ada produk di kategori ini.') }}</p>
                </div>
            @else
                <div class="mt-8 grid grid-cols-2 gap-3 sm:gap-5 md:grid-cols-3 lg:grid-cols-4 lg:gap-6 xl:grid-cols-5">
                    @foreach ($products as $product)
                        <div data-animate>@include('partials.product-card')</div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection
