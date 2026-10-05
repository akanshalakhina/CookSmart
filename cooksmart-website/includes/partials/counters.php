<?php /** Counter row — real figures only (JS counts up from 0). Vars: $tone ('dark'|'light'). */ $tone = $tone ?? 'dark'; ?>
<div class="counters counters--<?= e($tone) ?>" data-anim-stagger="fade-up" data-stagger="0.12">
    <div class="counter">
        <p class="counter__value"><span data-counter="<?= years_since_founded() ?>"><?= years_since_founded() ?></span><span class="counter__suffix">+</span></p>
        <p class="counter__label">Years of journey <small>since <?= FOUNDED_YEAR ?></small></p>
    </div>
    <div class="counter">
        <p class="counter__value"><span class="counter__prefix">₹</span><span data-counter="90">90</span><span class="counter__suffix">&nbsp;Cr</span></p>
        <p class="counter__label">Turnover <small>approximately</small></p>
    </div>
    <div class="counter">
        <p class="counter__value"><span data-counter="3">3</span></p>
        <p class="counter__label">Core categories <small>spices · pulses · cereals</small></p>
    </div>
    <div class="counter">
        <p class="counter__value"><span data-counter="9">9</span></p>
        <p class="counter__label">Business segments <small>from retail to HORECA</small></p>
    </div>
</div>
