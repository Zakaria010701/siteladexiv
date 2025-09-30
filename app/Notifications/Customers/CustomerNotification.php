<?php

namespace App\Notifications\Customers;

use App\Models\Customer;
use App\Settings\NotificationSetting;
use App\Support\TemplateSupport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use League\HTMLToMarkdown\HtmlConverter;

class CustomerNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $title;

    protected array $content;

    protected Customer $customer;

    protected ?Model $subject;

    protected NotificationSetting $settings;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $title, array $content, Customer $customer, ?Model $subject = null)
    {
        $this->title = $title;
        $this->content = $content;
        $this->subject = $subject?->withoutRelations();
        $this->customer = $customer->withoutRelations();
        $this->settings = app(NotificationSetting::class);
        $this->afterCommit();
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function getSubject(): ?Model
    {
        return $this->subject;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getMessage(): string
    {
        $message = TemplateSupport::make(customer: $this->customer)->formatTemplate($this->content);
        $converter = new HtmlConverter;
        $converter->getConfig()->setOption('strip_tags', true);

        return $converter->convert($message);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $templateSupport = TemplateSupport::make(customer: $this->customer);

        return (new MailMessage)
            ->subject($this->title)
            ->tag('customer')
            ->metadata('customer_id', strval($this->customer->id))
            ->view('mail.notification', [
                'subject' => $this->title,
                'content' => $templateSupport->formatTemplate($this->content),
                'header' => isset($this->settings->email_header) ? $templateSupport->formatTemplate($this->settings->email_header) : null,
                'footer' => isset($this->settings->email_footer) ? $templateSupport->formatTemplate($this->settings->email_footer) : null,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
