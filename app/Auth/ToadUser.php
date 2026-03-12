<?php

namespace App\Auth;

use Illuminate\Auth\GenericUser;

class ToadUser extends GenericUser
{
    public function getRememberToken(): string
    {
        return '';
    }

    public function setRememberToken($value): void
    {
        // pas de BDD, rien à persister
    }

    public function getRememberTokenName(): string
    {
        return 'remember_token';
    }

    public function __get($key)
    {
        if ($key === 'remember_token') {
            return null;
        }

        if ($key === 'name' && !isset($this->attributes['name'])) {
            $first = $this->attributes['first_name'] ?? '';
            $last  = $this->attributes['last_name']  ?? '';
            return trim("$first $last") ?: ($this->attributes['email'] ?? '');
        }

        return parent::__get($key);
    }
}