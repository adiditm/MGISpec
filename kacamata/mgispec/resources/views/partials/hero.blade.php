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
            <div class="hero-slide {{ $i === 0 ? 'is-active' : '' }}" data-hero-slide data-effect="{{ $slide->effect ? Slider::effectKey($slide->effect) : $defaultEffect }}"
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
            <div class="hidden shrink-0 items-baseline gap-1.5 font-semibold tabular-nums sm:flex">
                <span class="overflow-hidden text-4xl leading-none lg:text-5xl"><span data-hero-current class="hero-count-current">01</span></span>
                <span class="text-sm text-white/50">/ {{ str_pad($sliders->count(), 2, '0', STR_PAD_LEFT) }}</span>
            </div>

            <div class="flex flex-1 gap-2 sm:gap-4" role="tablist">
                @foreach ($sliders as $i => $slide)
                    @php $label = $slide->t('title') ?: $slide->t('cta_text') ?: 'Slide '.($i + 1); @endphp
                    <button type="button" data-hero-tab class="hero-tab group flex-1 text-left" aria-label="{{ $label }}">
                        <span class="label mb-3 hidden truncate text-xs font-medium tracking-wide lg:block">
                            <span class="mr-1.5 text-white/50">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>{{ $label }}
                        </span>
                        <span class="bar block"></span>
                    </button>
                @endforeach
            </div>

            <div class="hidden shrink-0 gap-3 md:flex">
                <button type="button" data-hero-prev class="hero-arrow" aria-label="{{ __('Sebelumnya') }}"><i class="fa-solid fa-arrow-left"></i></button>
                <button type="button" data-hero-next class="hero-arrow" aria-label="{{ __('Berikutnya') }}"><i class="fa-solid fa-arrow-right"></i></button>
            </div>
        </div>
    </div>

</section>
@endif
