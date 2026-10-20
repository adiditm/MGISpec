import Swiper from 'swiper';
import { A11y, Autoplay, EffectFade, FreeMode, Keyboard, Navigation, Pagination, Thumbs } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/effect-fade';
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

/* ───────────── Hero slider ─────────────
   Each slide brings its own entrance effect (data-effect, chosen per banner in the CMS), so this is a
   small purpose-built slider instead of Swiper, whose effect is fixed for the whole carousel. */
const FX_MS = 1100;

function initHero() {
    const hero = document.querySelector('[data-hero]');
    if (!hero) return;

    const track = hero.querySelector('[data-hero-track]');
    const slides = [...hero.querySelectorAll('[data-hero-slide]')];
    const tabs = [...hero.querySelectorAll('[data-hero-tab]')];
    const current = hero.querySelector('[data-hero-current]');
    const delay = (parseFloat(hero.dataset.delay) || 6) * 1000;

    hero.style.setProperty('--hero-delay', `${delay / 1000}s`);

    if (slides.length < 2) {
        hero.querySelectorAll('[data-hero-ui]').forEach((n) => n.remove());
        return;
    }

    let index = 0;
    let elapsed = 0;
    let last = performance.now();
    let hovered = false;
    let visible = true;
    let busy = null;

    const syncTabs = () => {
        tabs.forEach((tab, i) => {
            tab.classList.toggle('is-active', i === index);
            tab.classList.toggle('is-done', i < index);
            if (i !== index) tab.style.setProperty('--p', i < index ? 1 : 0);
            tab.setAttribute('aria-current', i === index ? 'true' : 'false');
        });
        if (current) {
            current.textContent = pad(index + 1);
            current.classList.remove('flip');
            void current.offsetWidth;
            current.classList.add('flip');
        }
    };

    const finish = () => {
        clearTimeout(busy);
        busy = null;
        slides.forEach((s) => s.classList.remove('is-entering', 'is-leaving'));
        delete track.dataset.fx;
    };

    const go = (to, dir) => {
        to = (to + slides.length) % slides.length;
        if (to === index) return;
        if (busy) finish();

        const from = slides[index];
        const next = slides[to];
        dir ??= to > index ? 1 : -1;

        track.dataset.fx = next.dataset.effect;
        track.dataset.dir = String(dir);
        track.style.setProperty('--dir', dir);

        from.classList.remove('is-active');
        from.classList.add('is-leaving');
        next.classList.add('is-active', 'is-entering');
        // restart the Ken Burns zoom on the image when the slide comes back
        next.querySelectorAll('.hero-img').forEach((img) => {
            img.style.animation = 'none';
            void img.offsetWidth;
            img.style.animation = '';
        });

        index = to;
        elapsed = 0;
        syncTabs();
        busy = setTimeout(finish, reduceMotion ? 0 : FX_MS + 50);
    };

    // autoplay with a progress bar on the active tab; paused on hover and while off-screen
    const tick = () => {
        const now = performance.now();
        if (!hovered && visible && !reduceMotion) {
            elapsed += Math.min(now - last, 250);
            tabs[index]?.style.setProperty('--p', Math.min(elapsed / delay, 1).toFixed(4));
            if (elapsed >= delay) go(index + 1, 1);
        }
        last = now;
    };

    tabs.forEach((tab, i) => tab.addEventListener('click', () => go(i)));
    hero.querySelector('[data-hero-prev]')?.addEventListener('click', () => go(index - 1, -1));
    hero.querySelector('[data-hero-next]')?.addEventListener('click', () => go(index + 1, 1));
    track.addEventListener('mouseenter', () => (hovered = true));
    track.addEventListener('mouseleave', () => (hovered = false));
    hero.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') go(index - 1, -1);
        if (e.key === 'ArrowRight') go(index + 1, 1);
    });

    // swipe on touch screens
    let startX = null;
    track.addEventListener('pointerdown', (e) => (startX = e.clientX));
    track.addEventListener('pointerup', (e) => {
        if (startX === null) return;
        const dx = e.clientX - startX;
        startX = null;
        if (Math.abs(dx) > 40) dx < 0 ? go(index + 1, 1) : go(index - 1, -1);
    });

    new IntersectionObserver(([entry]) => (visible = entry.isIntersecting), { threshold: 0.2 }).observe(hero);

    syncTabs();
    setInterval(tick, 50);
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
