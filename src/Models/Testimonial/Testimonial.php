<?php

namespace HMsoft\Cms\Models\Testimonial;

use HMsoft\Cms\Models\GeneralModel;


/**
 * Class Testimonial
 *
 * @property int $id Primary
 * @property mixed $name
 * @property string $message
 * @property float $rate
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @package HMsoft\Cms\Models
 */
class Testimonial extends GeneralModel
{

    /**
     * Table Name In Database.
     */
    protected $table = "testimonials";

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>|bool
     */
    protected $guarded = ['id'];


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'rate' => 'float',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'publish_at' => 'datetime',
        ];
    }

    public function defineGlobalSearchBaseAttributes(): array
    {
        return [
            'name',
            'message',
        ];
    }
}
