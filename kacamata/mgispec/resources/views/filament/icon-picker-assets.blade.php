{{-- Font Awesome + icon list + styles for the CMS icon picker (same Font Awesome build as the website). --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
<script>
    window.MGI_ICONS = @json(\App\Support\IconLibrary::all());
    window.MGI_ICON_GROUPS = @json(\App\Support\IconLibrary::groups());
</script>
<style>
    /* Same red-orange tile the icons have on the website */
    .mgi-ip-tile { display: grid; place-items: center; width: 3rem; height: 3rem; flex: none; border-radius: .85rem; color: #fff; font-size: 1.1rem;
        background-image: linear-gradient(100deg, #f0953f 0%, #e0457b 55%, #b4245a 100%); }
    .mgi-ip-tile.is-empty { background: rgb(var(--gray-200)); color: rgb(var(--gray-500)); }
    .mgi-ip-bar { display: flex; align-items: center; gap: .85rem; }
    .mgi-ip-name { min-width: 0; font-size: .85rem; line-height: 1.25; color: rgb(var(--gray-700)); }
    .mgi-ip-name small { display: block; color: rgb(var(--gray-500)); font-size: .72rem; word-break: break-all; }
    .mgi-ip-btn { padding: .45rem .9rem; border-radius: .6rem; font-size: .85rem; font-weight: 600; cursor: pointer;
        color: rgb(var(--gray-800)); background: #fff; box-shadow: 0 0 0 1px rgb(var(--gray-300)); }
    .mgi-ip-btn:hover { box-shadow: 0 0 0 1px rgb(var(--gray-500)); }
    .mgi-ip-panel { margin-top: .75rem; padding: .85rem; border-radius: .85rem; background: rgb(var(--gray-50)); box-shadow: 0 0 0 1px rgb(var(--gray-200)); }
    .mgi-ip-search { width: 100%; padding: .55rem .8rem; border-radius: .6rem; font-size: .9rem; color: inherit; background: #fff; box-shadow: 0 0 0 1px rgb(var(--gray-300)); border: 0; outline: 0; }
    .mgi-ip-search:focus { box-shadow: 0 0 0 2px rgb(var(--primary-600)); }
    .mgi-ip-chips { display: flex; flex-wrap: wrap; gap: .4rem; margin-top: .65rem; }
    .mgi-ip-chip { padding: .25rem .7rem; border-radius: 999px; font-size: .75rem; font-weight: 600; cursor: pointer; color: rgb(var(--gray-600)); background: #fff; box-shadow: 0 0 0 1px rgb(var(--gray-300)); }
    .mgi-ip-chip.is-on { color: #fff; background: rgb(var(--primary-600)); box-shadow: none; }
    .mgi-ip-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(4.6rem, 1fr)); gap: .5rem; max-height: 19rem; margin-top: .75rem; overflow-y: auto; padding: .25rem; }
    .mgi-ip-cell { display: flex; flex-direction: column; align-items: center; gap: .35rem; padding: .5rem .25rem; border-radius: .7rem; cursor: pointer; background: transparent; border: 0; }
    .mgi-ip-cell:hover { background: rgb(var(--gray-200)); }
    .mgi-ip-cell.is-on { box-shadow: 0 0 0 2px rgb(var(--primary-600)); background: #fff; }
    .mgi-ip-cell .mgi-ip-tile { width: 2.6rem; height: 2.6rem; font-size: .95rem; border-radius: .7rem; }
    .mgi-ip-cell span.l { max-width: 100%; overflow: hidden; font-size: .66rem; line-height: 1.15; text-align: center; text-overflow: ellipsis; white-space: nowrap; color: rgb(var(--gray-600)); }
    .mgi-ip-meta { margin-top: .5rem; font-size: .72rem; color: rgb(var(--gray-500)); }
    .mgi-ip-manual { margin-top: .6rem; font-size: .75rem; color: rgb(var(--gray-500)); }
    .mgi-ip-manual summary { cursor: pointer; }
    .mgi-ip-manual input { width: 100%; margin-top: .4rem; padding: .45rem .7rem; border-radius: .5rem; font-size: .8rem; color: inherit; background: #fff; box-shadow: 0 0 0 1px rgb(var(--gray-300)); border: 0; outline: 0; }
    .dark .mgi-ip-name { color: rgb(var(--gray-200)); }
    .dark .mgi-ip-btn, .dark .mgi-ip-chip, .dark .mgi-ip-search, .dark .mgi-ip-manual input { color: rgb(var(--gray-100)); background: rgb(var(--gray-800)); box-shadow: 0 0 0 1px rgb(var(--gray-600)); }
    .dark .mgi-ip-chip.is-on { color: #fff; background: rgb(var(--primary-600)); box-shadow: none; }
    .dark .mgi-ip-panel { background: rgb(var(--gray-900)); box-shadow: 0 0 0 1px rgb(var(--gray-700)); }
    .dark .mgi-ip-cell:hover { background: rgb(var(--gray-800)); }
    .dark .mgi-ip-cell.is-on { background: rgb(var(--gray-800)); }
    .dark .mgi-ip-cell span.l { color: rgb(var(--gray-300)); }
    .dark .mgi-ip-tile.is-empty { background: rgb(var(--gray-700)); color: rgb(var(--gray-300)); }
</style>
