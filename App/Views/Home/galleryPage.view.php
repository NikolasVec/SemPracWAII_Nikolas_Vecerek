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
            <?php $albumIndex = 0; foreach ($albums as $a):
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
                                <?php $carouselId = 'galleryCarousel' . $albumIndex; ?>
                                <div id="<?= $carouselId ?>" class="carousel slide" data-bs-ride="false">
                                    <div class="carousel-indicators">
                                        <?php foreach ($photos as $idx => $ph):
                                            $filename = $ph['filename'] ?? $ph['file'] ?? null;
                                            if (!$filename) continue;
                                        ?>
                                            <button type="button" data-bs-target="#<?= $carouselId ?>" data-bs-slide-to="<?= $idx ?>" <?= $idx===0 ? 'class="active" aria-current="true"' : '' ?> aria-label="Slide <?= $idx+1 ?>"></button>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="carousel-inner">
                                        <?php foreach ($photos as $idx => $ph):
                                            $filename = $ph['filename'] ?? $ph['file'] ?? null;
                                            if (!$filename) continue;
                                            $src = $__asset('images/gallery/' . ($albumId ? $albumId . '/' : '') . $filename);
                                        ?>
                                            <div class="carousel-item <?= $idx===0 ? 'active' : '' ?>">
                                                <a href="<?= htmlspecialchars((string)$src) ?>" target="_blank">
                                                    <img src="<?= htmlspecialchars((string)$src) ?>" alt="<?= htmlspecialchars((string)($ph['original_name'] ?? $filename)) ?>" class="d-block w-100" style="max-height:60vh; object-fit:contain;" />
                                                </a>
                                                <!-- removed filename caption per request -->
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <button class="carousel-control-prev" type="button" data-bs-target="#<?= $carouselId ?>" data-bs-slide="prev" aria-label="Predchádzajúce">
                                        <i class="bi bi-chevron-left" aria-hidden="true" style="font-size:80px; color:#ECB501;"></i>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#<?= $carouselId ?>" data-bs-slide="next" aria-label="Nasledujúce">
                                        <i class="bi bi-chevron-right" aria-hidden="true" style="font-size:80px; color:#ECB501;"></i>
                                    </button>
                                </div>

                                <?php $albumIndex++; ?>
                             <?php endif; ?>
                         </div>
                     </div>
                 </div>
             <?php endforeach; ?>
         </div>
    <?php endif; ?>
</div>
