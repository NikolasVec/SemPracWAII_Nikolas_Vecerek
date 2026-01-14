<?php

/** @var \Framework\Support\LinkGenerator $link */

// helper: robust asset resolver when $link may not be provided by the framework
$__asset = function(string $path) {
    global $link;
    if (isset($link) && is_object($link) && method_exists($link, 'asset')) {
        return $link->asset($path);
    }
    return '/' . ltrim($path, '/');
};
?>

<div class="container py-4">
    <br>
    <br>
    <h1>Galéria</h1>

    <?php if (empty($albums)): ?>
        <p>Galeria je prázdna.</p>
    <?php else: ?>
        <div class="row">
            <?php foreach ($albums as $a):
                // support two shapes: ['album'=>..., 'photos'=>...] or simple album row
                $alb = isset($a['album']) ? $a['album'] : $a;
                $photos = isset($a['photos']) ? $a['photos'] : [];
                $albumId = $alb['ID_album'] ?? $alb['ID_album'] ?? ($alb['ID'] ?? null);
                $albumName = $alb['name'] ?? ($alb['nazov'] ?? '');
                $albumDesc = $alb['description'] ?? '';
            ?>
                <div class="col-12 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars((string)$albumName) ?></h5>
                            <?php if ($albumDesc): ?><p class="card-text"><?= nl2br(htmlspecialchars((string)$albumDesc)) ?></p><?php endif; ?>

                            <?php if (empty($photos)): ?>
                                <p>Žiadne fotky v albume.</p>
                            <?php else: ?>
                                <div class="d-flex flex-wrap gap-2">
                                    <?php foreach ($photos as $ph):
                                        $filename = $ph['filename'] ?? $ph['file'] ?? null;
                                        if (!$filename) continue;
                                        $src = $__asset('images/gallery/' . ($albumId ? $albumId . '/' : '') . $filename);
                                    ?>
                                        <a href="<?= htmlspecialchars((string)$src) ?>" target="_blank" class="d-block">
                                            <img src="<?= htmlspecialchars((string)$src) ?>" alt="<?= htmlspecialchars((string)($ph['original_name'] ?? 'photo')) ?>" style="height:120px; object-fit:cover;" class="rounded">
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
