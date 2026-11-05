@php
    use App\Models\Setting;
    use App\Models\Slider;
    use App\Support\I18n;
    use App\Support\Media;
    use Illuminate\Support\Facades\Storage;

    $alignment = [
        'left' => 'items-start text-left mr-auto',
        'center' => 'items-center text-center mx-auto',
        'right' => 'items-end text-right ml-auto',
    ];

    // The banners carry their own text, so the slide area takes the image's aspect ratio instead of the
    // full screen height; with object-fit: cover nothing gets cropped and every word stays readable.
    $ratio = function (?string $path): ?string {
        if (blank($path) || preg_match('#^(https?:)?/#', $path)) {
            return null;
        }
        $size = @getimagesize(Storage::disk('public')->path($path));

        return $size ? "{$size[0]} / {$size[1]}" : null;
    };
    $first = $sliders->first();
    $desktopRatio = $first ? $ratio($first->t('desktop_image')) : null;
    $mobileRatio = $first ? $ratio($first->t('mobile_image')) : null;
    $defaultEffect = Slider::effectKey(Setting::get('slider_effect', 'fade'));
@endphp

@if ($sliders->isNotEmpty())
<section data-hero data-delay="{{ Setting::get('slider_delay', 6) }}"
         class="hero relative isolate w-full overflow-hidden bg-black text-white" aria-roledescription="carousel" aria-label="{{ __('Banner utama') }}"
         style="@if ($desktopRatio) --hero-ratio: {{ $desktopRatio }}; @endif @if ($mobileRatio) --hero-ratio-mobile: {{ $mobileRatio }}; @endif">
    <div class="hero-track" data-hero-track>
        @foreach ($sliders as $i => $slide)
            @php
                $title = $slide->show_text ? $slide->t('title') : null;
                $badge = $slide->show_text ? $slide->t('badge') : null;
                $subtitle = $slide->show_text ? $slide->t('subtitle') : null;
                $cta = $slide->t('cta_text');
                $cta2 = $slide->t('cta2_text');
                $pos = $slide->text_position ?: 'left';
                $desktopImage = $slide->t('desktop_image');
                $mobileImage = $slide->t('mobile_image');
            @endphp
            <div class="hero-slide {{ $i === 0 ? 'is-active' : '' }}" data-hero-slide data-effect="{{ ($e = $slide->effect ? Slider::effectKey($slide->effect) : $defaultEffect) === 'random' ? 'fade' : $e }}" @if ($e === 'random') data-random @endif
                 data-pos="{{ $pos }}" role="group" aria-roledescription="slide" aria-label="{{ ($i + 1).' / '.$sliders->count() }}">
                <div class="hero-media">
                    <picture>
                        @if ($mobileImage)
                            <source media="(max-width: 767px)" srcset="{{ Media::url($mobileImage) }}">
                        @endif
                        <img src="{{ Media::url($desktopImage) }}" alt="{{ $slide->t('title') ?: 'MGISpec banner '.($i + 1) }}" class="hero-img"
                             @if ($i === 0) fetchpriority="high" @else loading="lazy" @endif>
                    </picture>
                </div>
                @if ($slide->overlay > 0)
                    <div class="hero-overlay" style="--overlay: {{ $slide->overlay / 100 }}"></div>
                @endif

                @if ($slide->video_path)
                    <button type="button" class="hero-play" data-hero-video="{{ Media::url($slide->video_path) }}"
                            data-poster="{{ Media::url($desktopImage) }}" aria-label="{{ __('Putar video') }}">
                        <span class="hero-play-icon"><i class="fa-solid fa-play"></i></span>
                        <span class="hero-play-label">{{ __('Putar video') }}</span>
                    </button>
                @endif

                @if ($title || $badge || $subtitle || $cta || $cta2)
                    <div class="relative z-10 mx-auto flex h-full max-w-[1440px] px-5 pt-24 pb-36 sm:px-10 lg:px-20 {{ $title || $subtitle ? 'items-center' : 'items-end' }}">
                        <div class="flex max-w-3xl flex-col {{ $alignment[$pos] ?? $alignment['left'] }}">
                            @if ($badge)
                                <span class="reveal mb-5 inline-flex items-center gap-2 rounded-full border border-white/25 bg-white/10 px-4 py-1.5 text-[11px] font-semibold uppercase tracking-[.22em] backdrop-blur-md sm:text-xs" style="--d:0">
                                    <span class="h-1.5 w-1.5 rounded-full bg-sun-400 shadow-[0_0_12px_2px] shadow-sun-400"></span>{{ $badge }}
                                </span>
                            @endif
                            @if ($title)
                                <h2 class="hero-title text-[2.6rem] leading-[1.02] font-extrabold tracking-tight text-balance drop-shadow-[0_4px_30px_rgba(0,0,0,.35)] sm:text-6xl lg:text-7xl xl:text-[5.5rem]" aria-label="{{ $title }}">
                                    @foreach (preg_split('/\s+/', trim($title)) as $w => $word)
                                        <span class="w" aria-hidden="true"><span style="--i:{{ $w }}">{{ $word }}</span></span>
                                    @endforeach
                                </h2>
                            @endif
                            @if ($subtitle)
                                <p class="reveal mt-6 max-w-xl text-base leading-relaxed font-light text-white/85 sm:text-lg lg:text-xl" style="--d:3">{{ $subtitle }}</p>
                            @endif
                            @if ($cta || $cta2)
                                <div class="reveal mt-9 flex flex-wrap gap-3 {{ $pos === 'center' ? 'justify-center' : ($pos === 'right' ? 'justify-end' : '') }}" style="--d:4">
                                    @if ($cta)
                                        <a href="{{ I18n::link($slide->cta_url) ?: route('catalog.index') }}" class="btn-hero btn-hero-primary"
                                           @if (str_starts_with((string) $slide->cta_url, 'http')) target="_blank" rel="noopener" @endif>
                                            {{ $cta }} <i class="fa-solid fa-arrow-right arrow text-sm"></i>
                                        </a>
                                    @endif
                                    @if ($cta2)
                                        <a href="{{ I18n::link($slide->cta2_url) ?: route('catalog.index') }}" class="btn-hero btn-hero-ghost"
                                           @if (str_starts_with((string) $slide->cta2_url, 'http')) target="_blank" rel="noopener" @endif>
                                            {{ $cta2 }}
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    {{-- Bottom UI: counter · progress tabs · arrows --}}
    <div data-hero-ui class="hero-ui relative z-20">
        <div class="mx-auto flex max-w-[1440px] items-end gap-6 px-5 pb-7 sm:px-10 lg:gap-10 lg:px-20 lg:pb-10 2xl:pr-20 max-2xl:md:pr-48 max-md:pr-24">
            {{-- previous arrow: bottom-left corner of the slide --}}
            <div class="hero-ctl-prev hidden shrink-0 md:flex"><button type="button" data-hero-prev class="hero-arrow" aria-label="{{ __('Sebelumnya') }}"><i class="fa-solid fa-arrow-left"></i></button></div>

            <div class="hero-ctl-count hidden shrink-0 items-baseline gap-1.5 font-semibold tabular-nums sm:flex">
                <span class="overflow-hidden text-4xl leading-none lg:text-5xl"><span data-hero-current class="hero-count-current">01</span></span>
                <span class="text-sm text-white/50">/ {{ str_pad($sliders->count(), 2, '0', STR_PAD_LEFT) }}</span>
            </div>

            {{-- min-width:0 lets long tab labels shrink (and truncate) instead of pushing the arrow out of the slide --}}
            <div class="flex flex-1 gap-2 sm:gap-4" style="min-width:0" role="tablist">
                @foreach ($sliders as $i => $slide)
                    @php $label = $slide->t('title') ?: $slide->t('cta_text') ?: 'Slide '.($i + 1); @endphp
                    <button type="button" data-hero-tab class="hero-tab group flex-1 text-left" style="min-width:0" aria-label="{{ $label }}">
                        <span class="label mb-3 hidden truncate text-xs font-medium tracking-wide lg:block">
                            <span class="mr-1.5 text-white/50">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>{{ $label }}
                        </span>
                        <span class="bar block"></span>
                    </button>
                @endforeach
            </div>

            {{-- next arrow: bottom-right corner --}}
            <div class="hero-ctl-next hidden shrink-0 md:flex"><button type="button" data-hero-next class="hero-arrow" aria-label="{{ __('Berikutnya') }}"><i class="fa-solid fa-arrow-right"></i></button></div>
        </div>
    </div>

