@props(['name', 'class' => 'icon'])
@php($s = 'fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"')
<svg class="{{ $class }}" viewBox="0 0 24 24" width="1em" height="1em" aria-hidden="true" focusable="false" {!! $s !!}>
@switch($name)
    @case('menu')
        <line x1="4" y1="7" x2="20" y2="7"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="17" x2="20" y2="17"/>
        @break
    @case('close')
        <line x1="5" y1="5" x2="19" y2="19"/><line x1="19" y1="5" x2="5" y2="19"/>
        @break
    @case('pin')
        <path d="M12 21s7-6.2 7-12a7 7 0 1 0-14 0c0 5.8 7 12 7 12Z"/><circle cx="12" cy="9" r="2.4"/>
        @break
    @case('phone')
        <path d="M6.5 3.5h3l1.5 4-2 1.5a11 11 0 0 0 6 6l1.5-2 4 1.5v3c0 1.1-.9 2-2 2A16.5 16.5 0 0 1 4.5 5.5c0-1.1.9-2 2-2Z"/>
        @break
    @case('mail')
        <rect x="3.5" y="5.5" width="17" height="13" rx="1.8"/><path d="m4.5 6.5 7.5 6 7.5-6"/>
        @break
    @case('clock')
        <circle cx="12" cy="12" r="8.5"/><path d="M12 7.5V12l3 2"/>
        @break
    @case('edit')
        <path d="M4 20h4L18.5 9.5a2 2 0 0 0 0-2.8l-1.2-1.2a2 2 0 0 0-2.8 0L4 16v4Z"/><path d="m13.5 6.5 4 4"/>
        @break
    @case('chevron-down')
        <path d="m6 9 6 6 6-6"/>
        @break
    @case('sparkle')
        <path d="M12 3v4M12 17v4M3 12h4M17 12h4M6 6l2.5 2.5M15.5 15.5 18 18M18 6l-2.5 2.5M8.5 15.5 6 18"/>
        @break
    @case('ruler')
        <rect x="3" y="9" width="18" height="6" rx="1.2"/><path d="M7 9v2M11 9v3M15 9v2M19 9v3"/>
        @break
    @case('cart')
        <circle cx="9.5" cy="20" r="1.3"/><circle cx="17.5" cy="20" r="1.3"/><path d="M3 4h2.2l2 11.2a2 2 0 0 0 2 1.6h8.1a2 2 0 0 0 2-1.6L21 8H6.2"/>
        @break
    @case('calendar')
        <rect x="3.5" y="5.5" width="17" height="15" rx="1.8"/><path d="M8 3.5v4M16 3.5v4M3.5 10h17"/>
        @break
    @case('shirt')
        <path d="M8 4 5 6.5 3.5 9.5 6 11v9h12v-9l2.5-1.5L19 6.5 16 4l-2 2a2 2 0 0 1-4 0Z"/>
        @break
    @case('jacket')
        <path d="M8.5 4 5 7l1 3 2-1v11h8V9l2 1 1-3-3.5-3-2.5 2.2a2.2 2.2 0 0 1-3 0Z"/><path d="M10 7.5 8.8 20M14 7.5 15.2 20"/>
        @break
    @case('vest')
        <path d="M8.5 4 6 6.5 7 10l1.5-.6V20h7V9.4L17 10l1-3.5L15.5 4l-2.3 2.6a2 2 0 0 1-2.4 0Z"/>
        @break
    @case('coat')
        <path d="M8 4 4.5 7.5 6 11l1.5-1V21h9V10l1.5 1 1.5-3.5L16 4l-2.5 2.5a2.2 2.2 0 0 1-3 0Z"/><path d="M12 11v10"/>
        @break
    @case('bowtie')
        <path d="M4 8v8l6-4-6-4Z"/><path d="M20 8v8l-6-4 6-4Z"/><circle cx="12" cy="12" r="1.6"/>
        @break
    @case('gear')
        <circle cx="12" cy="12" r="3"/><path d="M12 3.5v2.4M12 18.1v2.4M20.5 12h-2.4M5.9 12H3.5M17.8 6.2l-1.7 1.7M7.9 16.1l-1.7 1.7M17.8 17.8l-1.7-1.7M7.9 7.9 6.2 6.2"/>
        @break
    @case('users')
        <circle cx="8.5" cy="8" r="3"/><path d="M2.8 19.5c.6-3.3 2.9-5 5.7-5s5.1 1.7 5.7 5"/><circle cx="16.5" cy="9" r="2.4"/><path d="M15 14.8c2.3.2 4 1.8 4.5 4.7"/>
        @break
    @case('user')
        <circle cx="12" cy="8" r="3.6"/><path d="M4.2 20c.8-4 3.6-6 7.8-6s7 2 7.8 6"/>
        @break
    @case('logout')
        <path d="M9 4H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h3"/><path d="M15.5 16.5 20 12l-4.5-4.5"/><path d="M20 12H9"/>
        @break
    @case('grid')
        <rect x="3.5" y="3.5" width="7.5" height="7.5" rx="1.2"/><rect x="13" y="3.5" width="7.5" height="7.5" rx="1.2"/><rect x="3.5" y="13" width="7.5" height="7.5" rx="1.2"/><rect x="13" y="13" width="7.5" height="7.5" rx="1.2"/>
        @break
    @case('trash')
        <path d="M4.5 7h15M9.5 7V5a1.5 1.5 0 0 1 1.5-1.5h2A1.5 1.5 0 0 1 14.5 5v2M6.5 7l1 12.5a1.5 1.5 0 0 0 1.5 1.5h6a1.5 1.5 0 0 0 1.5-1.5L17.5 7"/>
        @break
    @case('eye')
        <path d="M2.5 12S6 5.5 12 5.5 21.5 12 21.5 12 18 18.5 12 18.5 2.5 12 2.5 12Z"/><circle cx="12" cy="12" r="3"/>
        @break
    @case('eye-off')
        <path d="M2.5 12S6 5.5 12 5.5 21.5 12 21.5 12 18 18.5 12 18.5 2.5 12 2.5 12Z"/><circle cx="12" cy="12" r="3"/><line x1="4" y1="20" x2="20" y2="4"/>
        @break
    @case('whatsapp')
        <path fill="currentColor" stroke="none" d="M19.1 4.9A9.8 9.8 0 0 0 12 2a9.9 9.9 0 0 0-8.6 14.8L2 22l5.3-1.4A9.9 9.9 0 0 0 12 21.9a9.9 9.9 0 0 0 7.1-16.9ZM12 20.1a8.2 8.2 0 0 1-4.2-1.1l-.3-.2-3.1.8.8-3-.2-.3A8.2 8.2 0 1 1 20.2 12 8.2 8.2 0 0 1 12 20.1Zm4.5-6.1c-.2-.1-1.4-.7-1.7-.8s-.4-.1-.6.1-.7.8-.8 1-.3.2-.6.1a6.7 6.7 0 0 1-2-1.2 7.4 7.4 0 0 1-1.4-1.7c-.1-.2 0-.4.1-.5s.2-.3.4-.4a1.5 1.5 0 0 0 .2-.4.4.4 0 0 0 0-.4c-.1-.1-.6-1.4-.8-1.9s-.4-.4-.6-.4h-.5a1 1 0 0 0-.7.3 3 3 0 0 0-.9 2.2 5.2 5.2 0 0 0 1.1 2.8 11.9 11.9 0 0 0 4.6 4c.6.3 1.1.4 1.5.6a3.6 3.6 0 0 0 1.7.1 2.8 2.8 0 0 0 1.8-1.3 2.3 2.3 0 0 0 .2-1.3c-.1-.1-.2-.2-.5-.3Z"/>
        @break
    @default
        <circle cx="12" cy="12" r="8.5"/>
@endswitch
</svg>
