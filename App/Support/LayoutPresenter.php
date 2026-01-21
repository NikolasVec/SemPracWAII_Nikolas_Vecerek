<?php

namespace App\Support;

use Framework\Auth\AppUser;
use Framework\Support\LinkGenerator;

/**
 * Presenter pre layout (hlavička/pätička).
 * Poskytuje pomocné metódy pre šablóny (URL, assety, stav prihlásenia).
 */
class LayoutPresenter
{
    private AppUser $user;
    private LinkGenerator $link;

    /**
     * Nastaví kontext (používateľa a link generator).
     */
    public function __construct(AppUser $user, LinkGenerator $link)
    {
        $this->user = $user;
        $this->link = $link;
    }

    /**
     * Skontroluje, či je používateľ prihlásený.
     */
    public function isLoggedIn(): bool
    {
        return $this->user->isLoggedIn();
    }

    /**
     * Skontroluje, či má používateľ administrátorské práva.
     */
    public function isAdmin(): bool
    {
        // keep original checks but encapsulated here
        return $this->user->isLoggedIn()
            && $this->user->getIdentity() !== null
            && method_exists($this->user->getIdentity(), 'isAdmin')
            && $this->user->isAdmin();
    }

    /**
     * URL na administráciu.
     */
    public function adminUrl(): string
    {
        return $this->link->url('admin.index');
    }

    /**
     * URL na profil prihláseného používateľa.
     */
    public function profileUrl(): string
    {
        return $this->link->url('home.profile');
    }

    /**
     * URL na prihlasovaciu stránku.
     */
    public function loginUrl(): string
    {
        return \App\Configuration::LOGIN_URL;
    }

    /**
     * Vráti URL pre zadanú routu.
     */
    public function url(string $route): string
    {
        return $this->link->url($route);
    }

    /**
     * Vráti URL assetu (statický súbor).
     */
    public function asset(string $path): string
    {
        return $this->link->asset($path);
    }
}
