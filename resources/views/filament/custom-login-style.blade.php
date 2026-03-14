<style>
    /* 1. Background Halaman Utama dengan Soft Gradient & Pattern */
    .fi-layout {
        background: radial-gradient(circle at top left, #ecfdf5 0%, #ffffff 50%, #f0fdf4 100%) !important;
        position: relative;
        overflow: hidden;
    }

    .dark .fi-layout {
        background: radial-gradient(circle at top left, #064e3b 0%, #09090b 50%, #022c22 100%) !important;
    }

    /* 2. Dekorasi Background Pattern (Laravel SVG) */
    .fi-layout::before {
        content: "";
        position: absolute;
        top: 0; right: 0;
        width: 100%; height: 100%;
        background-image: url('https://laravel.com/assets/img/welcome/background.svg');
        background-repeat: no-repeat;
        background-position: top right;
        opacity: 0.1;
        pointer-events: none;
        z-index: 0;
    }

    /* 3. Glassmorphism Card pada Form Login */
    main section {
        background: rgba(255, 255, 255, 0.8) !important;
        backdrop-filter: blur(16px) !important;
        -webkit-backdrop-filter: blur(16px) !important;
        border: 1px solid rgba(16, 185, 129, 0.3) !important;
        border-radius: 28px !important;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
        z-index: 10;
        padding: 2rem !important;
    }

    .dark main section {
        background: rgba(24, 24, 27, 0.7) !important;
        border: 1px solid rgba(16, 185, 129, 0.2) !important;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5) !important;
    }

    /* 4. Kustomisasi Input Field agar lebih "Clean" */
    .fi-input-wrp {
        border-radius: 12px !important;
        transition: all 0.3s ease !important;
    }

    .fi-input-wrp:focus-within {
        ring: 2px !important;
        ring-color: #10b981 !important; /* Hijau Emerald khas Puskesmas */
        border-color: #10b981 !important;
        transform: translateY(-1px);
    }

    /* 5. Animasi Tombol Login (Button) */
    .fi-btn {
        border-radius: 12px !important;
        padding: 0.75rem !important;
        font-weight: 700 !important;
        letter-spacing: 0.025em !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        background-image: linear-gradient(to right, #10b981, #059669) !important;
    }

    .fi-btn:hover {
        transform: scale(1.02) translateY(-2px) !important;
        box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.4) !important;
        filter: brightness(1.1);
    }

    .fi-btn:active {
        transform: scale(0.98) !important;
    }

    /* 6. Logo & Text Styling */
    .fi-logo {
        filter: drop-shadow(0 5px 15px rgba(16, 185, 129, 0.2));
        margin-bottom: 1rem !important;
    }

    .fi-header-title {
        font-weight: 900 !important;
        background: linear-gradient(to bottom right, #064e3b, #10b981);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        letter-spacing: -0.05em !important;
    }

    .dark .fi-header-title {
        background: linear-gradient(to bottom right, #ffffff, #10b981);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    /* 7. Footer / Copyright Info */
    footer p {
        font-size: 0.75rem !important;
        color: #6b7280 !important;
        font-weight: 500;
    }
</style>