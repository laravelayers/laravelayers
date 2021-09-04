<?php

namespace Laravelayers\Foundation\Models;

use Illuminate\Notifications\DatabaseNotification as BaseDatabaseNotification;
use Laravelayers\Contracts\Foundation\Models\Model as ModelContract;

class DatabaseNotification extends BaseDatabaseNotification implements ModelContract
{
    use ModelTrait;
}
