<?php

namespace HMsoft\Cms\Models\Shared;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DownloadItemTranslation extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'download_item_translations';

    /**
     * @var bool
     */
    public $timestamps = false;

    protected $guarded = ['id'];
}
