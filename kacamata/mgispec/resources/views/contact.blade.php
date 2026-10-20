@extends('layouts.app', ['title' => __('Kontak & Kantor Cabang Internasional')])

@section('content')
    {{-- Hero Section --}}
    <section class="relative overflow-hidden bg-ink text-white">
        <div class="pointer-events-none absolute -top-32 -right-20 h-96 w-96 rounded-full bg-brand-600/40 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-40 left-10 h-80 w-80 rounded-full bg-sun-500/25 blur-3xl"></div>
        <div class="relative mx-auto max-w-[1440px] px-4 py-14 sm:px-6 lg:px-10 lg:py-20">
            <nav class="text-xs text-white/50" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="hover:text-white">{{ __('Home') }}</a>
                <span class="mx-2">/</span><span class="text-white/80">{{ __('Kontak') }}</span>
            </nav>
            <span class="mt-4 inline-block text-xs font-bold tracking-[.25em] text-sun-400 uppercase">{{ __('Jaringan Representatif Resmi') }}</span>
            <h1 class="mt-2 text-3xl font-extrabold tracking-tight sm:text-4xl lg:text-6xl">
                {{ __('Kontak & Kantor Cabang Internasional') }}
            </h1>
            <p class="mt-4 max-w-2xl text-white/70 lg:text-lg">
                {{ __('MGISpec hadir melalui jaringan kantor cabang dan perwakilan regional di berbagai negara untuk melayani konsultasi kesehatan mata, informasi produk, dan layanan pelanggan.') }}
            </p>
        </div>
    </section>

    {{-- Branch Offices Grid --}}
    <section class="bg-gray-50 py-12 lg:py-20">
        <div class="mx-auto max-w-[1440px] px-4 sm:px-6 lg:px-10">
            <div class="mb-10 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <span class="text-xs font-bold tracking-[.2em] text-brand-600 uppercase">{{ __('Multi-Negara') }}</span>
                    <h2 class="mt-1 text-2xl font-extrabold text-gray-900 sm:text-3xl">{{ __('Pilih Kantor Perwakilan Negara Anda') }}</h2>
                </div>
                <p class="text-xs text-gray-500 sm:text-sm">{{ __('Silakan hubungi perwakilan di negara terkait untuk layanan tercepat.') }}</p>
            </div>

            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-2">
                @foreach ($branches as $branch)
                    @php
                        $locale = app()->getLocale();
                        $countryName = $branch['country'][$locale] ?? $branch['country']['id'];
                        $hours = isset($branch['hours']) ? ($branch['hours'][$locale] ?? $branch['hours']['id']) : [];
                        $waText = __('Halo MGISpec :country, saya ingin konsultasi mengenai produk kacamata medis.', ['country' => $countryName]);
                        $waLink = isset($branch['whatsapp']) ? 'https://wa.me/'.$branch['whatsapp'].'?text='.rawurlencode($waText) : null;
                        $email = $branch['email'] ?? null;
                    @endphp

                    <div class="relative flex flex-col justify-between overflow-hidden rounded-3xl bg-white p-7 shadow-sm ring-1 ring-gray-200/80 transition duration-300 hover:shadow-xl hover:ring-brand-200 sm:p-8">
                        <div>
                            <div class="flex items-center gap-3">
                                <span class="text-3xl drop-shadow-sm">{{ $branch['flag'] }}</span>
                                <h3 class="text-xl font-extrabold text-gray-900">{{ $countryName }}</h3>
                            </div>

                            <ul class="mt-6 space-y-4 text-sm text-gray-600">
                                <li class="flex gap-3">
                                    <i class="fa-solid fa-location-dot mt-1 text-brand-600 shrink-0"></i>
                                    <span>
                                        @if (isset($branch['company']))<strong class="block font-semibold text-gray-900">{{ $branch['company'] }}</strong>@endif
                                        {{ $branch['address'] }}
                                    </span>
                                </li>
                                @if ($hours)
                                    <li class="flex gap-3">
                                        <i class="fa-solid fa-clock mt-1 text-brand-600 shrink-0"></i>
                                        <span>{!! collect($hours)->map(fn ($line) => e($line))->implode('<br>') !!}</span>
                                    </li>
                                @endif
                                @if (isset($branch['phone']))
                                    <li class="flex gap-3">
                                        <i class="fa-solid fa-phone mt-1 text-brand-600 shrink-0"></i>
                                        <a href="tel:{{ $branch['phone'] }}" class="font-medium text-gray-900 hover:text-brand-600">{{ $branch['phone'] }}</a>
                                    </li>
                                @endif
                                @if ($email)
                                    <li class="flex gap-3">
                                        <i class="fa-solid fa-envelope mt-1 text-brand-600 shrink-0"></i>
                                        <a href="mailto:{{ $email }}" class="font-medium text-gray-900 hover:text-brand-600">{{ $email }}</a>
                                    </li>
                                @endif
                            </ul>
                        </div>

                        @if ($waLink || $email)
                        <div class="mt-8 pt-6 border-t border-gray-100 flex flex-wrap items-center gap-3">
                            @if ($waLink)
                            <a href="{{ $waLink }}" target="_blank" rel="noopener"
                               class="inline-flex flex-1 items-center justify-center gap-2 rounded-full bg-[#25D366] px-5 py-3 text-xs sm:text-sm font-semibold text-white shadow-md shadow-green-500/20 transition hover:-translate-y-0.5 hover:bg-[#1ebe5a]">
                                <i class="fa-brands fa-whatsapp text-lg"></i> {{ __('Chat WhatsApp') }}
                            </a>
                            @endif
                            @if ($email)
                                <a href="mailto:{{ $email }}"
                                   class="inline-flex items-center justify-center gap-2 rounded-full border border-gray-300 bg-white px-5 py-3 text-xs sm:text-sm font-semibold text-gray-700 transition hover:border-gray-900">
                                    <i class="fa-solid fa-paper-plane"></i> {{ __('Kirim Email') }}
                                </a>
                            @endif
                        </div>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- General Support Banner --}}
            <div class="mt-14 overflow-hidden rounded-3xl brand-gradient p-8 text-white shadow-xl shadow-brand-500/20 sm:p-12">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div>
                        <span class="text-xs font-bold tracking-[.25em] text-sun-300 uppercase">{{ __('Bantuan & Kerjasama') }}</span>
                        <h3 class="mt-2 text-2xl font-extrabold sm:text-3xl">{{ __('Punya Pertanyaan Spesifik atau Tertarik Kemitraan?') }}</h3>
                        <p class="mt-2 max-w-xl text-white/80 text-sm sm:text-base">
                            {{ __('Tim spesialis kami siap membantu informasi resep lensa, teknologi material bio-ion, atau informasi jaringan distributor internasional.') }}
                        </p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3 shrink-0">
                        <a href="https://wa.me/{{ \App\Models\Setting::get('whatsapp') }}?text={{ rawurlencode('Halo MGISpec, saya ingin bertanya tentang informasi produk dan kemitraan.') }}" target="_blank" rel="noopener"
                           class="inline-flex items-center justify-center gap-2 rounded-full bg-white px-7 py-3.5 text-sm font-bold text-gray-900 shadow-md transition hover:bg-gray-100">
                            <i class="fa-brands fa-whatsapp text-lg text-[#25D366]"></i> {{ __('WhatsApp Pusat') }}
                        </a>
                        <a href="https://member.mgiclub.com/member/login.jsp" target="_blank" rel="noopener"
                           class="inline-flex items-center justify-center gap-2 rounded-full border border-white/40 bg-white/10 px-6 py-3.5 text-sm font-semibold text-white backdrop-blur transition hover:bg-white/20">
                            <i class="fa-solid fa-user-lock"></i> {{ __('Login Member') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
