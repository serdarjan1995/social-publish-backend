<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class AgencyUserSetInitialPasswordNotification extends Notification
{
    use Queueable;

    /**
     * The callback that should be used to create the reset password URL.
     *
     * @var \Closure|null
     */
    public static $createUrlCallback;

    /**
     * The callback that should be used to build the mail message.
     *
     * @var \Closure|null
     */
    public static $toMailCallback;

    protected $parent_username;
    protected $user_name;
    public $locale;

    public function __construct($parent_username,$user_name,$locale)
    {
        $this->parent_username = $parent_username;
        $this->user_name = $user_name;
        $this->locale = $locale;
    }

    /**
     * Get the notification's channels.
     *
     * @param  mixed  $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->token);
        }

        if (static::$createUrlCallback) {
            $url = call_user_func(static::$createUrlCallback, $notifiable, $this->token);
        } else {
            $url = $this->verificationUrl($notifiable).'?email='.$notifiable->getEmailForPasswordReset();
        }
        $data = [
            'salutation' => trans('email.hello',['name' => $this->user_name],$this->locale),
            'reset_password_info' => trans('email.set_password_info'),
            'reset_link' => $url,
            'button_label' => trans('email.set_password'),
            'reset_password_expire' => trans('email.set_password_expire',[
                'count' => config('auth.passwords.users.expire')
            ]),
            'reset_password_did_not_reset' => trans('email.set_password_did_not_create'),
            'ending_salutation' => trans('email.ending_salutation'),
            'button_link_trouble' => trans('email.button_link_trouble',
                ['button_name' => trans('email.set_password')]),
            'rights_reserved' => trans('email.all_rights_reserved'),
        ];

        return (new MailMessage)
            ->subject(trans('email.set_password_notification',[
                'username' => $this->parent_username
            ]))
            ->markdown('emails.password_reset',$data);
    }

    /**
     * Get the verification URL for the given notifiable.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function verificationUrl($notifiable)
    {
        $url =  URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );

        $frontend_url = env('APP_URL_FRONTEND','http://localhost:8080')
            .env('APP_SET_PASSWORD_PATH_FRONTEND','/auth/setPassword');
        return Str::replaceFirst(URL::route('verification.verify'),
            $frontend_url, $url);
    }

    /**
     * Set a callback that should be used when creating the reset password button URL.
     *
     * @param  \Closure  $callback
     * @return void
     */
    public static function createUrlUsing($callback)
    {
        static::$createUrlCallback = $callback;
    }

    /**
     * Set a callback that should be used when building the notification mail message.
     *
     * @param  \Closure  $callback
     * @return void
     */
    public static function toMailUsing($callback)
    {
        static::$toMailCallback = $callback;
    }
}
