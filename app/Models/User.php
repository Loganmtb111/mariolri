<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    // Configuration pour la table Staff de la BDD Peach
    protected $table = 'staff';
    protected $primaryKey = 'staff_id';
    public $timestamps = false; // Si la table n'utilise pas created_at/updated_at standard
    const UPDATED_AT = 'last_update'; // Colonne utilisée pour les timestamps

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'username',
        'password',
        'address_id',
        'picture',
        'store_id',
        'active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            // Ne pas caster password car il est déjà hashé dans la base
        ];
    }

    /**
     * Get the name attribute (Laravel attend souvent un champ 'name').
     * On concatène first_name et last_name.
     *
     * @return string
     */
    public function getNameAttribute()
    {
        return trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));
    }

    /**
     * Get the password for authentication.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }
}
