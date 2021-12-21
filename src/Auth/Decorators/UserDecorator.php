<?php

namespace Laravelayers\Auth\Decorators;

use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Notifications\RoutesNotifications;
use Illuminate\Support\Facades\Hash;
use Laravelayers\Form\Decorators\Images;
use Laravelayers\Foundation\Decorators\DataDecorator;

class UserDecorator extends DataDecorator implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract,
    MustVerifyEmailContract
{
    use Authenticatable,
        Authorizable,
        CanResetPassword,
        Images,
        RoutesNotifications,
        MustVerifyEmail;

    /**
     * The column name of the "name".
     *
     * @var string
     */
    protected $nameColumn;

    /**
     * The column name of the "email".
     *
     * @var string
     */
    protected $emailColumn;

    /**
     * The column name of the "email_verified_at".
     *
     * @var string
     */
    protected $emailVerifiedAtColumn;

    /**
     * The column name of the "password".
     *
     * @var string
     */
    protected $passwordColumn;

    /**
     * The column name of the "avatar".
     *
     * @var string
     */
    protected $avatarColumn;

    /**
     * The parameters for the avatar image.
     *
     * @var array
     */
    protected static $avatarParams = [
        'image' => 'jpg',
        'size' => '',
        'width' => 200,
        'height' => 200,
        'quality' => 90,
        'disk' => 'public',
        'path' => 'images/users',
        'prefix' => null
    ];

    /**
     * The column name of the "remember token".
     *
     * @var string
     */
    protected $rememberTokenName;

    /**
     * The name of the unique identifier for the user.
     *
     * @var string
     */
    protected $authIdentifierName;

    /**
     * Get the login.
     *
     * @return string
     */
    public function getName()
    {
        return $this->getNameColumn() ? $this->get($this->getNameColumn()) : '';
    }

    /**
     * Set the name.
     *
     * @param string $value
     * @param array $element
     * @return array
     */
    public function setName($value, $element = [])
    {
        $this->put($this->getNameColumn(), $value);

        return array_merge($element, [
            'value' => $this->getName()
        ]);
    }

    /**
     * Get the column name of the "login".
     *
     * @return string
     */
    public function getNameColumn()
    {
        return $this->nameColumn;
    }

    /**
     * Get the email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->getEmailColumn() ? $this->get($this->getEmailColumn()) : '';
    }

    /**
     * Set the email.
     *
     * @param string $value
     * @param array $element
     * @return array
     */
    public function setEmail($value, $element = [])
    {
        $this->put($this->getEmailColumn(), $value);

        return array_merge($element, [
            'value' => $this->getEmail()
        ]);
    }

    /**
     * Get the column name of the "email".
     *
     * @return string
     */
    public function getEmailColumn()
    {
        return $this->emailColumn;
    }

    /**
     * Get the email verified date.
     *
     * @return string|null
     */
    public function getEmailVerifiedAt()
    {
        return $this->getEmailVerifiedAtColumn() ? $this->get($this->getEmailVerifiedAtColumn()) : null;
    }

    /**
     * Set the email verified date.
     *
     * @param string $value
     * @return string
     */
    public function setEmailVerifiedAt($value)
    {
        $this->put($this->getEmailVerifiedAtColumn(), $value);

        return $this;
    }

    /**
     * Get the column name of the "email_verified_at".
     *
     * @return string
     */
    public function getEmailVerifiedAtColumn()
    {
        return $this->emailVerifiedAtColumn;
    }

    /**
     * Get the password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->get($this->getPasswordColumn(), '');
    }

    /**
     * Set the password.
     *
     * @param string $value
     * @param array $element
     * @return array
     */
    public function setPassword($value, $element = [])
    {
        if ($value) {
            $this->put($this->getPasswordColumn(), Hash::make($value));
        }

        return array_merge($element, [
            'value' => $value
        ]);
    }

    /**
     * Get the column name of the "password".
     *
     * @return string
     */
    public function getPasswordColumn()
    {
        return $this->passwordColumn;
    }

    /**
     * Get the avatar.
     *
     * @return string
     */
    public function getAvatar()
    {
        return $this->getImageSize(
            $this->get($this->avatarColumn ?: '', $this->getAvatarParams('image')),
            $this->getAvatarParams('size'),
            $this->getAvatarParams('disk'),
            $this->getAvatarParams('path'),
            $this->getAvatarParams('prefix')
        );
    }

    /**
     * Set the avatar.
     *
     * @param \Illuminate\Http\UploadedFile $value
     * @return array
     */
    public function setAvatar($value)
    {
        $params = static::getAvatarParams();

        $images = $this->setUploadedImages($value, $params['disk'], $params['path'], $params['prefix'])
            ->setImageExtension($params['image'])
            ->setImageSize($params['size'], $params['width'], $params['height'], $params['quality']);

        return $images->getImageUrls();
    }

    /**
     * Get the parameters for the avatar image.
     *
     * @param string $key
     * @return array|string
     */
    public static function getAvatarParams($key = '')
    {
        return array_key_exists($key, static::$avatarParams) ? static::$avatarParams[$key] : static::$avatarParams;
    }

    /**
     * Set the parameters for the avatar image.
     *
     * @param string $extension
     * @param string $size
     * @param int|null $width
     * @param int|null $height
     * @param int $quality
     * @param string $disk
     * @param string $path
     * @param null $prefix
     * @return array
     */
    public static function setAvatarParams($extension, $size, $width, $height, $quality, $disk, $path = '', $prefix = null)
    {
        return static::$avatarParams = [
            'image' => !is_null($extension) ?: 'jpg',
            'size' => !is_null($size) ? $size : '',
            'width' => !is_null($width) ? $width : $width,
            'height' => !is_null($height) ? $height : $height,
            'quality' => $quality,
            'disk' => $disk ?: 'public',
            'path' => !is_null($path) ? $path : 'images/users',
            'prefix' => $prefix
        ];
    }

    /**
     * Get the user actions.
     *
     * @return UserActionDecorator|mixed
     */
    public function getUserActions()
    {
        return UserActionDecorator::make($this->getRelation('userActions') ?: collect());
    }
}
