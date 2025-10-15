<?php

namespace HMsoft\Cms\Models\Content;

use Illuminate\Database\Eloquent\Model;

class PortfolioTranslation extends Model
{

    protected $table = 'portfolio_translations';

    public $timestamps = false;

    protected $fillable = [
        'locale',
        'title',
        'slug',
        'short_content',
        'content',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];
}
