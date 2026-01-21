<?php

namespace App\Support;

use Framework\Support\FooterDataProvider;
use Framework\Support\LinkGenerator;

class FooterPresenter
{
    private FooterDataProvider $provider;
    private LinkGenerator $link;

    public function __construct(LinkGenerator $link, ?FooterDataProvider $provider = null)
    {
        $this->link = $link;
        $this->provider = $provider ?? new FooterDataProvider();
    }

    public function getContactEmail(): string
    {
        return $this->provider->getContactEmail();
    }

    public function getContactPhone(): string
    {
        return $this->provider->getContactPhone();
    }

    public function getFacebookUrl(): string
    {
        return $this->provider->getFacebookUrl();
    }

    public function getInstagramUrl(): string
    {
        return $this->provider->getInstagramUrl();
    }

    /**
     * Returns sponsors as an array of normalized items with presentation-ready fields:
     * [
     *   'name' => string,
     *   'url' => ?string,
     *   'hasLogo' => bool,
     *   'logoSrc' => ?string,    // asset path
     *   'imgStyle' => string     // inline style for image
     * ]
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
     * Returns prepared contact info for presentation.
     * Keys: email, mailto, phone, tel, facebook, instagram
     * All values are strings (empty when not available).
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
     * Returns a presentation-ready copyright line (HTML).
     * The app name is escaped here so the view can render the string directly.
     */
    public function getCopyrightLine(): string
    {
        $year = date('Y');
        $appName = htmlspecialchars((string)\App\Configuration::APP_NAME, ENT_QUOTES, 'UTF-8');
        return '&copy; ' . $year . ' ' . $appName . ' — Všetky práva vyhradené';
    }
}
