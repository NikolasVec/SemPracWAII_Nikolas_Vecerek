<?php

namespace App\Support;

use Framework\Support\LinkGenerator;

/**
 * Presenter pre galériu.
 * Normalizuje albumy/fotky a rieši asset URL pre view.
 */
class GalleryPresenter
{
    private LinkGenerator $link;
    private array $albumsRaw;

    public function __construct(LinkGenerator $link, array $albums = [])
    {
        $this->link = $link;
        $this->albumsRaw = $albums;
    }

    /**
     * Vyrieši URL assetu cez LinkGenerator.
     */
    public function asset(string $path): string
    {
        return $this->link->asset($path);
    }

    /**
     * Vráti pripravené albumy pre zobrazenie.
     * Každý album obsahuje: id, name, description, photos[].
     */
    public function getAlbums(): array
    {
        $out = [];
        foreach ($this->albumsRaw as $a) {
            $alb = isset($a['album']) ? $a['album'] : $a;
            $photos = isset($a['photos']) ? $a['photos'] : [];

            $albumId = $alb['ID_album'] ?? $alb['ID'] ?? null;
            $albumName = $alb['name'] ?? $alb['nazov'] ?? '';
            $albumDesc = $alb['description'] ?? '';

            $normalizedPhotos = [];
            foreach ($photos as $ph) {
                $filename = $ph['filename'] ?? $ph['file'] ?? null;
                if (!$filename) {
                    continue;
                }
                $src = $this->asset('images/gallery/' . ($albumId ? $albumId . '/' : '') . $filename);
                $normalizedPhotos[] = [
                    'filename' => $filename,
                    'original_name' => $ph['original_name'] ?? $filename,
                    'src' => $src,
                ];
            }

            $out[] = [
                'id' => $albumId,
                'name' => $albumName,
                'description' => $albumDesc,
                'photos' => $normalizedPhotos,
            ];
        }

        return $out;
    }

    /**
     * Vráti unikátne ID karuselu pre index.
     */
    public function getCarouselIdForIndex(int $index): string
    {
        return 'galleryCarousel' . $index;
    }
}
