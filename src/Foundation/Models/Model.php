<?php

namespace Laravelayers\Foundation\Models;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Laravelayers\Contracts\Foundation\Models\Model as ModelContract;

/**
 * The base class for models using Eloquent ORM.
 *
 * The Eloquent ORM included with Laravel provides a beautiful,
 * simple ActiveRecord implementation for working with your database.
 *
 * @use \Illuminate\Database\Eloquent\Model
 * * {@link https://laravel.com/docs/eloquent}
 */
class Model extends EloquentModel implements ModelContract
{
    use ModelTrait;
}
