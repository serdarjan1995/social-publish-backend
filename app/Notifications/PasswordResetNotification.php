<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetNotification extends Notification
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

    protected $user_name;
    protected $token;
    public $locale;

    public function __construct($user_name,$token,$locale)
    {
        $this->user_name = $user_name;
        $this->token = $token;
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
            $frontend_url = env('APP_URL_FRONTEND','http://localhost:8080')
                .env('APP_RESET_PASSWORD_PATH_FRONTEND','/auth/forgot');
            $url = $frontend_url.'/'.$this->token.'?email='.$notifiable->getEmailForPasswordReset();
        }
        $data = [
            'salutation' => trans('email.hello',['name' => $this->user_name],$this->locale),
            'reset_password_info' => trans('email.reset_password_info'),
            'reset_link' => $url,
            'button_label' => trans('email.reset_password'),
            'reset_password_expire' => trans('email.reset_password_expire',[
                'count' => config('auth.passwords.users.expire')
            ]),
            'reset_password_did_not_reset' => trans('email.reset_password_did_not_reset'),
            'ending_salutation' => trans('email.ending_salutation'),
            'button_link_trouble' => trans('email.button_link_trouble',
                ['button_name' => trans('email.reset_password')]),
            'rights_reserved' => trans('email.all_rights_reserved'),
        ];

        return (new MailMessage)
            ->subject(trans('email.reset_password_notification'))
            ->markdown('emails.password_reset',$data);
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
