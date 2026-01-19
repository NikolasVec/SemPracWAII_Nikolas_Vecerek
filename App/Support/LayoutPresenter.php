<?php

namespace App\Support;

use Framework\Auth\AppUser;
use Framework\Support\LinkGenerator;

class LayoutPresenter
{
    private AppUser $user;
    private LinkGenerator $link;

    public function __construct(AppUser $user, LinkGenerator $link)
    {
        $this->user = $user;
        $this->link = $link;
    }

    public function isLoggedIn(): bool
    {
        return $this->user->isLoggedIn();
    }

    public function isAdmin(): bool
    {
        // keep original checks but encapsulated here
        return $this->user->isLoggedIn()
            && $this->user->getIdentity() !== null
            && method_exists($this->user->getIdentity(), 'isAdmin')
            && $this->user->isAdmin();
    }

    public function adminUrl(): string
    {
        return $this->link->url('admin.index');
    }

    public function profileUrl(): string
    {
        return $this->link->url('home.profile');
    }

    public function loginUrl(): string
    {
        return \App\Configuration::LOGIN_URL;
    }

    public function url(string $route): string
    {
        return $this->link->url($route);
    }

    public function asset(string $path): string
    {
        return $this->link->asset($path);
    }
}

