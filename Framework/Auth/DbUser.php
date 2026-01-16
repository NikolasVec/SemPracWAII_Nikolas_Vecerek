<?php

namespace Framework\Auth;

use Framework\Core\IIdentity;

/**
 * Class DbUser
 * Represents a user loaded from the database and implements IIdentity.
 */
class DbUser implements IIdentity
{
    private int $id;
    private string $firstName;
    private string $lastName;
    private string $email;
    private bool $admin;
    private float $kilometres;
    private int $beers;

    public function __construct(int $id, string $firstName, string $lastName, string $email, bool $admin = false, float $kilometres = 0.0, int $beers = 0)
    {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->admin = $admin;
        $this->kilometres = $kilometres;
        $this->beers = $beers;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Returns a display name for the identity (first + last name, or email as fallback).
     */
    public function getName(): string
    {
        $full = trim($this->firstName . ' ' . $this->lastName);
        return $full !== '' ? $full : $this->email;
    }

    /**
     * Returns true if the user has admin privileges.
     */
    public function isAdmin(): bool
    {
        return $this->admin;
    }

    public function getKilometres(): float
    {
        // In case the object was restored from an older session and the typed property
        // wasn't initialized, guard with isset to avoid "must not be accessed before initialization".
        return isset($this->kilometres) ? $this->kilometres : 0.0;
    }

    public function getBeers(): int
    {
        return isset($this->beers) ? $this->beers : 0;
    }

    /**
     * Ensure object serializes in a stable way for session storage.
     * This avoids uninitialized typed properties after restoring from session.
     */
    public function __serialize(): array
    {
        return [
            'id' => $this->id,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'email' => $this->email,
            'admin' => $this->admin,
            'kilometres' => isset($this->kilometres) ? (float)$this->kilometres : 0.0,
            'beers' => $this->beers ?? 0,
        ];
    }

    public function __unserialize(array $data): void
    {
        $this->id = (int)($data['id'] ?? 0);
        $this->firstName = (string)($data['firstName'] ?? '');
        $this->lastName = (string)($data['lastName'] ?? '');
        $this->email = (string)($data['email'] ?? '');
        $this->admin = (bool)($data['admin'] ?? false);
        $this->kilometres = isset($data['kilometres']) ? (float)$data['kilometres'] : 0.0;
        $this->beers = isset($data['beers']) ? (int)$data['beers'] : 0;
    }
}
