<?php

namespace Laravelayers\Foundation\Models;

use Illuminate\Database\Eloquent\Relations\MorphPivot as EloquentMorphPivot;
use Laravelayers\Contracts\Foundation\Models\Model as ModelContract;

class MorphPivot extends EloquentMorphPivot implements ModelContract
{
    use ModelTrait;

    /**
     * Indicates if all mass assignment is enabled.
     *
     * @var bool
     */
    protected static $unguarded = true;
}
