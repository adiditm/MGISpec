@extends('layouts.app', ['overHero' => $sliders->isNotEmpty()])

@section('content')
    @include('partials.hero')

    {{-- Antara slider besar & footer: kartu manfaat / promo sebagai "slider kecil" --}}
    @if ($promos->isNotEmpty())
        <section id="technologies" class="relative overflow-hidden bg-white py-16 lg:py-24 scroll-mt-20">
            <div class="mx-auto max-w-[1440px] px-4 sm:px-6 lg:px-10" data-carousel data-autoplay="3800" data-loop="{{ $promos->count() > 4 ? 'true' : 'false' }}"
                 data-breakpoints='{"640":{"slidesPerView":2.2,"spaceBetween":20},"1024":{"slidesPerView":3.2,"spaceBetween":24},"1280":{"slidesPerView":4,"spaceBetween":28}}'>
                <div class="mb-10 flex flex-wrap items-end justify-between gap-6" data-animate>
                    <div>
                        <span class="text-xs font-bold tracking-[.25em] text-brand-600 uppercase">{{ __('Material Canggih & Inovasi Medis') }}</span>
                        <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-gray-900 lg:text-4xl">{{ __('Material Canggih') }} <span class="text-brand-gradient">MGISpec</span></h2>
                        <p class="mt-2 max-w-2xl text-sm text-gray-500">{{ __('Performa Unggul Setiap Hari — Formulasi 10 elemen teknologi kesehatan terintegrasi dalam setiap bingkai kacamata.') }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <button type="button" onclick="document.getElementById('infographic-modal').classList.remove('hidden')"
                                class="inline-flex items-center gap-2 rounded-full border border-brand-500/30 bg-brand-50 px-4 py-2 text-xs font-bold text-brand-700 shadow-sm transition hover:bg-brand-100 hover:text-brand-800">
                            <i class="fa-solid fa-circle-nodes"></i> {{ __('Lihat Diagram Infografis') }}
                        </button>
                        <div class="flex gap-2">
                            <button type="button" data-prev class="carousel-nav" aria-label="{{ __('Sebelumnya') }}"><i class="fa-solid fa-arrow-left text-sm"></i></button>
                            <button type="button" data-next class="carousel-nav" aria-label="{{ __('Berikutnya') }}"><i class="fa-solid fa-arrow-right text-sm"></i></button>
                        </div>
                    </div>
                </div>

                <div class="swiper !overflow-visible" data-animate>
                    <div class="swiper-wrapper">
                        @foreach ($promos as $promo)
                            @php $tag = $promo->link_url ? 'a' : 'div'; @endphp
                            <div class="swiper-slide !h-auto">
                                @if ($promo->image)
                                    <{{ $tag }} @if ($promo->link_url) href="{{ \App\Support\I18n::link($promo->link_url) }}" @endif
                                        class="promo-card block !bg-none">
                                        <img src="{{ \App\Support\Media::url($promo->image) }}" alt="{{ $promo->t('title') }}" loading="lazy" class="h-full w-full object-cover">
                                    </{{ $tag }}>
                                @else
                                    <{{ $tag }} @if ($promo->link_url) href="{{ \App\Support\I18n::link($promo->link_url) }}" @endif
                                        class="promo-card flex flex-col items-center justify-end p-5 text-center text-white sm:p-6">
                                        <i class="fa-solid fa-star sparkle top-[18%] left-[14%] text-xs"></i>
                                        <i class="fa-solid fa-heart sparkle top-[12%] right-[16%] text-sm [animation-delay:.8s]"></i>
                                        <i class="fa-solid fa-star sparkle bottom-[38%] right-[10%] text-[10px] [animation-delay:1.6s]"></i>
                                        @if ($promo->badge)
                                            <span class="absolute top-4 left-4 grid h-12 min-w-12 place-items-center rounded-full bg-ink px-2 text-[10px] font-extrabold tracking-wide">{{ $promo->badge }}</span>
                                        @endif
                                        <div class="mb-auto grid flex-1 place-items-center">
                                            <span class="grid h-24 w-24 place-items-center rounded-full bg-white/15 ring-1 ring-white/30 backdrop-blur-sm transition duration-700 group-hover:scale-110 sm:h-28 sm:w-28">
                                                <i class="{{ $promo->icon ?: 'fa-solid fa-glasses' }} text-4xl drop-shadow sm:text-5xl"></i>
                                            </span>
                                        </div>
                                        <span class="rounded-xl bg-brand-700/90 px-4 py-1.5 text-base font-extrabold tracking-wide uppercase shadow-lg ring-2 ring-white/60 sm:text-lg">{{ $promo->t('title') }}</span>
                                        @if ($desc = $promo->t('description'))
                                            <span class="mt-3 rounded-md bg-white/90 px-3 py-1 text-[11px] leading-snug font-medium text-gray-800 sm:text-xs">{{ $desc }}</span>
                                        @endif
                                    </{{ $tag }}>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Infographic Modal (z-index, backdrop and max-height are inline because the deployed CSS build lacks those arbitrary classes; without them the Swiper cards cover the modal on mobile) --}}
            <div id="infographic-modal" class="fixed inset-0 hidden flex items-center justify-center p-4 backdrop-blur-sm transition-opacity" style="z-index: 100; background-color: rgba(0, 0, 0, .75)" onclick="if(event.target===this) this.classList.add('hidden')">
                <div class="relative max-w-2xl overflow-hidden rounded-3xl bg-white shadow-2xl" style="max-height: 92vh">
                    <div class="flex items-center justify-between border-b px-6 py-4">
                        <div class="flex items-center gap-2.5">
                            <span class="grid h-7 w-7 place-items-center rounded-full bg-brand-50 text-brand-600">
                                <i class="fa-solid fa-circle-nodes text-xs"></i>
                            </span>
                            <h3 class="text-sm font-bold text-gray-900">{{ __('Diagram Material Canggih MGISpec') }}</h3>
                        </div>
                        <button type="button" onclick="document.getElementById('infographic-modal').classList.add('hidden')" class="grid h-8 w-8 place-items-center rounded-full text-gray-400 hover:bg-gray-100 hover:text-gray-700" aria-label="Close">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>
                    <div class="overflow-y-auto p-4 sm:p-6 text-center" style="max-height: calc(92vh - 80px)">
                        @php $infoImg = app()->getLocale() === 'en' ? 'images/infografis-material-en.png' : 'images/infografis-material-id.png'; @endphp
                        <img src="{{ asset($infoImg) }}" alt="{{ __('Diagram Material Canggih MGISpec') }}" class="mx-auto h-auto max-w-full rounded-2xl shadow-sm">
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- Di atas footer: thumbnail semua produk, TANPA harga --}}
    @if ($products->isNotEmpty())
        <section class="bg-gray-50 py-16 lg:py-24">
            <div class="mx-auto max-w-[1440px] px-4 sm:px-6 lg:px-10" data-carousel data-free="true" data-autoplay="2600" data-loop="{{ $products->count() > 6 ? 'true' : 'false' }}"
                 data-breakpoints='{"480":{"slidesPerView":2.3},"768":{"slidesPerView":3.3,"spaceBetween":20},"1024":{"slidesPerView":4.3,"spaceBetween":24},"1280":{"slidesPerView":5.3,"spaceBetween":24},"1600":{"slidesPerView":6,"spaceBetween":24}}'>
                <div class="mb-10 flex flex-wrap items-end justify-between gap-6" data-animate>
                    <div>
                        <span class="text-xs font-bold tracking-[.25em] text-brand-600 uppercase">{{ __('Semua Koleksi') }}</span>
                        <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-gray-900 lg:text-4xl">{{ __('Katalog Produk MGISPEC') }}</h2>
                    </div>
                    <div class="flex items-center gap-4">
                        <a href="{{ route('catalog.index') }}" class="hidden text-sm font-semibold text-gray-900 underline decoration-brand-500 decoration-2 underline-offset-8 hover:text-brand-600 sm:inline">
                            {{ __('Lihat semua') }} ({{ $products->count() }})
                        </a>
                        <div class="flex gap-2">
                            <button type="button" data-prev class="carousel-nav" aria-label="{{ __('Sebelumnya') }}"><i class="fa-solid fa-arrow-left text-sm"></i></button>
                            <button type="button" data-next class="carousel-nav" aria-label="{{ __('Berikutnya') }}"><i class="fa-solid fa-arrow-right text-sm"></i></button>
                        </div>
                    </div>
                </div>

                <div class="swiper" data-animate>
                    <div class="swiper-wrapper">
                        @foreach ($products as $product)
                            <div class="swiper-slide !h-auto">@include('partials.product-card')</div>
                        @endforeach
                    </div>
                </div>
                <div data-progress class="marquee-progress relative mt-8"></div>

                <a href="{{ route('catalog.index') }}" class="mt-8 flex items-center justify-center gap-2 rounded-full bg-ink px-6 py-3.5 text-sm font-semibold text-white sm:hidden">
                    {{ __('Lihat semua') }} <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>
            </div>
        </section>
    @endif
@endsection
