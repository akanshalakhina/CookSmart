<?php /** Branded preloader — shown only on the first page view of a session. */ ?>
<div class="preloader" aria-hidden="true">
    <div class="preloader__inner">
        <img src="<?= e(asset('images/logo/cooksmart-logo.webp')) ?>" alt="" width="260" height="93" class="preloader__logo">
        <svg class="preloader__swoosh" viewBox="0 0 260 30">
            <defs>
                <linearGradient id="pre-swoosh" x1="0" x2="1">
                    <stop offset="0" stop-color="#319847"/><stop offset=".5" stop-color="#64B741"/><stop offset="1" stop-color="#A8E13C"/>
                </linearGradient>
            </defs>
            <path class="preloader__line preloader__line--red" d="M8 22 C 70 6, 180 4, 252 16"/>
            <path class="preloader__line preloader__line--green" d="M16 25 C 80 10, 180 8, 244 19" stroke="url(#pre-swoosh)"/>
        </svg>
        <p class="preloader__text">Quality is not expensive — it’s priceless</p>
    </div>
</div>
