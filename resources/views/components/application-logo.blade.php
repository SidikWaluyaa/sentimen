<svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" {{ $attributes }}>
    <defs>
        <linearGradient id="shopeeOrangeGrad" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#ff7337"/>
            <stop offset="50%" stop-color="#ee4d2d"/>
            <stop offset="100%" stop-color="#c92a08"/>
        </linearGradient>
        <filter id="logoGlowFilter" x="-20%" y="-20%" width="140%" height="140%">
            <feDropShadow dx="0" dy="4" stdDeviation="6" flood-color="#ee4d2d" flood-opacity="0.45"/>
        </filter>
    </defs>
    <!-- Background rounded squircle with shadow -->
    <rect x="6" y="6" width="88" height="88" rx="24" fill="url(#shopeeOrangeGrad)" filter="url(#logoGlowFilter)"/>
    <rect x="7" y="7" width="86" height="86" rx="23" stroke="rgba(255,255,255,0.25)" stroke-width="2"/>
    
    <!-- Cloche Top Handle -->
    <circle cx="50" cy="27" r="4.5" fill="#ffffff"/>
    
    <!-- Food Cloche Dome -->
    <path d="M26 48C26 35.85 36.75 26 50 26C63.25 26 74 35.85 74 48H26Z" fill="#ffffff" fill-opacity="0.95"/>
    
    <!-- Cloche Base Tray Rim -->
    <rect x="22" y="49" width="56" height="5" rx="2.5" fill="#ffffff"/>
    
    <!-- AI Sentiment Wave Arc (Smile / Analytics Pulse) -->
    <path d="M30 64C38 73 62 73 70 64" stroke="#ffffff" stroke-width="4.5" stroke-linecap="round"/>
    
    <!-- Neural Analytics Spark Nodes -->
    <circle cx="31" cy="64" r="3" fill="#ffffff"/>
    <circle cx="69" cy="64" r="3" fill="#ffffff"/>
    <circle cx="50" cy="71" r="3.5" fill="#ffffff"/>
</svg>
