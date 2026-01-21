<?php

namespace App\Support;

use Framework\Support\FooterDataProvider;
use Framework\Support\LinkGenerator;

/**
 * Presenter pre pätičku stránky.
 * Pripravuje dáta pre zobrazenie footeru.
 */
class FooterPresenter
{
    private FooterDataProvider $provider;
    private LinkGenerator $link;

    /**
     * Nastaví link generator a data provider.
     */
    public function __construct(LinkGenerator $link, ?FooterDataProvider $provider = null)
    {
        $this->link = $link;
        $this->provider = $provider ?? new FooterDataProvider();
    }

    /**
     * Email pre kontakt.
     */
    public function getContactEmail(): string
    {
        return $this->provider->getContactEmail();
    }

    /**
     * Telefónne číslo pre kontakt.
     */
    public function getContactPhone(): string
    {
        return $this->provider->getContactPhone();
    }

    /**
     * URL na Facebook.
     */
    public function getFacebookUrl(): string
    {
        return $this->provider->getFacebookUrl();
    }

    /**
     * URL na Instagram.
     */
    public function getInstagramUrl(): string
    {
        return $this->provider->getInstagramUrl();
    }

    /**
     * Vracia sponzorov ako pole normalizovaných položiek pripravených pre zobrazenie.
     * Každ�� položka obsahuje: name, url, hasLogo, logoSrc, imgStyle.
     */
    public function getSponsors(): array
    {
        $raw = $this->provider->getSponsors();
        $out = [];

        foreach ($raw as $r) {
            $logo = $r['logo'] ?? null;
            $name = $r['name'] ?? '';
            $url = $r['url'] ?? null;

            $item = [
                'name' => $name,
                'url' => $url,
            ];

            if ($logo) {
                $src = $this->link->asset('images/sponsors/' . $logo);
                $ext = strtolower(pathinfo((string)$logo, PATHINFO_EXTENSION));
                $imgStyle = 'height:48px;object-fit:contain;display:block;border:1px solid rgba(255,255,255,0.06);';
                if ($ext !== 'png') {
                    $imgStyle .= 'background:#fff;padding:4px;';
                }

                $item['hasLogo'] = true;
                $item['logoSrc'] = $src;
                $item['imgStyle'] = $imgStyle;
            } else {
                $item['hasLogo'] = false;
                $item['logoSrc'] = null;
                $item['imgStyle'] = '';
            }

            $out[] = $item;
        }

        return $out;
    }

    /**
     * Pripraví kontaktné informácie pre view.
     * Kľúče: email, mailto, phone, tel, facebook, instagram
     */
    public function getPreparedContact(): array
    {
        $email = $this->getContactEmail() ?? '';
        $phone = $this->getContactPhone() ?? '';
        $facebook = $this->getFacebookUrl() ?? '';
        $instagram = $this->getInstagramUrl() ?? '';

        return [
            'email' => $email,
            'mailto' => $email !== '' ? 'mailto:' . $email : '',
            'phone' => $phone,
            'tel' => $phone !== '' ? 'tel:' . $phone : '',
            'facebook' => $facebook,
            'instagram' => $instagram,
        ];
    }

    /**
     * Vráti copyright riadok (HTML), meno aplikácie je escapované.
     */
    public function getCopyrightLine(): string
    {
        $year = date('Y');
        $appName = htmlspecialchars((string)\App\Configuration::APP_NAME, ENT_QUOTES, 'UTF-8');
        return '&copy; ' . $year . ' ' . $appName . ' — Všetky práva vyhradené';
    }
}