</section>
<style>
    /* phones: same controls as desktop (prev / counter / next), tab bars on their own row above them */
    @media (max-width: 767px) {
        .hero-ui > div { flex-wrap: wrap; align-items: center; gap: .75rem; padding-bottom: 1rem; }
        .hero-ui [role="tablist"] { order: -1; flex: 1 1 100%; }
        .hero-ui .hero-ctl-prev, .hero-ui .hero-ctl-next, .hero-ui .hero-ctl-count { display: flex; }
        .hero-ui .hero-arrow { width: 2.5rem; height: 2.5rem; font-size: .8rem; }
        .hero-ui .hero-ctl-count > span:first-child { font-size: 1.35rem; }
    }
</style>

@if ($sliders->contains(fn ($s) => filled($s->video_path)))
    {{-- Video player opened by the play button on a slide. Styles/script are inline because the deployed CSS/JS build is not rebuilt. --}}
    <div class="mgi-video-modal" data-video-modal hidden role="dialog" aria-modal="true" aria-label="{{ __('Putar video') }}">
        <button type="button" class="mgi-video-x" data-video-close aria-label="{{ __('Tutup') }}"><i class="fa-solid fa-xmark"></i></button>
        <button type="button" class="mgi-video-sound" data-video-sound><i class="fa-solid fa-volume-xmark"></i><span>{{ __('Ketuk untuk suara') }}</span></button>
        <div class="mgi-video-stage" data-video-stage>
            <video data-video-el controls playsinline controlsList="nodownload" preload="none" muted></video>
        </div>
    </div>
    <style>
        .hero-play { position: absolute; left: 50%; top: 73%; z-index: 15; display: flex; flex-direction: column; align-items: center; gap: .75rem;
            transform: translate(-50%, -50%); color: #fff; cursor: pointer; }
        .hero-play-icon { display: grid; place-items: center; width: 5.25rem; height: 5.25rem; border-radius: 999px; font-size: 1.6rem; padding-left: .25rem;
            background-image: linear-gradient(100deg, #f0953f 0%, #e0457b 55%, #b4245a 100%); box-shadow: 0 0 0 .6rem rgb(255 255 255 / .14), 0 12px 40px rgb(0 0 0 / .5);
            transition: transform .3s, box-shadow .3s; animation: hero-play-pulse 2.4s ease-out infinite; }
        .hero-play:hover .hero-play-icon { transform: scale(1.08); }
        .hero-play-label { padding: .3rem .9rem; border-radius: 999px; font-size: .78rem; font-weight: 600; letter-spacing: .08em; text-transform: uppercase; background: rgb(0 0 0 / .45); backdrop-filter: blur(6px); }
        @keyframes hero-play-pulse { 0% { box-shadow: 0 0 0 0 rgb(255 255 255 / .35), 0 12px 40px rgb(0 0 0 / .5); } 100% { box-shadow: 0 0 0 1.4rem rgb(255 255 255 / 0), 0 12px 40px rgb(0 0 0 / .5); } }
        @media (max-width: 767px) { .hero-play { top: 74%; } .hero-play-icon { width: 4rem; height: 4rem; font-size: 1.2rem; } .hero-play-label { font-size: .68rem; } }
        .mgi-video-modal { position: fixed; inset: 0; z-index: 90; display: flex; align-items: center; justify-content: center; background: rgb(6 8 14 / .94); }
        .mgi-video-modal[hidden] { display: none; }
        .mgi-video-stage { display: grid; place-items: center; width: 100%; padding: 1rem; }
        .mgi-video-stage video { width: min(96vw, 1700px); height: auto; max-height: 84vh; border-radius: .75rem; background: #000; box-shadow: 0 10px 50px rgb(0 0 0 / .6); }
        .mgi-video-x { position: absolute; top: 1rem; right: 1rem; z-index: 2; display: grid; place-items: center; width: 2.75rem; height: 2.75rem; border-radius: 999px; color: #fff; background: rgb(255 255 255 / .16); cursor: pointer; }
        .mgi-video-x:hover { background: rgb(255 255 255 / .3); }
        .mgi-video-sound { position: absolute; top: 1rem; left: 1rem; z-index: 2; display: flex; align-items: center; gap: .6rem; height: 2.75rem; padding: 0 1.1rem; border-radius: 999px; color: #fff; font-size: .85rem; font-weight: 600;
            background-image: linear-gradient(100deg, #f0953f 0%, #e0457b 55%, #b4245a 100%); box-shadow: 0 6px 24px rgb(0 0 0 / .45); cursor: pointer; }
        .mgi-video-sound[hidden] { display: none; }
    </style>
    <script>
        (function () {
            var modal = document.querySelector('[data-video-modal]'), hero = document.querySelector('[data-hero]');
            if (!modal || !hero) return;
            var video = modal.querySelector('[data-video-el]'), track = hero.querySelector('[data-hero-track]'), isOpen = false;

            // keep the hero from auto-advancing while the video is open: reuse its own hover pause
            function holdHero(on) {
                isOpen = on;
                if (track) track.dispatchEvent(new MouseEvent(on ? 'mouseenter' : 'mouseleave'));
            }
            if (track) track.addEventListener('mouseleave', function (e) { if (isOpen) e.stopImmediatePropagation(); }, true);

            // the video always starts silent; sound is switched on by the visitor
            var soundBtn = modal.querySelector('[data-video-sound]');
            function syncSound() { soundBtn.hidden = !(video.muted || video.volume === 0); }
            soundBtn.addEventListener('click', function () { video.muted = false; if (video.volume === 0) video.volume = 1; syncSound(); });
            video.addEventListener('volumechange', syncSound);

            function open(btn) {
                video.poster = btn.dataset.poster || '';
                video.muted = true;
                syncSound();
                video.src = btn.dataset.heroVideo;
                modal.hidden = false;
                document.documentElement.style.overflow = 'hidden';
                holdHero(true);
                var p = video.play();
                if (p && p.catch) p.catch(function () {});
            }
            function close() {
                video.pause();
                video.removeAttribute('src');
                video.load();
                modal.hidden = true;
                document.documentElement.style.overflow = '';
                holdHero(false);
            }
            hero.querySelectorAll('[data-hero-video]').forEach(function (btn) {
                btn.addEventListener('click', function () { open(btn); });
            });
            modal.querySelector('[data-video-close]').addEventListener('click', close);
            modal.querySelector('[data-video-stage]').addEventListener('click', function (e) { if (e.target === this) close(); });
            document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && !modal.hidden) close(); });
        })();
    </script>
@endif
@endif
