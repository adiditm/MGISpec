@php
    use App\Models\Setting;
    use App\Support\I18n;

    $name = $product->t('name');
    $images = $product->imageUrls();
    $waText = __('Halo MGISpec, saya tertarik dengan produk :name (SKU: :sku). Mohon info lebih lanjut.', ['name' => $name, 'sku' => $product->sku]);
    $waUrl = 'https://wa.me/'.Setting::get('whatsapp').'?text='.rawurlencode($waText.' '.url()->current());
    $highlights = collect($product->highlights ?? [])->map(fn ($h) => I18n::pick($h['text'] ?? null))->filter();
    $specs = collect($product->specs ?? [])->filter(fn ($s) => filled($s['value'] ?? null));
    $techs = collect($product->technologies ?? []);
    $description = $product->t('description');
@endphp

@extends('layouts.app', ['title' => $name, 'description' => $product->t('short_description')])

@section('content')
    <section class="bg-white pt-6 pb-14 lg:pt-10 lg:pb-20">
        <div class="mx-auto max-w-[1440px] px-4 sm:px-6 lg:px-10">
            <nav class="flex flex-wrap items-center gap-x-2 text-xs text-gray-400" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="hover:text-gray-900">{{ __('Home') }}</a><span>/</span>
                <a href="{{ route('catalog.index') }}" class="hover:text-gray-900">{{ __('Katalog') }}</a>
                @if ($product->category)
                    <span>/</span><a href="{{ route('catalog.index', ['kategori' => $product->category->slug]) }}" class="hover:text-gray-900">{{ $product->category->t('name') }}</a>
                @endif
                <span>/</span><span class="text-gray-700">{{ $name }}</span>
            </nav>

            <div class="mt-6 grid grid-cols-1 gap-8 lg:mt-8 lg:grid-cols-2 lg:gap-14">
                {{-- Gallery --}}
                <div data-gallery class="min-w-0 lg:sticky lg:top-28 lg:self-start">
                    <div class="swiper gallery-main overflow-hidden rounded-3xl bg-gray-100">
                        <div class="swiper-wrapper">
                            @foreach ($images as $i => $src)
                                <div class="swiper-slide">
                                    <div class="zoomable aspect-square overflow-hidden">
                                        <img src="{{ $src }}" alt="{{ $name }} {{ $i + 1 }}" class="h-full w-full object-cover" @if ($i) loading="lazy" @endif>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if (count($images) > 1)
                            <button type="button" data-prev class="carousel-nav absolute top-1/2 left-3 z-10 -translate-y-1/2 bg-white/90" aria-label="{{ __('Sebelumnya') }}"><i class="fa-solid fa-arrow-left text-sm"></i></button>
                            <button type="button" data-next class="carousel-nav absolute top-1/2 right-3 z-10 -translate-y-1/2 bg-white/90" aria-label="{{ __('Berikutnya') }}"><i class="fa-solid fa-arrow-right text-sm"></i></button>
                        @endif
                    </div>
                    @if (count($images) > 1)
                        <div class="swiper gallery-thumbs mt-3">
                            <div class="swiper-wrapper">
                                @foreach ($images as $i => $src)
                                    <div class="swiper-slide cursor-pointer overflow-hidden rounded-xl bg-gray-100">
                                        <img src="{{ $src }}" alt="" class="aspect-square h-full w-full object-cover" loading="lazy">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Info --}}
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        @if ($product->category)
                            <span class="rounded-full bg-gray-100 px-3 py-1 text-[11px] font-semibold tracking-[.14em] text-gray-500 uppercase">{{ $product->category->t('name') }}</span>
                        @endif
                        @if ($product->badge)
                            <span class="brand-gradient rounded-full px-3 py-1 text-[11px] font-bold tracking-wide text-white uppercase">{{ $product->badge }}</span>
                        @endif
                    </div>
                    <h1 class="mt-4 text-3xl leading-tight font-extrabold tracking-tight text-gray-900 lg:text-5xl">{{ $name }}</h1>
                    <p class="mt-2 text-sm text-gray-400">SKU: <span class="font-medium text-gray-600">{{ $product->sku }}</span></p>

                    @if ($short = $product->t('short_description'))
                        <p class="mt-6 text-base leading-relaxed text-gray-600 lg:text-lg">{{ $short }}</p>
                    @endif

                    @if ($highlights->isNotEmpty())
                        <ul class="mt-7 space-y-3">
                            @foreach ($highlights as $h)
                                <li class="flex gap-3 text-[15px] text-gray-700">
                                    <span class="mt-0.5 grid h-5 w-5 shrink-0 place-items-center rounded-full bg-brand-50 text-brand-600"><i class="fa-solid fa-check text-[10px]"></i></span>{{ $h }}
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    <div class="mt-9 flex flex-col gap-3 sm:flex-row">
                        <a href="{{ $waUrl }}" target="_blank" rel="noopener"
                           class="inline-flex flex-1 items-center justify-center gap-3 rounded-full bg-[#25D366] px-7 py-4 text-base font-semibold text-white shadow-lg shadow-green-500/25 transition hover:-translate-y-0.5 hover:bg-[#1ebe5a]">
                            <i class="fa-brands fa-whatsapp text-2xl"></i> {{ __('Tanya via WhatsApp') }}
                        </a>
                        <a href="{{ route('catalog.index') }}" class="inline-flex items-center justify-center gap-2 rounded-full px-6 py-4 text-sm font-semibold text-gray-700 ring-1 ring-gray-300 transition hover:ring-gray-900">
                            <i class="fa-solid fa-grip text-xs"></i> {{ __('Lihat Katalog') }}
                        </a>
                    </div>

                    <div class="mt-8 grid grid-cols-2 gap-3 text-xs text-gray-600">
                        <div class="flex items-center gap-3 rounded-2xl bg-gray-50 p-4"><i class="fa-solid fa-shield-halved text-xl text-brand-600"></i><span><strong class="block text-gray-900">FDA Registered</strong>U.S. FDA</span></div>
                        <div class="flex items-center gap-3 rounded-2xl bg-gray-50 p-4"><i class="fa-solid fa-certificate text-xl text-sun-500"></i><span><strong class="block text-gray-900">MDA Malaysia</strong>MDA-3521-WDP122</span></div>
                    </div>

                    {{-- Tabs --}}
                    <div data-tabs class="mt-12">
                        <div class="hide-scrollbar flex gap-6 overflow-x-auto border-b border-gray-200 text-sm font-semibold" role="tablist">
                            @if ($description)<button type="button" data-tab="desc" class="tab-btn">{{ __('Deskripsi') }}</button>@endif
                            @if ($specs->isNotEmpty())<button type="button" data-tab="spec" class="tab-btn">{{ __('Spesifikasi') }}</button>@endif
                            @if ($techs->isNotEmpty())<button type="button" data-tab="tech" class="tab-btn">{{ __('Teknologi') }}</button>@endif
                        </div>

                        @if ($description)
                            <div data-panel="desc" class="prose-mgi pt-6">{!! $description !!}</div>
                        @endif
                        @if ($specs->isNotEmpty())
                            <div data-panel="spec" class="pt-6">
                                <dl class="divide-y divide-gray-100 overflow-hidden rounded-2xl ring-1 ring-gray-200">
                                    @foreach ($specs as $s)
                                        <div class="grid grid-cols-2 gap-4 px-5 py-3.5 text-sm odd:bg-gray-50">
                                            <dt class="text-gray-500">{{ I18n::pick($s['label'] ?? null) }}</dt>
                                            <dd class="font-medium text-gray-900">{{ $s['value'] }}</dd>
                                        </div>
                                    @endforeach
                                </dl>
                            </div>
                        @endif
                        @if ($techs->isNotEmpty())
                            <div data-panel="tech" class="grid gap-4 pt-6 sm:grid-cols-2">
                                @foreach ($techs as $t)
                                    <div class="rounded-2xl p-5 ring-1 ring-gray-200">
                                        <span class="brand-gradient grid h-11 w-11 place-items-center rounded-xl text-white"><i class="{{ $t['icon'] ?? 'fa-solid fa-eye' }}"></i></span>
                                        <h3 class="mt-4 font-semibold text-gray-900">{{ I18n::pick($t['title'] ?? null) }}</h3>
                                        <p class="mt-1.5 text-sm leading-relaxed text-gray-500">{{ I18n::pick($t['description'] ?? null) }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if ($related->isNotEmpty())
        <section class="bg-gray-50 py-14 lg:py-20">
            <div class="mx-auto max-w-[1440px] px-4 sm:px-6 lg:px-10" data-carousel data-free="true"
                 data-breakpoints='{"480":{"slidesPerView":2.3},"768":{"slidesPerView":3.3,"spaceBetween":20},"1024":{"slidesPerView":4.3,"spaceBetween":24},"1280":{"slidesPerView":5,"spaceBetween":24}}'>
                <div class="mb-8 flex items-end justify-between gap-6">
                    <h2 class="text-2xl font-extrabold tracking-tight text-gray-900 lg:text-3xl">{{ __('Produk Lainnya') }}</h2>
                    <div class="flex gap-2">
                        <button type="button" data-prev class="carousel-nav" aria-label="{{ __('Sebelumnya') }}"><i class="fa-solid fa-arrow-left text-sm"></i></button>
                        <button type="button" data-next class="carousel-nav" aria-label="{{ __('Berikutnya') }}"><i class="fa-solid fa-arrow-right text-sm"></i></button>
                    </div>
                </div>
                <div class="swiper">
                    <div class="swiper-wrapper">
                        @foreach ($related as $product)
                            <div class="swiper-slide !h-auto">@include('partials.product-card')</div>
                        @endforeach
                    </div>
                </div>
                <div data-progress class="marquee-progress relative mt-8"></div>
            </div>
        </section>
    @endif
@endsection
