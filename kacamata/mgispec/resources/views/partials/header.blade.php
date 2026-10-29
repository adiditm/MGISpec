@php
    use App\Support\I18n;

    $isHome = request()->routeIs('home');
    $nav = [
        ['label' => __('Beranda'), 'url' => route('home'), 'active' => $isHome && ! request()->has('section')],
        ['label' => __('Katalog MGISPEC'), 'url' => route('catalog.index'), 'active' => request()->routeIs('catalog.*')],
        // Profil, Teknologi and Sertifikasi MGISPEC links are hidden until those pages are ready (feedback 27-10-2026)
        ['label' => __('Kontak MGISPEC'), 'url' => route('contact'), 'active' => request()->routeIs('contact')],
    ];
    $locale = app()->getLocale();
@endphp

<header data-header data-over-hero="{{ $overHero ? 'true' : 'false' }}" data-transparent="{{ $overHero ? 'true' : 'false' }}"
        class="site-header fixed inset-x-0 top-0 z-50">
    <div class="mx-auto flex h-[72px] max-w-[1440px] items-center justify-between gap-6 px-4 sm:px-6 lg:h-[84px] lg:px-10">
        <a href="{{ route('home') }}" class="shrink-0" aria-label="MGISpec — Home">
            <img src="{{ asset('images/logo-sm.png') }}" alt="MGISpec" class="h-9 w-auto drop-shadow-[0_2px_8px_rgba(0,0,0,.25)] lg:h-11" width="640" height="174">
        </a>

        <nav class="hidden items-center gap-7 text-[0.9rem] font-medium xl:flex" aria-label="Main">
            @foreach ($nav as $item)
                <a href="{{ $item['url'] }}" class="nav-link whitespace-nowrap" @if ($item['active'] ?? false) aria-current="page" @endif>{{ $item['label'] }}</a>
            @endforeach
        </nav>

        <div class="flex items-center gap-2 sm:gap-3">
            {{-- Language switch: segmented ID | EN (CN prepared) --}}
            <div class="hidden items-center rounded-full border border-current/25 p-1 text-xs font-semibold sm:flex" role="group" aria-label="Language">
                @foreach (I18n::enabledLocales() as $code => $meta)
                    <a href="{{ I18n::switchUrl($code) }}" hreflang="{{ $code }}" title="{{ $meta['label'] }}"
                       class="flex items-center gap-1.5 rounded-full px-2.5 py-1 transition {{ $code === $locale ? 'bg-white text-gray-900 shadow' : 'opacity-70 hover:opacity-100' }}">
                        @include('partials.flag', ['code' => $code, 'class' => 'h-4 w-4', 'uid' => 'h'])
                        {{ $meta['short'] }}
                    </a>
                @endforeach
            </div>

            <a href="https://member.mgiclub.com/member/login.jsp" target="_blank" rel="noopener noreferrer"
               class="hidden items-center gap-2 rounded-full px-3 py-2 text-sm font-medium transition hover:bg-current/10 sm:flex">
                <i class="fa-regular fa-circle-user text-lg"></i> {{ __('Login') }}
            </a>

            @if ($fb = \App\Models\Setting::get('facebook'))
                <a href="{{ $fb }}" target="_blank" rel="noopener" aria-label="Facebook" class="hidden h-10 w-10 place-items-center rounded-full transition hover:bg-current/10 sm:grid">
                    <i class="fa-brands fa-facebook-f"></i>
                </a>
            @endif

            <button type="button" data-drawer-open class="grid h-11 w-11 place-items-center rounded-full transition hover:bg-current/10 xl:hidden" aria-label="Menu">
                <i class="fa-solid fa-bars-staggered text-xl"></i>
            </button>
        </div>
    </div>
</header>

{{-- Mobile drawer --}}
<div data-drawer-backdrop data-drawer-close class="fixed inset-0 z-[60] hidden bg-black/50 backdrop-blur-sm"></div>
<aside data-drawer class="fixed inset-y-0 right-0 z-[70] flex w-[86%] max-w-sm translate-x-full flex-col bg-white shadow-2xl transition-transform duration-500 ease-[cubic-bezier(.16,1,.3,1)]" aria-label="Menu">
    <div class="flex items-center justify-between border-b px-5 py-4">
        <img src="{{ asset('images/logo-sm.png') }}" alt="MGISpec" class="h-8 w-auto">
        <button type="button" data-drawer-close class="grid h-10 w-10 place-items-center rounded-full hover:bg-gray-100" aria-label="Close">
            <i class="fa-solid fa-xmark text-xl"></i>
        </button>
    </div>
    <nav class="flex-1 overflow-y-auto px-3 py-4">
        @foreach ($nav as $item)
            <a href="{{ $item['url'] }}" class="flex items-center justify-between rounded-xl px-4 py-3.5 text-[15px] font-medium {{ ($item['active'] ?? false) ? 'bg-brand-50 text-brand-700' : 'text-gray-800 hover:bg-gray-50' }}">
                {{ $item['label'] }} <i class="fa-solid fa-chevron-right text-xs opacity-40"></i>
            </a>
        @endforeach
    </nav>
    <div class="space-y-3 border-t p-5">
        <div class="grid grid-cols-2 gap-2">
            @foreach (I18n::enabledLocales() as $code => $meta)
                <a href="{{ I18n::switchUrl($code) }}" class="flex items-center justify-center gap-2 rounded-xl border px-3 py-2.5 text-sm font-semibold {{ $code === $locale ? 'border-brand-500 bg-brand-50 text-brand-700' : 'text-gray-600' }}">
                    @include('partials.flag', ['code' => $code, 'uid' => 'm'])
                    {{ $meta['label'] }}
                </a>
            @endforeach
        </div>
        <a href="https://member.mgiclub.com/member/login.jsp" target="_blank" rel="noopener noreferrer"
           class="flex items-center justify-center gap-2 rounded-xl bg-ink px-4 py-3 text-sm font-semibold text-white">
            <i class="fa-regular fa-circle-user"></i> {{ __('Login') }}
        </a>
    </div>
</aside>
