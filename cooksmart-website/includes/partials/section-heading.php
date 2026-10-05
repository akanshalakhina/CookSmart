<?php
/**
 * Section heading: script eyebrow + display heading + drawn swoosh underline.
 * Vars: $eyebrow, $title (trusted HTML, may contain <em>), $lead (optional),
 *       $align ('center'|'left'), $tone ('dark'|'light'), $id (optional heading id).
 */
$align = $align ?? 'center';
$tone  = $tone ?? 'dark';
?>
<div class="section-heading section-heading--<?= e($align) ?> section-heading--<?= e($tone) ?>">
    <?php if (!empty($eyebrow)): ?><p class="eyebrow" data-anim="fade-up"><?= e($eyebrow) ?></p><?php endif; ?>
    <h2 class="section-title"<?= !empty($id) ? ' id="' . e($id) . '"' : '' ?> data-anim="split-up"><?= $title ?></h2>
    <?= swoosh_line() ?>
    <?php if (!empty($lead)): ?><p class="section-lead" data-anim="fade-up" data-delay="0.15"><?= e($lead) ?></p><?php endif; ?>
</div>
