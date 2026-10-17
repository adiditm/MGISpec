@php
    use App\Models\Setting;
    $wa = Setting::get('whatsapp');
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() === 'cn' ? 'zh-CN' : (app()->getLocale() === 'en' ? 'en' : 'id') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ isset($title) ? $title.' · ' : '' }}MGISpec — Bio-Tech Oxygen Glasses</title>
    <meta name="description" content="{{ $description ?? __('Kacamata medis berteknologi ion & oksigen. Terdaftar FDA & MDA Malaysia.') }}">
    <link rel="icon" href="{{ asset('images/logo-sm.png') }}">
    @foreach (\App\Support\I18n::enabledLocales() as $code => $meta)
        <link rel="alternate" hreflang="{{ $code === 'cn' ? 'zh-CN' : $code }}" href="{{ \App\Support\I18n::switchUrl($code) }}">
    @endforeach
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    @stack('head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col">
    @include('partials.header', ['overHero' => $overHero ?? false])

    <main class="flex-1 {{ ($overHero ?? false) ? '' : 'pt-[72px] lg:pt-[84px]' }}">
        @yield('content')
    </main>

    @include('partials.footer')

    {{-- Floating contact, as on the client's reference site --}}
    <a href="https://wa.me/{{ $wa }}?text={{ rawurlencode(__('Halo MGISpec, saya ingin bertanya tentang produk kacamata.')) }}"
       target="_blank" rel="noopener"
       class="group fixed bottom-5 right-5 z-40 flex items-center gap-2">
        <span class="hidden sm:block rounded-full bg-white px-4 py-2.5 text-sm font-semibold text-gray-900 shadow-xl ring-1 ring-black/5 transition group-hover:-translate-x-1">
            {{ __('Hubungi kami') }}
        </span>
        <span class="relative grid h-14 w-14 place-items-center rounded-full bg-[#25D366] text-white shadow-xl shadow-green-600/30 transition group-hover:scale-110">
            <span class="absolute inset-0 rounded-full bg-[#25D366] opacity-60 animate-ping [animation-duration:2.5s]"></span>
            <i class="fa-brands fa-whatsapp relative text-[1.7rem]"></i>
        </span>
    </a>
</body>
</html>
