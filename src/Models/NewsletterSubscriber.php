<?php

namespace HMsoft\Cms\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;


/**
 * Class NewsletterSubscriber
 *
 * @property int $id Primary
 * @property mixed $email
 * @property mixed $token
 * @property \Carbon\Carbon $verified_at
 * @property mixed $is_active
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package HMsoft\Cms\Models
 */
class NewsletterSubscriber extends GeneralModel
{

    /**
     * Table Name In Database.
     */
    protected $table = "subscribers";

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>|bool
     */
    protected $guarded = [];


    public function scopeActive(Builder $query)
    {
        $query->where('is_active', true);
    }

    public function defineGlobalSearchBaseAttributes(): array
    {
        return [
            'email',
        ];
    }
}
