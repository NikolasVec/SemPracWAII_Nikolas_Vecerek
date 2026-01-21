<?php
/** @var \Framework\Support\LinkGenerator $link */
/** @var \App\Support\FooterPresenter $footerPresenter */

$contact = $footerPresenter->getPreparedContact();
$sponsors = $footerPresenter->getSponsors();

$contactEmail = $contact['email'] ?? '';
$contactPhone = $contact['phone'] ?? '';
$facebookUrl = $contact['facebook'] ?? '';
$instagramUrl = $contact['instagram'] ?? '';
$mailto = $contact['mailto'] ?? '';
$tel = $contact['tel'] ?? '';
?>

<footer class="site-footer" style="background:#111;color:#eee;padding:2rem 1rem 2rem 4rem;margin-top:2rem;border-top:4px solid #222;">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-4 mb-3">
                <h5>Kontakt</h5>
                <div style="font-size:0.95rem;">
                    <div>Email: <a href="<?= htmlspecialchars($mailto) ?>" style="color:#ddd;"><?= htmlspecialchars($contactEmail) ?></a></div>
                    <div>Tel: <a href="<?= htmlspecialchars($tel) ?>" style="color:#ddd;"><?= htmlspecialchars($contactPhone) ?></a></div>
                </div>
                <!-- Social icons placed under contact info -->
                <div class="mt-2">
                    <a href="<?= htmlspecialchars($facebookUrl) ?>" target="_blank" rel="noopener noreferrer" class="text-muted me-3" aria-label="Facebook" title="Facebook">
                        <i class="bi bi-facebook" style="font-size:1.35rem;color:#ddd;"></i>
                    </a>
                    <a href="<?= htmlspecialchars($instagramUrl) ?>" target="_blank" rel="noopener noreferrer" class="text-muted" aria-label="Instagram" title="Instagram">
                        <i class="bi bi-instagram" style="font-size:1.35rem;color:#ddd;"></i>
                    </a>
                </div>
            </div>
            <div class="col-md-8">
                <h5>Sponzori</h5>
                <div class="d-flex align-items-center" style="gap:1rem;flex-wrap:wrap;">
                    <?php if (!empty($sponsors)): ?>
                        <?php foreach ($sponsors as $sp): ?>
                            <div style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.25rem 0.5rem;background:transparent;border-radius:4px;">
                                <?php if (!empty($sp['hasLogo'])): ?>
                                    <?php if (!empty($sp['url'])): ?><a href="<?= htmlspecialchars($sp['url']) ?>" target="_blank" rel="noopener noreferrer"><?php endif; ?>
                                        <img src="<?= htmlspecialchars($sp['logoSrc'] ?? '') ?>" alt="<?= htmlspecialchars($sp['name'] ?? '') ?>" style="<?= htmlspecialchars($sp['imgStyle'] ?? '') ?>"/>
                                    <?php if (!empty($sp['url'])): ?></a><?php endif; ?>
                                <?php else: ?>
                                    <div style="padding:0.5rem 0.75rem;background:#222;border-radius:4px;color:#ddd;"><?= htmlspecialchars($sp['name'] ?? '') ?></div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-muted">Žiadni sponzori zatiaľ nepridaní.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col text-center small text-muted"><?php echo $footerPresenter->getCopyrightLine(); ?></div>
        </div>
    </div>
</footer>
