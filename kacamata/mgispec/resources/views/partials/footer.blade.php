@php
    use App\Models\Setting;
@endphp
<footer class="relative overflow-hidden bg-ink text-gray-400">
    <div class="pointer-events-none absolute -top-40 left-1/2 h-80 w-[60rem] -translate-x-1/2 rounded-full bg-brand-600/20 blur-3xl"></div>

    <div class="relative mx-auto max-w-[1440px] px-4 pt-16 pb-8 sm:px-6 lg:px-10">
        <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-12">
            <div id="profile" class="lg:col-span-4 scroll-mt-24">
                <img src="{{ asset('images/logo-sm.png') }}" alt="MGISpec" class="h-11 w-auto">
                <p class="mt-5 max-w-sm text-sm leading-relaxed">{{ Setting::get('footer_about') }}</p>
                <div class="mt-6 flex gap-3">
                    @foreach (['facebook' => 'fa-facebook-f', 'instagram' => 'fa-instagram'] as $key => $icon)
                        @if ($link = Setting::get($key))
                            <a href="{{ $link }}" target="_blank" rel="noopener" aria-label="{{ ucfirst($key) }}"
                               class="grid h-10 w-10 place-items-center rounded-full bg-white/5 text-white ring-1 ring-white/10 transition hover:bg-brand-600">
                                <i class="fa-brands {{ $icon }}"></i>
                            </a>
                        @endif
                    @endforeach
                    <a href="https://wa.me/{{ Setting::get('whatsapp') }}" target="_blank" rel="noopener" aria-label="WhatsApp"
                       class="grid h-10 w-10 place-items-center rounded-full bg-white/5 text-white ring-1 ring-white/10 transition hover:bg-[#25D366]">
                        <i class="fa-brands fa-whatsapp"></i>
                    </a>
                </div>
            </div>

            <div class="lg:col-span-2">
                <h4 class="text-xs font-semibold uppercase tracking-[.2em] text-white">{{ __('Halaman') }}</h4>
                <ul class="mt-5 space-y-3 text-sm">
                    <li><a href="{{ route('home') }}" class="transition hover:text-white">{{ __('Beranda') }}</a></li>
                    <li><a href="{{ route('catalog.index') }}" class="transition hover:text-white">{{ __('Katalog MGISPEC') }}</a></li>
                    <li><a href="{{ route('home') }}#profile" class="transition hover:text-white">{{ __('Profil MGISPEC') }}</a></li>
                    <li><a href="{{ route('home') }}#technologies" class="transition hover:text-white">{{ __('Teknologi MGISPEC') }}</a></li>
                    <li><a href="{{ route('home') }}#certifications" class="transition hover:text-white">{{ __('Sertifikasi MGISPEC') }}</a></li>
                    <li><a href="{{ route('contact') }}" class="transition hover:text-white">{{ __('Kontak MGISPEC') }}</a></li>
                </ul>
            </div>

            <div id="certifications" class="lg:col-span-3 scroll-mt-24">
                <h4 class="text-xs font-semibold uppercase tracking-[.2em] text-white">{{ __('Sertifikasi Resmi') }}</h4>
                @php
                    $certs = [
                        ['FDA Registered', ['U.S. Food and Drug Administration']],
                        ['Halal Registered', ['Halal Indonesia Reg RI HL CN000128113226', 'Halal Certification Services Chongqing (HCS) HCS-BSD260813']],
                        ['Clinical Test Report', ['Huaxia Eye Hospital']],
                        ['Liability Insurance', ['Great Eastern']],
                        [__('Sertifikasi Lainnya'), ['RoHS Certification & CE', 'ISO 9001 · ISO 13485 · ISO 12312-1 · GMP']],
                    ];
                @endphp
                <ul class="mt-5 space-y-4 text-sm">
                    @foreach ($certs as [$title, $lines])
                        <li class="flex gap-3"><i class="fa-solid fa-circle-check mt-1 text-brand-400"></i><span><strong class="text-gray-200">{{ $title }}</strong>@foreach ($lines as $line)<br>{{ $line }}@endforeach</span></li>
                    @endforeach
                </ul>
            </div>

            <div id="contact" class="lg:col-span-3 scroll-mt-24">
                <h4 class="text-xs font-semibold uppercase tracking-[.2em] text-white">{{ __('Hubungi Kami') }}</h4>
                <p class="mt-5 text-sm leading-relaxed text-gray-400">
                    {{ __('MGISpec melayani jaringan perwakilan dan kantor cabang resmi di berbagai negara untuk kemudahan konsultasi Anda.') }}
                </p>
                <div class="mt-4">
                    <a href="{{ route('contact') }}"
                       class="inline-flex items-center gap-2 rounded-full brand-gradient px-4 py-2.5 text-xs font-bold text-white shadow-md shadow-brand-500/20 transition hover:-translate-y-0.5 hover:shadow-lg">
                        <i class="fa-solid fa-globe"></i> {{ __('Lihat Kontak & Cabang Negara') }}
                    </a>
                </div>
                <div class="mt-4 flex items-center gap-2.5 text-xs text-gray-400">
                    <i class="fa-brands fa-whatsapp text-base text-[#25D366]"></i>
                    <span>{{ __('Layanan Cepat:') }} <a href="https://wa.me/{{ Setting::get('whatsapp') }}" target="_blank" rel="noopener" class="text-white hover:underline">{{ Setting::get('phone') }}</a></span>
                </div>
            </div>
        </div>

        <div class="mt-14 border-t border-white/10 pt-6 text-center text-xs tracking-wide">
            {{ Setting::get('copyright') }}
        </div>
    </div>
</footer>
