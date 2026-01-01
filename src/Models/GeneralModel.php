<?php

namespace HMsoft\Cms\Models;

use HMsoft\Cms\Interfaces\AutoFilterable;
use HMsoft\Cms\Support\ResolvesExtendedModel;
use HMsoft\Cms\Traits\General\ClearsResponseCache;
use HMsoft\Cms\Traits\General\CURDTrait;
use Illuminate\Database\Eloquent\Model;
use HMsoft\Cms\Traits\General\IsAutoFilterable;

class GeneralModel extends Model implements AutoFilterable
{
    use ResolvesExtendedModel;
    // use CURDTrait;
    use IsAutoFilterable;
    use ClearsResponseCache;
}
