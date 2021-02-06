<?php

namespace App;

use App\Jobs\SendMail;
use App\Notifications\PasswordResetNotification;
use Illuminate\Support\Facades\App;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use \DateTimeInterface;
use GoldSpecDigital\LaravelEloquentUUID\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $dates  =[
        'created_at',
        'edited_at',
        'deleted_at'
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'surname',
        'image',
        'default_lang',
        'phone_number',
        'status',
        'email_verified',
        'phone_verified',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        /*self::created(function (User $user) {
            $registrationRole = config('custom.registration_default_role');

            if (!$user->roles()->get()->contains($registrationRole)) {
                $this->assignRole($user,$registrationRole);
            }
        });*/
    }


    /**
     * @inheritDoc
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * @inheritDoc
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function roles()
    {
        return $this->belongsToMany(
            'App\Model\Role',
            'user_roles',
            'user_id',
            'role_id'
        );
    }

    public function assignRole($user,$role){
        $user->roles()->attach($role);
    }

    public function setPasswordAttribute($value) {
        $this->attributes['password'] = Hash::make($value);
    }

    public function sendNotification($notification){
        $this->notify($notification);
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token){
        SendMail::dispatch($this,new PasswordResetNotification($this->name,$token,App::getLocale()));
    }

    /**
     * Determine if the user has verified their email address.
     *
     * @return bool
     */
    public function hasVerifiedEmail()
    {
        return $this->email_verified ? true:false;
    }

    /**
     * Mark the given user's email as verified.
     *
     * @return bool
     */
    public function markEmailAsVerified()
    {
        return $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
            'email_verified' => 1,
            'status' => 1,
        ])->save();
    }


    /**
     * Get the email address that should be used for verification.
     *
     * @return string
     */
    public function getEmailForVerification()
    {
        return $this->email;
    }

    public function posts()
    {
        return $this->hasMany('App\Model\Post');
    }

    public function accounts()
    {
        return $this->hasMany('App\Model\AccountManager\AccountManager');
    }
}
