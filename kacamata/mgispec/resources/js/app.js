import Swiper from 'swiper';
import { A11y, Autoplay, EffectCreative, EffectFade, FreeMode, Keyboard, Navigation, Pagination, Parallax, Thumbs } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/effect-fade';
import 'swiper/css/effect-creative';
import 'swiper/css/free-mode';
import 'swiper/css/pagination';

const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const pad = (n) => String(n).padStart(2, '0');

/* ───────────── Header: transparent over the hero, solid after scrolling ───────────── */
function initHeader() {
    const header = document.querySelector('[data-header]');
    if (!header) return;

    const overHero = header.dataset.overHero === 'true';
    const update = () => {
        header.dataset.transparent = String(overHero && window.scrollY < 40 && !document.body.classList.contains('menu-open'));
    };
    update();
    window.addEventListener('scroll', update, { passive: true });

    // language dropdown
    document.querySelectorAll('[data-dropdown]').forEach((wrap) => {
        const btn = wrap.querySelector('button');
        const menu = wrap.querySelector('[data-dropdown-menu]');
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const open = menu.classList.toggle('hidden') === false;
            btn.setAttribute('aria-expanded', String(open));
        });
        document.addEventListener('click', () => {
            menu.classList.add('hidden');
            btn.setAttribute('aria-expanded', 'false');
        });
    });

    // mobile drawer
    const drawer = document.querySelector('[data-drawer]');
    const toggle = (open) => {
        drawer.classList.toggle('translate-x-full', !open);
        document.querySelector('[data-drawer-backdrop]').classList.toggle('hidden', !open);
        document.body.classList.toggle('menu-open', open);
        document.body.style.overflow = open ? 'hidden' : '';
        update();
    };
    document.querySelectorAll('[data-drawer-open]').forEach((b) => b.addEventListener('click', () => toggle(true)));
    document.querySelectorAll('[data-drawer-close]').forEach((b) => b.addEventListener('click', () => toggle(false)));
}

/* ───────────── Hero slider ───────────── */
const EFFECTS = {
    fade: { effect: 'fade', fadeEffect: { crossFade: true }, speed: 1400 },
    curtain: { effect: 'fade', fadeEffect: { crossFade: false }, speed: 1500 },
    parallax: { effect: 'slide', parallax: true, speed: 1500 },
    depth: {
        effect: 'creative',
        speed: 1400,
        creativeEffect: {
            prev: { shadow: true, translate: ['-25%', 0, -400], opacity: 0.6 },
            next: { translate: ['100%', 0, 0] },
        },
    },
};

function initHero() {
    const hero = document.querySelector('[data-hero]');
    if (!hero) return;

    const el = hero.querySelector('.swiper');
    const slideCount = el.querySelectorAll('.swiper-slide').length;
    const delay = (parseFloat(hero.dataset.delay) || 6) * 1000;
    const effect = EFFECTS[hero.dataset.effect] ?? EFFECTS.fade;
    const tabs = [...hero.querySelectorAll('[data-hero-tab]')];
    const current = hero.querySelector('[data-hero-current]');

    hero.style.setProperty('--hero-delay', `${delay / 1000}s`);
    if (hero.dataset.effect === 'curtain') hero.classList.add('fx-curtain');

    if (slideCount < 2) {
        el.querySelector('.swiper-slide')?.classList.add('swiper-slide-active');
        hero.querySelectorAll('[data-hero-ui]').forEach((n) => n.remove());
        return;
    }

    const syncTabs = (swiper) => {
        tabs.forEach((tab, i) => {
            tab.classList.toggle('is-active', i === swiper.realIndex);
            tab.classList.toggle('is-done', i < swiper.realIndex);
            if (i !== swiper.realIndex) tab.style.setProperty('--p', i < swiper.realIndex ? 1 : 0);
            tab.setAttribute('aria-current', i === swiper.realIndex ? 'true' : 'false');
        });
        if (current) {
            current.textContent = pad(swiper.realIndex + 1);
            current.classList.remove('flip');
            void current.offsetWidth;
            current.classList.add('flip');
        }
    };

    const swiper = new Swiper(el, {
        modules: [Autoplay, EffectFade, EffectCreative, Parallax, Keyboard, A11y, Navigation],
        ...effect,
        loop: true,
        grabCursor: true,
        keyboard: { enabled: true },
        a11y: { enabled: true },
        navigation: { prevEl: hero.querySelector('[data-hero-prev]'), nextEl: hero.querySelector('[data-hero-next]') },
        autoplay: reduceMotion ? false : { delay, disableOnInteraction: false, pauseOnMouseEnter: true },
        on: {
            init: syncTabs,
            slideChange: syncTabs,
            autoplayTimeLeft(s, _time, progress) {
                tabs[s.realIndex]?.style.setProperty('--p', (1 - progress).toFixed(4));
            },
        },
    });

    tabs.forEach((tab, i) => tab.addEventListener('click', () => swiper.slideToLoop(i)));

    // stop the autoplay timer while the hero is off-screen, resume when it returns
    new IntersectionObserver(([entry]) => {
        if (!swiper.autoplay || reduceMotion) return;
        entry.isIntersecting ? swiper.autoplay.resume() : swiper.autoplay.pause();
    }, { threshold: 0.2 }).observe(hero);
}

