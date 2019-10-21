<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Bmatovu\OAuthNegotiator\Models\TokenInterface;
use Illuminate\Database\Eloquent\Model as BaseModel;

class Token extends BaseModel implements TokenInterface
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tokens';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'access_token',
        'refresh_token',
        'token_type',
        'expires_at',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'expires_at',
        'deleted_at',
    ];

    /**
     * Get access token.
     *
     * @return string
     */
    public function getAccessToken()
    {
        return $this->access_token;
    }

    /**
     * Get refresh token.
     *
     * @return string|null
     */
    public function getRefreshToken()
    {
        return $this->refresh_token;
    }

    /**
     * Get token type.
     *
     * @return string
     */
    public function getTokenType()
    {
        return $this->token_type;
    }

    /**
     * Get expires at.
     *
     * @return string Datatime
     */
    public function getExpiresAt()
    {
        return $this->expires_at;
    }

    /**
     * Determine if a token is expired.
     *
     * @return bool
     */
    public function isExpired()
    {
        if (is_null($this->expires_at)) {
            return false;
        }

        return $this->expires_at->isPast();
    }
}
