{{-- Certificate slider: small cards, click = large view with a button that downloads the PDF --}}
@if ($certificates->isNotEmpty())
    <section class="mgi-cert bg-white pb-14 lg:pb-20" aria-label="{{ __('Sertifikat') }}">
        <div class="mx-auto max-w-[1440px] px-4 sm:px-6 lg:px-10" data-carousel data-autoplay="3500" data-loop="{{ $certificates->count() > 7 ? 'true' : 'false' }}"
             data-breakpoints='{"480":{"slidesPerView":3.2},"768":{"slidesPerView":4.2,"spaceBetween":16},"1024":{"slidesPerView":5.2,"spaceBetween":18},"1280":{"slidesPerView":6,"spaceBetween":20}}'>
            <div class="mb-5 flex items-center justify-between gap-6 border-t border-gray-100 pt-10">
                <h2 class="text-xl font-extrabold tracking-tight text-gray-900 lg:text-2xl">{{ __('Sertifikat') }}</h2>
                <div class="flex gap-2">
                    <button type="button" data-prev class="carousel-nav" aria-label="{{ __('Sebelumnya') }}"><i class="fa-solid fa-arrow-left text-sm"></i></button>
                    <button type="button" data-next class="carousel-nav" aria-label="{{ __('Berikutnya') }}"><i class="fa-solid fa-arrow-right text-sm"></i></button>
                </div>
            </div>
            <div class="swiper">
                <div class="swiper-wrapper">
                    @foreach ($certificates as $cert)
                        <div class="swiper-slide !h-auto">
                            <button type="button" class="mgi-cert-card" data-cert-open
                                    data-img="{{ $cert->imageUrl() }}" data-pdf="{{ $cert->pdfUrl() }}" data-title="{{ $cert->title }}"
                                    aria-label="{{ $cert->title ?: __('Sertifikat') }}">
                                <img src="{{ $cert->imageUrl() }}" alt="{{ $cert->title ?: __('Sertifikat') }}" loading="lazy">
                                @if ($cert->title)<span>{{ $cert->title }}</span>@endif
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <div class="mgi-cert-modal" data-cert-modal hidden role="dialog" aria-modal="true" aria-label="{{ __('Sertifikat') }}">
        <div class="mgi-cert-bar">
            <strong data-cert-title></strong>
            <span>
                <a data-cert-download href="#" download class="mgi-cert-dl"><i class="fa-solid fa-download"></i> {{ __('Unduh PDF') }}</a>
                <button type="button" data-cert-close class="mgi-cert-x" aria-label="{{ __('Tutup') }}"><i class="fa-solid fa-xmark"></i></button>
            </span>
        </div>
        <div class="mgi-cert-stage" data-cert-stage><img data-cert-img src="" alt=""></div>
    </div>

    <style>
        .mgi-cert-card { display: flex; flex-direction: column; width: 100%; height: 100%; padding: .6rem; border-radius: 1rem; background: #fff; text-align: center; cursor: zoom-in;
            box-shadow: 0 0 0 1px rgb(0 0 0 / .08); transition: box-shadow .25s, transform .25s; }
        .mgi-cert-card:hover { box-shadow: 0 0 0 1px rgb(0 0 0 / .2), 0 8px 20px rgb(0 0 0 / .08); transform: translateY(-2px); }
        .mgi-cert-card img { width: 100%; aspect-ratio: 3 / 4; object-fit: contain; border-radius: .6rem; background: #f8f9fb; }
        .mgi-cert-card span { margin-top: .5rem; font-size: .72rem; font-weight: 600; color: #4b5563; line-height: 1.2; }
        .mgi-cert-modal { position: fixed; inset: 0; z-index: 80; display: flex; flex-direction: column; background: rgb(10 12 18 / .92); }
        .mgi-cert-modal[hidden] { display: none; }
        .mgi-cert-bar { display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: .75rem 1rem; color: #fff; }
        .mgi-cert-bar strong { min-width: 0; overflow: hidden; font-size: .95rem; text-overflow: ellipsis; white-space: nowrap; }
        .mgi-cert-bar span { display: flex; flex: none; align-items: center; gap: .6rem; }
        .mgi-cert-dl { display: inline-flex; align-items: center; gap: .5rem; padding: .55rem 1.1rem; border-radius: 999px; font-size: .85rem; font-weight: 600; color: #fff;
            background-image: linear-gradient(100deg, #f0953f 0%, #e0457b 55%, #b4245a 100%); }
        .mgi-cert-x { display: grid; place-items: center; width: 2.5rem; height: 2.5rem; border-radius: 999px; color: #fff; background: rgb(255 255 255 / .14); cursor: pointer; }
        .mgi-cert-x:hover { background: rgb(255 255 255 / .28); }
        .mgi-cert-stage { flex: 1; display: grid; place-items: center; min-height: 0; padding: .5rem 1rem 1.5rem; overflow: auto; }
        .mgi-cert-stage img { max-width: 100%; max-height: 100%; object-fit: contain; border-radius: .5rem; background: #fff; box-shadow: 0 10px 40px rgb(0 0 0 / .4); }
    </style>
    <script>
        (function () {
            var modal = document.querySelector('[data-cert-modal]');
            if (!modal) return;
            var img = modal.querySelector('[data-cert-img]'), title = modal.querySelector('[data-cert-title]'), dl = modal.querySelector('[data-cert-download]');
            function close() { modal.hidden = true; document.documentElement.style.overflow = ''; img.src = ''; }
            document.querySelectorAll('[data-cert-open]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    img.src = btn.dataset.img; img.alt = btn.dataset.title || '';
                    title.textContent = btn.dataset.title || '';
                    dl.href = btn.dataset.pdf;
                    dl.setAttribute('download', (btn.dataset.title || 'certificate').replace(/[^\w\- ]+/g, '').trim().replace(/\s+/g, '-') + '.pdf');
                    modal.hidden = false; document.documentElement.style.overflow = 'hidden';
                });
            });
            modal.querySelector('[data-cert-close]').addEventListener('click', close);
            modal.querySelector('[data-cert-stage]').addEventListener('click', function (e) { if (e.target !== img) close(); });
            document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && !modal.hidden) close(); });
        })();
    </script>
@endif
