<?php
/** @var \Framework\Support\LinkGenerator $link */
use Framework\DB\Connection;

$contactEmail = null;
$contactPhone = null;
$facebookUrl = null;
$instagramUrl = null;
$sponsors = [];
try {
    $conn = Connection::getInstance();
    // try to read contact info from settings table if present
    $stmt = $conn->query("SHOW TABLES LIKE 'settings'");
    if ($stmt && $stmt->fetchColumn() !== false) {
        // include social links if present
        $s = $conn->prepare("SELECT k, v FROM settings WHERE k IN ('contact_email','contact_phone','facebook_url','instagram_url')");
        $s->execute();
        $rows = $s->fetchAll();
        foreach ($rows as $r) {
            if ($r['k'] === 'contact_email') $contactEmail = $r['v'];
            if ($r['k'] === 'contact_phone') $contactPhone = $r['v'];
            if ($r['k'] === 'facebook_url') $facebookUrl = $r['v'];
            if ($r['k'] === 'instagram_url') $instagramUrl = $r['v'];
        }
    }

    // fetch sponsors if table exists
    $stmt = $conn->query("SHOW TABLES LIKE 'sponsors'");
    if ($stmt && $stmt->fetchColumn() !== false) {
        $s2 = $conn->query('SELECT * FROM sponsors ORDER BY created_at ASC');
        $sponsors = $s2->fetchAll();
    }
} catch (\Throwable $e) {
    // ignore DB errors in footer - fail silently
}

// Fallback contact values
if (empty($contactEmail)) $contactEmail = 'behpopivo@gmail.com';
if (empty($contactPhone)) $contactPhone = '+421 900 000 000';
// Fallback social URLs (show icons always; use these if DB doesn't provide them)
if (empty($facebookUrl)) $facebookUrl = 'https://www.facebook.com/BehPoPivo?locale=sk_SK';
if (empty($instagramUrl)) $instagramUrl = 'https://www.instagram.com/behpopivo_martin/';
?>

<footer class="site-footer" style="background:#111;color:#eee;padding:2rem 1rem 2rem 4rem;margin-top:2rem;border-top:4px solid #222;">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-4 mb-3">
                <h5 style="color:#fff;margin-bottom:0.5rem;">Kontakt</h5>
                <div style="font-size:0.95rem;">
                    <div>Email: <a href="mailto:<?= htmlspecialchars($contactEmail) ?>" style="color:#ddd;"><?= htmlspecialchars($contactEmail) ?></a></div>
                    <div>Tel: <a href="tel:<?= htmlspecialchars($contactPhone) ?>" style="color:#ddd;"><?= htmlspecialchars($contactPhone) ?></a></div>
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
                <h5 style="color:#fff;margin-bottom:0.5rem;">Sponzori</h5>
                <div class="d-flex align-items-center" style="gap:1rem;flex-wrap:wrap;">
                    <?php if (!empty($sponsors)): ?>
                        <?php foreach ($sponsors as $sp): ?>
                            <?php $logo = $sp['logo'] ?? null; $url = $sp['url'] ?? null; $name = $sp['name'] ?? ''; ?>
                            <div style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.25rem 0.5rem;background:transparent;border-radius:4px;">
                                <?php if ($logo): ?>
                                    <?php $src = isset($link) ? $link->asset('images/sponsors/' . $logo) : '/images/sponsors/' . $logo; ?>
                                    <?php // determine extension (case-insensitive) to preserve transparency for PNG files ?>
                                    <?php $ext = strtolower(pathinfo((string)$logo, PATHINFO_EXTENSION)); ?>
                                    <?php $imgStyle = 'height:48px;object-fit:contain;display:block;border:1px solid rgba(255,255,255,0.06);'; ?>
                                    <?php if ($ext !== 'png') { $imgStyle .= 'background:#fff;padding:4px;'; } ?>
                                    <?php if ($url): ?><a href="<?= htmlspecialchars($url) ?>" target="_blank" rel="noopener noreferrer"><?php endif; ?>
                                        <img src="<?= htmlspecialchars($src) ?>" alt="<?= htmlspecialchars($name) ?>" style="<?= $imgStyle ?>"/>
                                    <?php if ($url): ?></a><?php endif; ?>
                                <?php else: ?>
                                    <div style="padding:0.5rem 0.75rem;background:#222;border-radius:4px;color:#ddd;"><?= htmlspecialchars($name) ?></div>
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
            <div class="col text-center small text-muted">&copy; <?= date('Y') ?> <?= htmlspecialchars(App\Configuration::APP_NAME) ?> — Všetky práva vyhradené</div>
        </div>
    </div>
</footer>
