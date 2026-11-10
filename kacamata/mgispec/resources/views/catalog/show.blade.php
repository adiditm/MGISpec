@php
    use App\Models\Setting;
    use App\Support\I18n;

    $name = $product->t('name');
    $images = $product->imageUrls();
    $waText = __('Halo MGISpec, saya tertarik dengan produk :name (SKU: :sku). Mohon info lebih lanjut.', ['name' => $name, 'sku' => $product->sku]);
    $waUrl = 'https://wa.me/'.Setting::get('whatsapp').'?text='.rawurlencode($waText.' '.url()->current());
    $highlights = collect($product->highlights ?? [])->map(fn ($h) => I18n::pick($h['text'] ?? null))->filter();
    $specs = collect($product->specs ?? [])->filter(fn ($s) => filled($s['value'] ?? null));
    // null = not migrated / never filled: fall back to the old single list (lens) and the lorem ipsum sample (frame)
    $frameTechs = collect($product->frame_technologies ?? \App\Models\Product::sampleFrameTechnologies())->filter(fn ($t) => filled(I18n::pick($t['title'] ?? null)));
    $lensTechs = collect($product->lens_technologies ?? $product->technologies ?? [])->filter(fn ($t) => filled(I18n::pick($t['title'] ?? null)));
    $techTabs = ['frame' => [__('Teknologi Frame'), $frameTechs], 'lens' => [__('Teknologi Lensa'), $lensTechs]];
    $description = $product->t('description');

    // Banner thumbnail per model (CMS: Produk > Foto > Banner), shown under the breadcrumb; nothing when the product has none.
    $bannerFile = $product->bannerUrl();
    $catalogUrl = $product->catalogUrl(); // CMS: Produk > Foto > Katalog PDF; no file = no download button
    $bannerHtml = $bannerFile ? '<div class="mgi-pbanner"><img src="'.e($bannerFile).'" alt="'.e($name).'" decoding="async"></div>' : '';
@endphp

@extends('layouts.app', ['title' => $name, 'description' => $product->t('short_description')])