/* ───────────── "Slider kecil": promo cards & product thumbnails ───────────── */
function initCarousels() {
    document.querySelectorAll('[data-carousel]').forEach((wrap) => {
        const el = wrap.querySelector('.swiper');
        const auto = wrap.dataset.autoplay;

        new Swiper(el, {
            modules: [Navigation, Autoplay, FreeMode, Pagination, A11y],
            slidesPerView: 1.25,
            spaceBetween: 16,
            grabCursor: true,
            speed: 700,
            loop: wrap.dataset.loop === 'true',
            freeMode: wrap.dataset.free === 'true' ? { enabled: true, sticky: false, momentumRatio: 0.6 } : false,
            navigation: { prevEl: wrap.querySelector('[data-prev]'), nextEl: wrap.querySelector('[data-next]') },
            pagination: wrap.querySelector('[data-progress]') ? { el: wrap.querySelector('[data-progress]'), type: 'progressbar' } : false,
            autoplay: auto && !reduceMotion ? { delay: Number(auto), disableOnInteraction: false, pauseOnMouseEnter: true } : false,
            breakpoints: JSON.parse(wrap.dataset.breakpoints || '{}'),
        });
    });
}

/* ───────────── Product gallery with thumbs + hover zoom ───────────── */
function initGallery() {
    const gallery = document.querySelector('[data-gallery]');
    if (!gallery) return;

    const thumbsEl = gallery.querySelector('.gallery-thumbs');
    const thumbs = thumbsEl && new Swiper(thumbsEl, {
        modules: [FreeMode],
        slidesPerView: 4.3,
        spaceBetween: 12,
        freeMode: true,
        watchSlidesProgress: true,
        breakpoints: { 768: { slidesPerView: 5 } },
    });

    new Swiper(gallery.querySelector('.gallery-main'), {
        modules: [Thumbs, Navigation, EffectFade, Keyboard, A11y],
        effect: 'fade',
        fadeEffect: { crossFade: true },
        speed: 500,
        keyboard: { enabled: true },
        navigation: { prevEl: gallery.querySelector('[data-prev]'), nextEl: gallery.querySelector('[data-next]') },
        ...(thumbs ? { thumbs: { swiper: thumbs } } : {}),
    });

    if (!window.matchMedia('(hover: hover)').matches) return;
    gallery.querySelectorAll('.zoomable').forEach((box) => {
        const img = box.querySelector('img');
        box.addEventListener('mousemove', (e) => {
            const r = box.getBoundingClientRect();
            img.style.transformOrigin = `${((e.clientX - r.left) / r.width) * 100}% ${((e.clientY - r.top) / r.height) * 100}%`;
            img.style.transform = 'scale(1.9)';
        });
        box.addEventListener('mouseleave', () => (img.style.transform = ''));
    });
}

/* ───────────── Tabs (product detail) ───────────── */
function initTabs() {
    document.querySelectorAll('[data-tabs]').forEach((wrap) => {
        const buttons = wrap.querySelectorAll('[data-tab]');
        buttons.forEach((btn) =>
            btn.addEventListener('click', () => {
                buttons.forEach((b) => b.setAttribute('aria-selected', String(b === btn)));
                wrap.querySelectorAll('[data-panel]').forEach((p) => p.classList.toggle('hidden', p.dataset.panel !== btn.dataset.tab));
            }),
        );
        buttons[0]?.click();
    });
}

/* ───────────── Fade-up sections on scroll ───────────── */
function initReveal() {
    const items = document.querySelectorAll('[data-animate]');
    if (!('IntersectionObserver' in window)) return items.forEach((i) => i.classList.add('is-in'));

    const io = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            entry.target.classList.add('is-in');
            io.unobserve(entry.target);
        });
    }, { rootMargin: '0px 0px -8% 0px' });
    items.forEach((i) => io.observe(i));
}

document.addEventListener('DOMContentLoaded', () => {
    initHeader();
    initHero();
    initCarousels();
    initGallery();
    initTabs();
    initReveal();
});
