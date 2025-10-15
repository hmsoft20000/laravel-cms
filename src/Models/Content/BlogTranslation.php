<?php

namespace HMsoft\Cms\Models\Content;

use Illuminate\Database\Eloquent\Model;

class BlogTranslation extends Model
{

    protected $table = 'blog_translations';

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
