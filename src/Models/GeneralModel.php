<?php

namespace HMsoft\Cms\Models;

use HMsoft\Cms\Interfaces\AutoFilterable;
use HMsoft\Cms\Traits\General\CURDTrait;
use HMsoft\Cms\Traits\Relations\CustomRelationsTrait;
use Illuminate\Database\Eloquent\Model;
use HMsoft\Cms\Traits\General\IsAutoFilterable;

class GeneralModel extends Model implements AutoFilterable
{
    use CURDTrait;
    use IsAutoFilterable;
    use CustomRelationsTrait;
}
