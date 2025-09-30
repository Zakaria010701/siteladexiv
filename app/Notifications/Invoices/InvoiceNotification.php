<?php

namespace App\Notifications\Invoices;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\NotificationTemplate;
use App\Settings\NotificationSetting;
use App\Support\TemplateSupport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use League\HTMLToMarkdown\HtmlConverter;
use NotificationChannels\SMS77\SMS77Channel;
use NotificationChannels\SMS77\SMS77Message;

abstract class InvoiceNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Invoice $invoice;

    protected NotificationSetting $settings;

    protected ?NotificationTemplate $template;

    /**
     * Create a new notification instance.
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice->withoutRelations();
        $this->settings = app(NotificationSetting::class);
        $this->template = $this->getNotificationTemplate();
        $this->afterCommit();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        if (is_null($this->template)) {
            return [];
        }

        $via = [];

        if ($this->template->enable_mail) {
            $via[] = 'mail';
        }

        if ($this->template->enable_sms) {
            $via[] = SMS77Channel::class;
        }

        return $via;
    }

    /**
     * Determine if the notification should be sent.
     */
    public function shouldSend(object $notifiable, string $channel): bool
    {
        if ($this->settings->disable_notifications) {
            return false;
        }

        if (is_null($this->template)) {
            return false;
        }

        if (! $this->template->is_enabled) {
            return false;
        }

        if ($notifiable instanceof Customer) {
            if ($notifiable->hasOption('no_notifications')) {
                return false;
            }
        }

        return true;
    }

    public function getInvoice(): Invoice
    {
        return $this->invoice;
    }

    public function getCustomer(): ?Customer
    {
        if ($this->invoice->recipient instanceof Customer) {
            return $this->invoice->recipient;
        }

        return null;
    }

    public function getMessage(): string
    {
        $message = TemplateSupport::make(invoice: $this->invoice)->formatTemplateText($this->template->content);
        $converter = new HtmlConverter;
        $converter->getConfig()->setOption('strip_tags', true);

        return $converter->convert($message);
    }

    public function getTitle(): string
    {
        return $this->template->subject;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $templateSupport = TemplateSupport::make(invoice: $this->invoice);

        return (new MailMessage)
            ->subject($this->template->subject)
            ->tag('invoice')
            ->attachData(Pdf::loadView('pdf.invoice', ['invoice' => $this->invoice])->output(), "{$this->invoice->invoice_number}.pdf", [
                'mime' => 'application/pdf',
            ])
            ->metadata('invoice_id', strval($this->invoice->id))
            ->view('mail.notification', [
                'subject' => $this->template->subject,
                'content' => $templateSupport->formatTemplate($this->template->content),
                'header' => isset($this->settings->email_header) ? $templateSupport->formatTemplate($this->settings->email_header) : null,
                'footer' => isset($this->settings->email_footer) ? $templateSupport->formatTemplate($this->settings->email_footer) : null,
            ]);
    }

    abstract protected function getNotificationTemplate(): ?NotificationTemplate;

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

    public function toSms77(object $notifiable): SMS77Message
    {
        return (new SMS77Message)
            ->to($notifiable->routeNotificationFor('sms77'))
            ->content(TemplateSupport::make(invoice: $this->invoice)->formatTemplateText($this->template->sms_content))
            ->from($this->settings->sms_77_from)
            ->unicode();
    }
}
