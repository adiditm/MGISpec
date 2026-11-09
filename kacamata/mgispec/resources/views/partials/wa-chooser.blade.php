{{--
    WhatsApp chooser popup. Any link with data-wa-choose (and the message in data-wa-text) opens a small dialog listing
    Indonesia's hotlines, each opening its own WhatsApp chat. Without JavaScript the link still opens the default chat.
    Self-contained (inline CSS + JS) so it does not depend on the compiled Tailwind build.
--}}
@php
    $waChoices = collect(\App\Support\WhatsAppContacts::indonesia())->map(fn ($c) => [
        'name' => \App\Support\I18n::pick($c['label']),
        'phone' => $c['phone'],
        'number' => $c['whatsapp'],
    ])->values();
@endphp
<style>
    .mgi-wa-overlay { position: fixed; inset: 0; z-index: 70; display: none; align-items: center; justify-content: center; padding: 1rem; background: rgba(17, 24, 39, .55); }
    .mgi-wa-overlay.is-open { display: flex; }
    .mgi-wa-box { position: relative; width: 100%; max-width: 24rem; border-radius: 1.25rem; background: #fff; padding: 1.5rem 1.25rem 1.25rem; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, .35); }
    .mgi-wa-title { margin: 0 2rem 0 0; font-size: 1.05rem; font-weight: 700; color: #111827; }
    .mgi-wa-sub { margin: .25rem 0 1rem; font-size: .8rem; color: #6b7280; }
    .mgi-wa-close { position: absolute; top: .75rem; right: .75rem; width: 2rem; height: 2rem; border: 0; border-radius: 9999px; background: transparent; color: #6b7280; font-size: 1rem; cursor: pointer; }
    .mgi-wa-close:hover { background: #f3f4f6; color: #111827; }
    .mgi-wa-opt { display: flex; align-items: center; gap: .75rem; margin-top: .6rem; padding: .8rem 1rem; border-radius: .9rem; background: #f0fdf4; color: #111827; text-decoration: none; box-shadow: inset 0 0 0 1px #bbf7d0; transition: background .15s, transform .15s; }
    .mgi-wa-opt:hover { background: #dcfce7; transform: translateY(-1px); }
    .mgi-wa-ico { display: grid; flex: none; place-items: center; width: 2.5rem; height: 2.5rem; border-radius: 9999px; background: #25D366; color: #fff; font-size: 1.4rem; }
    .mgi-wa-name { display: block; font-size: .9rem; font-weight: 600; }
    .mgi-wa-phone { display: block; font-size: .78rem; color: #4b5563; }
</style>
<div class="mgi-wa-overlay" id="mgiWaChooser" role="dialog" aria-modal="true" aria-labelledby="mgiWaTitle" hidden>
    <div class="mgi-wa-box">
        <button type="button" class="mgi-wa-close" data-wa-close aria-label="{{ __('Tutup') }}"><i class="fa-solid fa-xmark"></i></button>
        <h2 class="mgi-wa-title" id="mgiWaTitle">{{ __('Pilih kontak WhatsApp') }}</h2>
        <p class="mgi-wa-sub">{{ __('Mau menghubungi yang mana?') }}</p>
        @foreach ($waChoices as $choice)
            <a class="mgi-wa-opt" href="https://wa.me/{{ $choice['number'] }}" data-wa-number="{{ $choice['number'] }}" target="_blank" rel="noopener">
                <span class="mgi-wa-ico"><i class="fa-brands fa-whatsapp"></i></span>
                <span><span class="mgi-wa-name">{{ $choice['name'] }}</span><span class="mgi-wa-phone">{{ $choice['phone'] }}</span></span>
            </a>
        @endforeach
    </div>
</div>
<script>
    (function () {
        var box = document.getElementById('mgiWaChooser');
        if (!box) return;
        var lastTrigger = null;

        function open(text, trigger) {
            lastTrigger = trigger || null;
            box.querySelectorAll('[data-wa-number]').forEach(function (a) {
                a.href = 'https://wa.me/' + a.getAttribute('data-wa-number') + (text ? '?text=' + encodeURIComponent(text) : '');
            });
            box.hidden = false;
            box.classList.add('is-open');
            var first = box.querySelector('.mgi-wa-opt');
            if (first) first.focus();
        }

        function close() {
            box.classList.remove('is-open');
            box.hidden = true;
            if (lastTrigger) lastTrigger.focus();
        }

        document.addEventListener('click', function (e) {
            var trigger = e.target.closest('[data-wa-choose]');
            if (trigger) {
                e.preventDefault();
                open(trigger.getAttribute('data-wa-text') || '', trigger);
                return;
            }
            if (e.target === box || e.target.closest('[data-wa-close]')) close();
            else if (e.target.closest('.mgi-wa-opt')) setTimeout(close, 150);
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && box.classList.contains('is-open')) close();
        });
    })();
</script>
