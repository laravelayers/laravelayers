<?php

namespace Laravelayers\Foundation\Models;

use Illuminate\Database\Eloquent\Relations\Pivot as EloquentPivot;
use Laravelayers\Contracts\Foundation\Models\Model as ModelContract;

class Pivot extends EloquentPivot implements ModelContract
{
    use ModelTrait;
}
