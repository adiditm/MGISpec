@php
    $hvEnabled = \App\Models\Setting::get('home_video_enabled', true);
    $hvEnabled = $hvEnabled !== false && $hvEnabled !== '0' && $hvEnabled !== 0;
    $hvVideo = \App\Support\Media::url(\App\Models\Setting::get('home_video_path'));
    $hvPoster = \App\Support\Media::url(\App\Models\Setting::get('home_video_poster'));
@endphp

@if ($hvEnabled && $hvVideo)
    <section class="mgi-hv relative bg-white" aria-label="{{ __('Putar video') }}" data-home-video>
        <div>
            <div class="mgi-hv__frame relative overflow-hidden bg-black" data-animate>
                <video class="mgi-hv__video block h-full w-full object-cover" data-hv-video muted loop playsinline preload="none" disablePictureInPicture controlsList="nodownload noremoteplayback"
                       @if ($hvPoster) poster="{{ $hvPoster }}" @endif data-src="{{ $hvVideo }}"></video>

                {{-- shown when the browser blocks autoplay (or the visitor prefers reduced motion) --}}
                <button type="button" class="mgi-hv__play" data-hv-play aria-label="{{ __('Putar video') }}" hidden>
                    <i class="fa-solid fa-play"></i>
                </button>

                {{-- "tap for sound" pill, visible while the video plays muted --}}
                <button type="button" class="mgi-hv__pill" data-hv-pill hidden>
                    <i class="fa-solid fa-volume-high"></i> <span>{{ __('Ketuk untuk suara') }}</span>
                </button>

                <div class="mgi-hv__ctl">
                    <button type="button" data-hv-sound aria-label="{{ __('Suara') }}" title="{{ __('Suara') }}"><i class="fa-solid fa-volume-xmark"></i></button>
                    <button type="button" data-hv-full aria-label="{{ __('Layar penuh') }}" title="{{ __('Layar penuh') }}"><i class="fa-solid fa-expand"></i></button>
                </div>
            </div>
        </div>
    </section>

    <style>
        /* spacing is inline on purpose: the server's Tailwind build does not know every utility class */
        .mgi-hv { padding-top: clamp(1.25rem, 2.5vw, 2.25rem); }
        .mgi-hv__frame { position: relative; overflow: hidden; background: #000; width: 100%; aspect-ratio: 2430 / 540; cursor: pointer; }
        .mgi-hv__video { display: block; width: 100%; height: 100%; object-fit: cover; }
        .mgi-hv__frame:fullscreen { aspect-ratio: auto; border-radius: 0; }
        .mgi-hv__frame:fullscreen .mgi-hv__video { object-fit: contain; }
        .mgi-hv__frame:-webkit-full-screen { aspect-ratio: auto; border-radius: 0; }
        .mgi-hv__frame:-webkit-full-screen .mgi-hv__video { object-fit: contain; }
        .mgi-hv__play {
            position: absolute; inset: 0; margin: auto; width: 4.25rem; height: 4.25rem; border-radius: 9999px; border: 0;
            display: grid; place-items: center; font-size: 1.4rem; color: #fff; cursor: pointer;
            background: rgba(0, 0, 0, .55); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px);
            box-shadow: 0 0 0 1px rgba(255, 255, 255, .35) inset; transition: transform .2s, background .2s;
        }
        .mgi-hv__play[hidden], .mgi-hv__pill[hidden] { display: none; }
        .mgi-hv__play i { margin-left: .2rem; }
        .mgi-hv__play:hover { transform: scale(1.06); background: rgba(0, 0, 0, .7); }
        .mgi-hv__pill {
            position: absolute; left: .75rem; bottom: .75rem; display: inline-flex; align-items: center; gap: .45rem;
            border: 0; border-radius: 9999px; padding: .45rem .9rem; font-size: .8rem; font-weight: 600; color: #fff; cursor: pointer;
            background: rgba(0, 0, 0, .6); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px);
        }
        .mgi-hv__ctl { position: absolute; right: .75rem; bottom: .75rem; display: flex; gap: .5rem; }
        .mgi-hv__ctl button {
            width: 2.25rem; height: 2.25rem; border: 0; border-radius: 9999px; display: grid; place-items: center; font-size: .9rem;
            color: #fff; cursor: pointer; background: rgba(0, 0, 0, .55); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px);
            transition: background .2s;
        }
        .mgi-hv__ctl button:hover { background: rgba(0, 0, 0, .75); }
        @media (max-width: 639px) {
            .mgi-hv__pill { left: .5rem; bottom: .5rem; padding: .3rem .65rem; font-size: .7rem; }
            .mgi-hv__ctl { right: .5rem; bottom: .5rem; gap: .35rem; }
            .mgi-hv__ctl button { width: 1.9rem; height: 1.9rem; font-size: .75rem; }
            .mgi-hv__play { width: 3rem; height: 3rem; font-size: 1rem; }
        }
    </style>

    <script>
        (function () {
            var root = document.querySelector('[data-home-video]');
            if (!root) return;
            var frame = root.querySelector('.mgi-hv__frame');
            var video = root.querySelector('[data-hv-video]');
            var playBtn = root.querySelector('[data-hv-play]');
            var pill = root.querySelector('[data-hv-pill]');
            var soundBtn = root.querySelector('[data-hv-sound]');
            var fullBtn = root.querySelector('[data-hv-full]');
            var reduced = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;
            var wanted = false;   // the visitor is looking at the block
            var started = false;  // the visitor (or autoplay) has started it at least once

            function load() {
                if (!video.getAttribute('src')) { video.src = video.getAttribute('data-src'); }
            }
            function paint() {
                var playing = !video.paused && !video.ended;
                playBtn.hidden = playing;
                pill.hidden = !(playing && video.muted);
                soundBtn.firstElementChild.className = 'fa-solid ' + (video.muted ? 'fa-volume-xmark' : 'fa-volume-high');
            }
            function play() {
                load();
                var p = video.play();
                if (p && p.catch) { p.catch(function () { paint(); }); }
                started = true;
            }
            function toggleSound() {
                load();
                video.muted = !video.muted;
                if (video.paused) play();
                paint();
            }

            video.addEventListener('play', paint);
            video.addEventListener('pause', paint);
            video.addEventListener('volumechange', paint);

            playBtn.addEventListener('click', function (e) { e.stopPropagation(); video.muted = false; play(); });
            frame.addEventListener('click', function (e) {
                if (e.target.closest('.mgi-hv__ctl, .mgi-hv__play')) return;
                if (video.paused) { play(); } else { toggleSound(); }
            });
            soundBtn.addEventListener('click', function (e) { e.stopPropagation(); toggleSound(); });
            fullBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                load();
                var doc = document;
                if (doc.fullscreenElement || doc.webkitFullscreenElement) {
                    (doc.exitFullscreen || doc.webkitExitFullscreen).call(doc);
                } else if (frame.requestFullscreen) {
                    frame.requestFullscreen();
                } else if (frame.webkitRequestFullscreen) {
                    frame.webkitRequestFullscreen();
                } else if (video.webkitEnterFullscreen) { // iPhone Safari only fullscreens the <video> itself
                    video.webkitEnterFullscreen();
                }
                if (video.paused) play();
            });
            function onFs() {
                var on = !!(document.fullscreenElement || document.webkitFullscreenElement);
                video.controls = on;
            }
            document.addEventListener('fullscreenchange', onFs);
            document.addEventListener('webkitfullscreenchange', onFs);

            // start only when visible (the file is ~37 MB), stop when it scrolls away
            if ('IntersectionObserver' in window) {
                new IntersectionObserver(function (entries) {
                    entries.forEach(function (en) {
                        if (en.isIntersecting && en.intersectionRatio >= 0.35) {
                            wanted = true;
                            if (!reduced || started) { play(); } else { paint(); }
                        } else if (!en.isIntersecting) {
                            wanted = false;
                            if (!video.paused) video.pause();
                        }
                    });
                }, { threshold: [0, 0.35, 0.6] }).observe(frame);
            } else {
                paint();
            }
            paint();
        })();
    </script>
@endif
