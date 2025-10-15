<?php

namespace HMsoft\Cms\Models\OurValue;

use Illuminate\Database\Eloquent\Model;

class OurValueTranslation extends Model
{
    protected $table = "our_value_translations";

    public $timestamps = false;


    protected $fillable = [
        'title',
        'description',
        'locale'
    ];
}
