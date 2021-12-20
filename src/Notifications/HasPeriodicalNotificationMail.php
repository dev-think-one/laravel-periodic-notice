<?php

namespace PeriodicNotice\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;

trait HasPeriodicalNotificationMail
{
    protected function mailSubject(): ?string
    {
        return trans('periodic-notice::notification.mail.subject');
    }

    protected function mailGreeting(): ?string
    {
        return trans('periodic-notice::notification.mail.greeting');
    }

    protected function mailContentBeforeList(MailMessage $message)
    {
        $message->line(new HtmlString('<br>'));
    }

    protected function mailContentAfterList(MailMessage $message)
    {
        $message->line(new HtmlString('<br>'))
                ->action(
                    trans('periodic-notice::notification.mail.action_text'),
                    trans('periodic-notice::notification.mail.action_link')
                );
    }

    protected function mailContentListItemSeparator(MailMessage $message)
    {
        $message->line(new HtmlString('<br>'))
                ->line('---------------')
                ->line(new HtmlString('<br>'));
    }
}
