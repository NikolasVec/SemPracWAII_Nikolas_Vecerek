<?php

/** @var \Framework\Support\LinkGenerator $link */

// Use a presenter to keep this view presentation-only
/** @var \App\Support\GalleryPresenter $galleryPresenter */
$galleryPresenter = new \App\Support\GalleryPresenter($link, $albums ?? []);
$albums = $galleryPresenter->getAlbums();

?>

<div class="container py-4">
    <br>
    <br>
    <h1>Galéria</h1>

    <?php if (empty($albums)): ?>
        <p>Galeria je prázdna.</p>
    <?php else: ?>
        <div class="row">
            <?php $albumIndex = 0; foreach ($albums as $alb): ?>
                 <div class="col-12 mb-4">
                     <div class="card">
                         <div class="card-body">
                             <h5 class="card-title"><?= htmlspecialchars((string)($alb['name'] ?? '')) ?></h5>
                             <?php if (!empty($alb['description'])): ?><p class="card-text"><?= nl2br(htmlspecialchars((string)$alb['description'])) ?></p><?php endif; ?>

                             <?php if (empty($alb['photos'])): ?>
                                 <p>Žiadne fotky v albume.</p>
                             <?php else: ?>
                                <?php $carouselId = $galleryPresenter->getCarouselIdForIndex($albumIndex); ?>
                                <div id="<?= htmlspecialchars((string)$carouselId) ?>" class="carousel slide" data-bs-ride="false">
                                    <div class="carousel-indicators">
                                        <?php foreach ($alb['photos'] as $idx => $ph): ?>
                                            <button type="button" data-bs-target="#<?= htmlspecialchars((string)$carouselId) ?>" data-bs-slide-to="<?= $idx ?>" <?= $idx===0 ? 'class="active" aria-current="true"' : '' ?> aria-label="Slide <?= $idx+1 ?>"></button>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="carousel-inner">
                                        <?php foreach ($alb['photos'] as $idx => $ph): ?>
                                            <div class="carousel-item <?= $idx===0 ? 'active' : '' ?>">
                                                <a href="<?= htmlspecialchars((string)($ph['src'] ?? '')) ?>" target="_blank">
                                                    <img src="<?= htmlspecialchars((string)($ph['src'] ?? '')) ?>" alt="<?= htmlspecialchars((string)($ph['original_name'] ?? $ph['filename'] ?? '')) ?>" class="d-block w-100" style="max-height:60vh; object-fit:contain;" />
                                                </a>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <button class="carousel-control-prev" type="button" data-bs-target="#<?= htmlspecialchars((string)$carouselId) ?>" data-bs-slide="prev" aria-label="Predchádzajúce">
                                        <i class="bi bi-chevron-left" aria-hidden="true" style="font-size:80px; color:#ECB501;"></i>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#<?= htmlspecialchars((string)$carouselId) ?>" data-bs-slide="next" aria-label="Nasledujúce">
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
