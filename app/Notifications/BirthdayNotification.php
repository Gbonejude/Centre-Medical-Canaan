<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BirthdayNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $person;

    protected $type;

    /**
     * Create a new notification instance.
     */
    public function __construct($person, string $type)
    {
        $this->person = $person;
        $this->type = $type;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        if ($this->type === 'employee') {
            return $this->getEmployeeBirthdayMail();
        } else {
            return $this->getCustomerBirthdayMail();
        }
    }

    /**
     * Birthday email for employees
     */
    private function getEmployeeBirthdayMail(): MailMessage
    {
        return (new MailMessage)
            ->subject("Happy Birthday {$this->person->firstname}!")
            ->markdown('mail.birthday-employee', [
                'person' => $this->person,
                'type' => $this->type,
            ]);
    }

    /**
     * Birthday email for customers
     */
    private function getCustomerBirthdayMail(): MailMessage
    {
        return (new MailMessage)
            ->subject("Happy Birthday {$this->person->firstname}!")
            ->markdown('mail.birthday-customer', [
                'person' => $this->person,
                'type' => $this->type,
            ]);
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        $birthdayDate = Carbon::parse($this->person->birthday)->format('d/m');
        $appName = config('app.name');
        if ($this->type === 'employee') {
            $message = '🎉 Happy Birthday! Have a wonderful day! 🎂';
            $title = 'Happy Birthday! 🎉';
            $url = route('dashboard.index', [], false);
        } elseif ($this->type === 'customer') {
            $message = '🎉 Happy Birthday! Thanks for being with us! 🎂';
            $title = 'Happy Birthday! 🎉';
            $url = route('dashboard.index', [], false);
        }

        return [
            'type' => 'birthday_wish',
            'person_type' => $this->type,
            'person_id' => $this->person->id,
            'title' => $title,
            'sender' => $appName,
            'message' => $message,
            'url' => $url,
            'birthday_date' => $birthdayDate,
            'created_at' => now(),
        ];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
