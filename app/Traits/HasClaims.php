<?php

namespace App\Traits;

use Exception;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

trait HasClaims
{
    /**
     * The claims for the token.
     *
     * @var array
     */
    protected $claims = [];

    /**
     * Get the claims for the token.
     *
     * @return array
     */
    public function getClaims(): array
    {
        return $this->claims;
    }

    /**
     * Summary of setClaims
     *
     * @return void
     */
    public function setClaims(): void
    {
        try {
            $token = JWTAuth::getToken();

            $this->claims = JWTAuth::getJWTProvider()->decode($token);

        } catch (\Throwable $th) {
            //
        }
    }

}

