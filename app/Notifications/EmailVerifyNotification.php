<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class EmailVerifyNotification extends Notification
{
    use Queueable;
    protected $user_name;
    public $locale;

    public function __construct($user_name,$locale)
    {
        $this->user_name = $user_name;
        $this->locale = $locale;
    }

    /**
     * The callback that should be used to build the mail message.
     *
     * @var \Closure|null
     */
    public static $toMailCallback;

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
        $verificationUrl = $this->verificationUrl($notifiable);
        $frontend_url = env('APP_URL_FRONTEND','http://localhost:8080')
            .env('APP_VERIFY_EMAIL_PATH_FRONTEND','/auth/verify');
        $verificationUrl = Str::replaceFirst(URL::route('verification.verify'),
            $frontend_url, $verificationUrl);

        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $verificationUrl);
        }
        $data = [
            'salutation' => trans('email.hello',['name' => $this->user_name],$this->locale),
            'verify_message' => trans('email.verify_email_click_to_verify'),
            'verification_url' => $verificationUrl,
            'button_label' => trans('email.verify_email_address'),
            'verify_email_did_not_create' => trans('email.verify_email_did_not_create'),
            'ending_salutation' => trans('email.ending_salutation'),
            'button_link_trouble' => trans('email.button_link_trouble',
                ['button_name' => trans('email.verify_email_address')]),
            'rights_reserved' => trans('email.all_rights_reserved'),
        ];

        return (new MailMessage())
            ->markdown('emails.verify_email',$data)
            ->subject(trans('email.verify_email_address'));
    }

    /**
     * Get the verification URL for the given notifiable.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
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
