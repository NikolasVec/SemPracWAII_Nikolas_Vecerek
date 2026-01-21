<?php

namespace App\Support;

class MapPresenter
{
    /**
     * Normalizuje mapový odkaz alebo súradnice na absolútnu URL (Google Maps) alebo vráti null.
     * Presunuté z helperu vo view do presenteru.
     *
     * @param string|null $s
     * @return string|null
     */
    public static function normalizeMapLink($s)
    {
        if ($s === null) return null;
        $s = trim($s);
        if ($s === '') return null;

        // Ak sú to čisté súradnice "lat,lng" (povolíme medzery)
        if (preg_match('/^\s*([+-]?\d+(?:\.\d+)?)\s*,\s*([+-]?\d+(?:\.\d+)?)\s*$/', $s, $m)) {
            $lat = $m[1]; $lng = $m[2];
            return 'https://www.google.com/maps/search/?api=1&query=' . $lat . ',' . $lng;
        }

        // Ak vyzerá ako google share s !3d...!4d..., doplníme prefix https ak chýba
        if (strpos($s, '!3d') !== false && strpos($s, '!4d') !== false) {
            if (!preg_match('#^https?://#i', $s)) {
                return 'https://www.google.com/maps' . $s;
            }
            return $s;
        }

        // Ak obsahuje sekvenciu @lat,lng, považujeme to za google maps URL — zabezpečíme scheme
        if (preg_match('/@([+-]?\d+(?:\.\d+)?),([+-]?\d+(?:\.\d+)?)/', $s)) {
            if (!preg_match('#^https?://#i', $s)) {
                return 'https://' . ltrim($s, '/');
            }
            return $s;
        }

        // Ak obsahuje ?q=lat,lng, zabezpečíme scheme
        if (preg_match('/[?&]q=([+-]?\d+(?:\.\d+)?),([+-]?\d+(?:\.\d+)?)/', $s)) {
            if (!preg_match('#^https?://#i', $s)) {
                return 'https://' . ltrim($s, '/');
            }
            return $s;
        }

        // Ak je to host/path bez scheme, pridáme https:// (inak null ak nevyzerá ako URL)
        if (!preg_match('#^https?://#i', $s)) {
            // ak to jasne nie je URL (bez bodiek a bez šlások), vrátime null
            if (strpos($s, '.') === false && strpos($s, '/') === false) {
                return null;
            }
            return 'https://' . ltrim($s, '/');
        }

        // inak vrátime pôvodné
        return $s;
    }

    /**
     * Pripraví surové riadky z DB do štruktúr vhodných pre zobrazenie.
     * Výstupná položka obsahuje kľúče:
     *   - ID_stanoviska
     *   - nazov
     *   - poloha
     *   - popis
     *   - mapa_href (string|null)
     *   - obrazok_odkaz (string|null)
     *   - markerLeft (string|null) napr. "12.3%"
     *   - markerTop (string|null)
     *
     * @param array $rows
     * @return array
     */
    public function present(array $rows): array
    {
        $out = [];
        foreach ($rows as $s) {
            // podpora pre pola aj objekty
            $id = is_array($s) ? ($s['ID_stanoviska'] ?? null) : ($s->ID_stanoviska ?? null);
            $nazov = is_array($s) ? ($s['nazov'] ?? '') : ($s->nazov ?? '');
            $poloha = is_array($s) ? ($s['poloha'] ?? '') : ($s->poloha ?? '');
            $popis = is_array($s) ? ($s['popis'] ?? '') : ($s->popis ?? '');
            $mapa = is_array($s) ? ($s['mapa_odkaz'] ?? null) : ($s->mapa_odkaz ?? null);
            $img = is_array($s) ? ($s['obrazok_odkaz'] ?? null) : ($s->obrazok_odkaz ?? null);

            $mapaHref = self::normalizeMapLink($mapa);

            // vypocitaj poziciu markeru ak su platne relativne suradnice v [0,1]
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
