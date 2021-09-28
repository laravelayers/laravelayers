<?php

namespace Laravelayers\Auth\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravelayers\Contracts\Auth\User as UserContract;
use Laravelayers\Foundation\Models\ModelTrait;
use Laravelayers\Foundation\Models\DatabaseNotification;

class User extends Authenticatable implements UserContract
{
    use ModelTrait,
        HasFactory,
        Notifiable;

    /**
     * The column name of the "login".
     *
     * @var string
     */
    protected $nameColumn = 'name';

    /**
     * The column name of the "email".
     *
     * @var string
     */
    protected $emailColumn = 'email';

    /**
     * The column name of the "email_verified_at".
     *
     * @var string
     */
    protected $emailVerifiedAtColumn = 'email_verified_at';

    /**
     * The column name of the "password".
     *
     * @var string
     */
    protected $passwordColumn = 'password';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'authIdentifierName', 'nameColumn', 'emailColumn', 'password', 'passwordColumn', 'remember_token', 'rememberTokenName'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'nameColumn', 'emailColumn', 'emailVerifiedAtColumn', 'passwordColumn', 'rememberTokenName', 'authIdentifierName'
    ];

    /**
     * Get the entity's notifications.
     */
    public function notifications()
    {
        return $this->morphMany(DatabaseNotification::class, 'notifiable')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Define the relationship to the user actions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userActions()
    {
        return $this->hasMany(UserAction::class, UserAction::getModel()->userColumn, $this->getKeyName());
    }

    /**
     * Get the email for the user.
     *
     * @return string
     */
    public function getEmailAttribute()
    {
        return $this->attributes[$this->getEmailColumnAttribute()];
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->attributes[$this->getPasswordColumnAttribute()];
    }

    /**
     * Get the column name for the "name".
     *
     * @return string
     */
    public function getNameColumnAttribute()
    {
        return $this->nameColumn;
    }

    /**
     * Get the column name for the "email".
     *
     * @return string
     */
    public function getEmailColumnAttribute()
    {
        return $this->emailColumn;
    }

    /**
     * Set the email verified date.
     *
     * @param string $value
     * @return void
     */
    public function setEmailVerifiedAtAttribute($value)
    {
        $this->attributes[$this->getEmailVerifiedAtColumnAttribute()] = $value;
    }

    /**
     * Get the column name for the "email_verified_at".
     *
     * @return string
     */
    public function getEmailVerifiedAtColumnAttribute()
    {
        return $this->emailVerifiedAtColumn;
    }

    /**
     * Get the column name for the "password".
     *
     * @return string
     */
    public function getPasswordColumnAttribute()
    {
        return $this->passwordColumn;
    }

    /**
     * Get the column name for the "remember token".
     *
     * @return string
     */
    public function getRememberTokenNameAttribute()
    {
        return $this->getRememberTokenName();
    }

    /**
     * Get the name for the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierNameAttribute()
    {
        return $this->getAuthIdentifierName();
    }

    /**
     * Add a where clause so that the user action is equal to the specified.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $action
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereAction($query, $action)
    {
        return $query->whereHas('userActions', function($query) use($action) {
            $query->whereKey($action);
        });
    }

    /**
     * Add a where clause so that the user action is not equal to the specified.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $action
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereActionNot($query, $action)
    {
        return $query->whereDoesntHave('userActions', function($query) use($action) {
            $query->whereKey($action);
        });
    }

    /**
     * Add a where clause so that the credentials for the user are equal to the specified.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $column
     * @param string $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereCredentials($query, $column, $value)
    {
        return $query->where($column, $value);
    }

    /**
     * Search by default.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search)
    {
        return $query->whereKey($search);
    }

    /**
     * Search by name.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearchByName($query, $search)
    {
        return $query->where($this->nameColumn, 'like', "{$search}%");
    }

    /**
     * Search by email.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearchByEmail($query, $search)
    {
        return $query->where($this->emailColumn, 'like', "{$search}%");
    }

    /**
     * Sort by column name.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $direction
     * @param string|null $column
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSort($query, $direction = 'desc', $column = null)
    {
        return $query->orderBy($column ?: $this->getQualifiedKeyName(), $direction);
    }

    /**
     * Sort by name.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $direction
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortByName($query, $direction = 'desc')
    {
        return $query->orderBy($this->nameColumn, $direction);
    }

    /**
     * Sort by email.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $direction
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortByEmail($query, $direction = 'desc')
    {
        return $query->orderBy($this->emailColumn, $direction);
    }
}
