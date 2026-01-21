<?php

namespace App\Support;

class MapPresenter
{
    /**
     * Normalize a map link or coordinates into a proper absolute URL (Google Maps compatible) or null.
     * Moved from view-level helper into a presenter.
     *
     * @param string|null $s
     * @return string|null
     */
    public static function normalizeMapLink($s)
    {
        if ($s === null) return null;
        $s = trim($s);
        if ($s === '') return null;

        // If it's plain coordinates like "lat,lng" (allow spaces)
        if (preg_match('/^\s*([+-]?\d+(?:\.\d+)?)\s*,\s*([+-]?\d+(?:\.\d+)?)\s*$/', $s, $m)) {
            $lat = $m[1]; $lng = $m[2];
            return 'https://www.google.com/maps/search/?api=1&query=' . $lat . ',' . $lng;
        }

        // If it looks like a google maps share with !3d...!4d..., convert to a google maps link (just prefix https if needed)
        if (strpos($s, '!3d') !== false && strpos($s, '!4d') !== false) {
            if (!preg_match('#^https?://#i', $s)) {
                return 'https://www.google.com/maps' . $s;
            }
            return $s;
        }

        // If it contains an @lat,lng sequence, assume it's part of a google maps URL �� ensure scheme
        if (preg_match('/@([+-]?\d+(?:\.\d+)?),([+-]?\d+(?:\.\d+)?)/', $s)) {
            if (!preg_match('#^https?://#i', $s)) {
                return 'https://' . ltrim($s, '/');
            }
            return $s;
        }

        // If it contains ?q=lat,lng, ensure scheme
        if (preg_match('/[?&]q=([+-]?\d+(?:\.\d+)?),([+-]?\d+(?:\.\d+)?)/', $s)) {
            if (!preg_match('#^https?://#i', $s)) {
                return 'https://' . ltrim($s, '/');
            }
            return $s;
        }

        // If it's a plain host/path without scheme, add https://
        if (!preg_match('#^https?://#i', $s)) {
            // but if it's clearly not a URL (no dots and no slashes) just return null
            if (strpos($s, '.') === false && strpos($s, '/') === false) {
                return null;
            }
            return 'https://' . ltrim($s, '/');
        }

        // otherwise return as-is
        return $s;
    }

    /**
     * Prepare raw DB rows into presentation-friendly structures.
     * Each output item will contain keys:
     *   - ID_stanoviska
     *   - nazov
     *   - poloha
     *   - popis
     *   - mapa_href (string|null)
     *   - obrazok_odkaz (string|null)
     *   - markerLeft (string|null) e.g. "12.3%"
     *   - markerTop (string|null)
     *
     * @param array $rows
     * @return array
     */
    public function present(array $rows): array
    {
        $out = [];
        foreach ($rows as $s) {
            // support both arrays and objects
            $id = is_array($s) ? ($s['ID_stanoviska'] ?? null) : ($s->ID_stanoviska ?? null);
            $nazov = is_array($s) ? ($s['nazov'] ?? '') : ($s->nazov ?? '');
            $poloha = is_array($s) ? ($s['poloha'] ?? '') : ($s->poloha ?? '');
            $popis = is_array($s) ? ($s['popis'] ?? '') : ($s->popis ?? '');
            $mapa = is_array($s) ? ($s['mapa_odkaz'] ?? null) : ($s->mapa_odkaz ?? null);
            $img = is_array($s) ? ($s['obrazok_odkaz'] ?? null) : ($s->obrazok_odkaz ?? null);

            $mapaHref = self::normalizeMapLink($mapa);

            // compute marker position if valid numeric relative coords in [0,1]
            $x = is_array($s) ? ($s['x_pos'] ?? null) : ($s->x_pos ?? null);
            $y = is_array($s) ? ($s['y_pos'] ?? null) : ($s->y_pos ?? null);
            $markerLeft = null;
            $markerTop = null;
            if ($x !== null && $y !== null && $x !== '' && $y !== '') {
                if (is_numeric($x) && is_numeric($y)) {
                    $xn = (float)$x; $yn = (float)$y;
                    if ($xn >= 0 && $xn <= 1 && $yn >= 0 && $yn <= 1) {
                        $markerLeft = ($xn * 100) . '%';
                        $markerTop = ($yn * 100) . '%';
                    }
                }
            }

            $out[] = [
                'ID_stanoviska' => $id,
                'nazov' => $nazov,
                'poloha' => $poloha,
                'popis' => $popis,
                'mapa_href' => $mapaHref,
                'obrazok_odkaz' => $img,
                'markerLeft' => $markerLeft,
                'markerTop' => $markerTop,
            ];
        }

        return $out;
    }
}

