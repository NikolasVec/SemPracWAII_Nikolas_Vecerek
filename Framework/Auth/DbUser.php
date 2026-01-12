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

    public function __construct(int $id, string $firstName, string $lastName, string $email)
    {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
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
}

