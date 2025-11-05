<?php

namespace App\Models;

use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDateStartEnd;
use Orchid\Platform\Models\User as OrchidUser;

class User extends OrchidUser
{
    /**
     * Atributos que podem ser preenchidos em massa.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'permissions',
    ];

    /**
     * Atributos ocultos na serialização.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'permissions',
    ];

    /**
     * Atributos convertidos para tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'permissions'       => 'array',
        'email_verified_at' => 'datetime',
    ];

    /**
     * Campos permitidos para filtros (usados em listagens Orchid).
     *
     * @var array
     */
    protected $allowedFilters = [
        'id'         => Where::class,
        'name'       => Like::class,
        'email'      => Like::class,
        'updated_at' => WhereDateStartEnd::class,
        'created_at' => WhereDateStartEnd::class,
    ];

    /**
     * Campos permitidos para ordenação (sort).
     *
     * @var array
     */
    protected $allowedSorts = [
        'id',
        'name',
        'email',
        'updated_at',
        'created_at',
    ];

        public function leads()
    {
        return $this->hasMany(Lead::class);
        return $this->hasMany(\App\Models\Lead::class);
    }

}