@section('content')
    {{-- phones: the single grid column must be allowed to shrink (minmax(0,1fr)); otherwise the gallery swiper stretches it wider than the screen and the whole page overflows sideways --}}
    <style>@media (max-width: 1023px) { .mgi-pgrid { grid-template-columns: minmax(0, 1fr); } }</style>
    @if ($bannerFile)
        <style>
            .mgi-pbanner { margin: 1.25rem 0 0; border-radius: 1rem; overflow: hidden; line-height: 0; background: #e8f1fb; }
            .mgi-pbanner img { display: block; width: 100%; height: auto; }
            /* phones stack everything vertically, so keep the space above/below the banner tight */
            @media (max-width: 1023px) { .mgi-pbanner { margin-top: .625rem; border-radius: .75rem; } .mgi-pbanner + .mgi-pgrid { margin-top: .875rem; } }
        </style>
    @endif
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

            {!! $bannerHtml !!}
            <div class="mgi-pgrid mt-6 grid gap-8 lg:mt-8 lg:grid-cols-2 lg:gap-14">
                {{-- Gallery (min-width: 0 stops Swiper slides from stretching the single auto grid column on mobile) --}}
                <div data-gallery class="lg:sticky lg:top-28 lg:self-start" style="min-width: 0">
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
                        <a href="{{ $waUrl }}" data-wa-choose data-wa-text="{{ $waText.' '.url()->current() }}" target="_blank" rel="noopener"
                           class="inline-flex flex-1 items-center justify-center gap-3 rounded-full bg-[#25D366] px-7 py-4 text-base font-semibold text-white shadow-lg shadow-green-500/25 transition hover:-translate-y-0.5 hover:bg-[#1ebe5a]">
                            <i class="fa-brands fa-whatsapp text-2xl"></i> {{ __('Tanya via WhatsApp') }}
                        </a>
                        @if ($catalogUrl)
                            <a href="{{ $catalogUrl }}" download="{{ basename(parse_url($catalogUrl, PHP_URL_PATH)) }}" target="_blank" rel="noopener"
                               class="inline-flex items-center justify-center gap-2 rounded-full px-6 py-4 text-sm font-semibold text-gray-700 ring-1 ring-gray-300 transition hover:ring-gray-900">
                                <i class="fa-solid fa-download text-xs"></i> {{ __('Unduh Katalog') }}
                            </a>
                        @endif
                    </div>

                    {{-- Share this page (partners post the product link on their own social media). Self-contained CSS/JS: the server's Tailwind build lacks some utilities. --}}
                    @php
                        $shareUrl = url()->current();
                        $shareTitle = $name;
                        $shareEnc = rawurlencode($shareUrl);
                        $shareTitleEnc = rawurlencode($shareTitle);
                        $shareLinks = [
                            ['Facebook', 'fa-facebook-f', 'https://www.facebook.com/sharer/sharer.php?u='.$shareEnc, '#1877F2'],
                            ['Instagram', 'fa-instagram', null, '#E4405F'],
                            ['TikTok', 'fa-tiktok', null, '#000000'],
                            ['X', 'fa-x-twitter', 'https://twitter.com/intent/tweet?url='.$shareEnc.'&text='.$shareTitleEnc, '#000000'],
                            ['Threads', 'fa-threads', 'https://www.threads.net/intent/post?text='.rawurlencode($shareTitle.' '.$shareUrl), '#000000'],
                            ['WhatsApp', 'fa-whatsapp', 'https://wa.me/?text='.rawurlencode($shareTitle.' '.$shareUrl), '#25D366'],
                            ['Telegram', 'fa-telegram', 'https://t.me/share/url?url='.$shareEnc.'&text='.$shareTitleEnc, '#26A5E4'],
                            ['LINE', 'fa-line', 'https://social-plugins.line.me/lineit/share?url='.$shareEnc, '#06C755'],
                            ['LinkedIn', 'fa-linkedin-in', 'https://www.linkedin.com/sharing/share-offsite/?url='.$shareEnc, '#0A66C2'],
                        ];
                        if (app()->getLocale() === 'cn') {
                            $shareLinks[] = ['微博', 'fa-weibo', 'https://service.weibo.com/share/share.php?url='.$shareEnc.'&title='.$shareTitleEnc, '#E6162D'];
                        }
                    @endphp
                    <style>
                        .mgi-share { margin-top: 1.25rem; display: flex; flex-wrap: wrap; align-items: center; gap: .5rem; }
                        .mgi-share-label { margin-right: .25rem; font-size: .75rem; font-weight: 600; letter-spacing: .08em; text-transform: uppercase; color: #9ca3af; }
                        .mgi-share a, .mgi-share button { display: grid; place-items: center; width: 2.25rem; height: 2.25rem; border-radius: 9999px; border: 1px solid #e5e7eb; background: #fff; color: #4b5563; font-size: .95rem; cursor: pointer; padding: 0; transition: color .15s, background-color .15s, border-color .15s, transform .15s; }
                        .mgi-share a:hover, .mgi-share button:hover, .mgi-share a:focus-visible, .mgi-share button:focus-visible { color: #fff; background: var(--c); border-color: var(--c); transform: translateY(-2px); outline: none; }
                        .mgi-share-toast { position: fixed; left: 50%; bottom: 5.5rem; z-index: 90; transform: translate(-50%, .5rem); max-width: calc(100vw - 2rem); padding: .625rem 1rem; border-radius: 9999px; background: #111827; color: #fff; font-size: .8125rem; font-weight: 500; text-align: center; opacity: 0; pointer-events: none; transition: opacity .2s, transform .2s; }
                        .mgi-share-toast.is-on { opacity: 1; transform: translate(-50%, 0); }
                    </style>
                    <div class="mgi-share" data-share data-share-url="{{ $shareUrl }}" data-share-title="{{ $shareTitle }}" data-msg-copied="{{ __('Tautan disalin') }}" data-msg-app="{{ __('Tautan disalin, tempel di :app') }}">
                        <span class="mgi-share-label">{{ __('Bagikan') }}</span>
                        @foreach ($shareLinks as [$label, $icon, $href, $color])
                            @if ($href)
                                <a href="{{ $href }}" target="_blank" rel="noopener noreferrer" style="--c: {{ $color }}" title="{{ __('Bagikan ke') }} {{ $label }}" aria-label="{{ __('Bagikan ke') }} {{ $label }}"><i class="fa-brands {{ $icon }}"></i></a>
                            @else
                                {{-- Instagram and TikTok have no web share link: phones open the system share sheet, desktops copy the link --}}
                                <button type="button" data-share-app="{{ $label }}" style="--c: {{ $color }}" title="{{ __('Bagikan ke') }} {{ $label }}" aria-label="{{ __('Bagikan ke') }} {{ $label }}"><i class="fa-brands {{ $icon }}"></i></button>
                            @endif
                        @endforeach
                        <button type="button" data-share-copy style="--c: #111827" title="{{ __('Salin tautan') }}" aria-label="{{ __('Salin tautan') }}"><i class="fa-solid fa-link"></i></button>
                    </div>
                    <div class="mgi-share-toast" data-share-toast role="status" aria-live="polite"></div>
                    <script>
                        (function () {
                            var box = document.querySelector('[data-share]');
                            if (!box) return;
                            var toast = document.querySelector('[data-share-toast]'), timer;
                            var url = box.dataset.shareUrl, title = box.dataset.shareTitle;
                            function say(msg) {
                                toast.textContent = msg; toast.classList.add('is-on');
                                clearTimeout(timer); timer = setTimeout(function () { toast.classList.remove('is-on'); }, 2600);
                            }
                            function copy(msg) {
                                function fallback() {
                                    var t = document.createElement('textarea');
                                    t.value = url; t.setAttribute('readonly', ''); t.style.cssText = 'position:fixed;opacity:0';
                                    document.body.appendChild(t); t.select();
                                    try { document.execCommand('copy'); say(msg); } catch (e) { window.prompt('', url); }
                                    document.body.removeChild(t);
                                }
                                if (navigator.clipboard && window.isSecureContext) {
                                    navigator.clipboard.writeText(url).then(function () { say(msg); }, fallback);
                                } else { fallback(); }
                            }
                            box.querySelector('[data-share-copy]').addEventListener('click', function () { copy(box.dataset.msgCopied); });
                            box.querySelectorAll('[data-share-app]').forEach(function (btn) {
                                btn.addEventListener('click', function () {
                                    var touch = window.matchMedia && window.matchMedia('(pointer: coarse)').matches;
                                    if (touch && navigator.share) {
                                        navigator.share({ title: title, url: url }).catch(function () {});
                                    } else { copy(box.dataset.msgApp.replace(':app', btn.dataset.shareApp)); }
                                });
                            });
                        })();
                    </script>

                    <div class="mt-8 grid grid-cols-2 gap-3 text-xs text-gray-600">
                        <div class="flex items-center gap-3 rounded-2xl bg-gray-50 p-4"><i class="fa-solid fa-shield-halved text-xl text-brand-600"></i><span><strong class="block text-gray-900">FDA Registered</strong>U.S. FDA</span></div>
                    </div>

                    {{-- Tabs --}}
                    <div data-tabs class="mt-12">
                        <div class="hide-scrollbar flex gap-6 overflow-x-auto border-b border-gray-200 text-sm font-semibold" role="tablist">
                            @if ($description)<button type="button" data-tab="desc" class="tab-btn">{{ __('Deskripsi') }}</button>@endif
                            @if ($specs->isNotEmpty())<button type="button" data-tab="spec" class="tab-btn">{{ __('Spesifikasi') }}</button>@endif
                            @foreach ($techTabs as $key => [$label, $items])
                                @if ($items->isNotEmpty())<button type="button" data-tab="tech-{{ $key }}" class="tab-btn">{{ $label }}</button>@endif
                            @endforeach
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
                        {{-- Teknologi Frame / Teknologi Lensa: icon cards, 2 columns on desktop, 1 on mobile --}}
                        @foreach ($techTabs as $key => [$label, $items])
                            @if ($items->isNotEmpty())
                                <div data-panel="tech-{{ $key }}" class="grid grid-cols-1 gap-4 pt-6 md:grid-cols-2">
                                    @foreach ($items as $t)
                                        <div class="rounded-2xl p-5 ring-1 ring-gray-200">
                                            <span class="brand-gradient grid h-11 w-11 place-items-center rounded-xl text-white"><i class="{{ $t['icon'] ?? 'fa-solid fa-atom' }}"></i></span>
                                            <h3 class="mt-4 font-semibold text-gray-900">{{ I18n::pick($t['title'] ?? null) }}</h3>
                                            @php
                                                // description lines: "- text" lines become a bullet list, other lines stay paragraphs
                                                $techBlocks = [];
                                                foreach (preg_split('/\R/', trim((string) I18n::pick($t['description'] ?? null))) ?: [] as $techLine) {
                                                    $techLine = trim($techLine);
                                                    if ($techLine === '') {
                                                        continue;
                                                    }
                                                    if (str_starts_with($techLine, '- ')) {
                                                        $last = array_key_last($techBlocks);
                                                        if ($last !== null && $techBlocks[$last][0] === 'ul') {
                                                            $techBlocks[$last][1][] = substr($techLine, 2);
                                                        } else {
                                                            $techBlocks[] = ['ul', [substr($techLine, 2)]];
                                                        }
                                                    } else {
                                                        $techBlocks[] = ['p', $techLine];
                                                    }
                                                }
                                            @endphp
                                            <div class="mt-1.5 text-sm leading-relaxed text-gray-500">
                                                @foreach ($techBlocks as [$techType, $techBody])
                                                    @if ($techType === 'ul')
                                                        <ul style="list-style:disc;padding-left:1.1rem;margin:.4rem 0 0">
                                                            @foreach ($techBody as $techItem)<li style="margin-top:.2rem">{{ $techItem }}</li>@endforeach
                                                        </ul>
                                                    @else
                                                        <p style="margin:.4rem 0 0">{{ $techBody }}</p>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- the banner already carries the certification logos, so the Sertifikat slider is hidden on products that have one --}}
    @unless ($bannerFile)
        @include('partials.certificates')
    @endunless

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
