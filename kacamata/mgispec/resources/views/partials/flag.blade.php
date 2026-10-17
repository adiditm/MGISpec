{{-- Round SVG flags (emoji flags don't render on Windows) --}}
@if ($code === 'en')
    <svg viewBox="0 0 36 36" class="{{ $class ?? 'h-5 w-5' }} shrink-0 rounded-full ring-1 ring-black/10" aria-hidden="true">
        <clipPath id="fen{{ $uid ?? '' }}"><circle cx="18" cy="18" r="18"/></clipPath>
        <g clip-path="url(#fen{{ $uid ?? '' }})">
            <rect width="36" height="36" fill="#012169"/>
            <path d="M0 0 L36 36 M36 0 L0 36" stroke="#fff" stroke-width="6"/>
            <path d="M0 0 L36 36 M36 0 L0 36" stroke="#c8102e" stroke-width="2"/>
            <path d="M18 0 V36 M0 18 H36" stroke="#fff" stroke-width="10"/>
            <path d="M18 0 V36 M0 18 H36" stroke="#c8102e" stroke-width="6"/>
        </g>
    </svg>
@elseif ($code === 'cn')
    <svg viewBox="0 0 36 36" class="{{ $class ?? 'h-5 w-5' }} shrink-0 rounded-full ring-1 ring-black/10" aria-hidden="true">
        <clipPath id="fcn{{ $uid ?? '' }}"><circle cx="18" cy="18" r="18"/></clipPath>
        <g clip-path="url(#fcn{{ $uid ?? '' }})">
            <rect width="36" height="36" fill="#de2910"/>
            <polygon points="9,6 10.2,9.8 14,9.8 11,12 12.2,15.8 9,13.5 5.8,15.8 7,12 4,9.8 7.8,9.8" fill="#ffde00"/>
            <circle cx="15" cy="6" r="1.1" fill="#ffde00"/>
            <circle cx="18" cy="9" r="1.1" fill="#ffde00"/>
            <circle cx="18" cy="13" r="1.1" fill="#ffde00"/>
            <circle cx="15" cy="16" r="1.1" fill="#ffde00"/>
        </g>
    </svg>
@elseif ($code === 'ph')
    <svg viewBox="0 0 36 36" class="{{ $class ?? 'h-5 w-5' }} shrink-0 rounded-full ring-1 ring-black/10" aria-hidden="true">
        <clipPath id="fph{{ $uid ?? '' }}"><circle cx="18" cy="18" r="18"/></clipPath>
        <g clip-path="url(#fph{{ $uid ?? '' }})">
            <rect width="36" height="18" fill="#0038a8"/><rect y="18" width="36" height="18" fill="#ce1126"/>
            <path d="M0 0 L20 18 L0 36 Z" fill="#fff"/><circle cx="7" cy="18" r="3.2" fill="#fcd116"/>
        </g>
    </svg>
@else
    <svg viewBox="0 0 36 36" class="{{ $class ?? 'h-5 w-5' }} shrink-0 rounded-full ring-1 ring-black/10" aria-hidden="true">
        <clipPath id="fid{{ $uid ?? '' }}"><circle cx="18" cy="18" r="18"/></clipPath>
        <g clip-path="url(#fid{{ $uid ?? '' }})"><rect width="36" height="18" fill="#ce1126"/><rect y="18" width="36" height="18" fill="#fff"/></g>
    </svg>
@endif
