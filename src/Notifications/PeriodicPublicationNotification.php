<?php

namespace PeriodicNotice\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class PeriodicPublicationNotification extends Notification implements ShouldQueue
{
    use Queueable, HasPeriodicalNotificationMail;
    use HasDynamicChannels {
        HasDynamicChannels::via as dynamicVia;
    }

    public function __construct(
        protected Collection $entities
    ) {
    }

    public function via($notifiable)
    {
        if ($this->entities->count() > 0) {
            return $this->dynamicVia($notifiable);
        }

        return [];
    }

    public function toMail($notifiable)
    {
        $message = (new MailMessage);

        if (method_exists($this, 'mailSubject')
            && ($value = $this->mailSubject())) {
            $message->subject($value);
        }

        if (method_exists($this, 'mailGreeting')
            && ($value = $this->mailGreeting())) {
            $message->greeting($value);
        }

        if (method_exists($this, 'mailSalutation')
            && ($value = $this->mailSalutation())) {
            $message->salutation($value);
        }

        if (method_exists($this, 'mailContentBeforeList')) {
            $this->mailContentBeforeList($message);
        }

        $itemsCount = $this->entities->count();
        $counter    = 0;
        /** @var \PeriodicNotice\Contracts\SendableEntity $entity */
        foreach ($this->entities as $entity) {
            $counter++;

            if (method_exists($this, 'mailContentBeforeListItem')) {
                $this->mailContentBeforeListItem($message);
            }
            $message->line("[{$entity->notificationEntityTitle()}]({$entity->notificationEntityWebUrl()})");
            $message->line($entity->notificationEntityDescription());
            if (method_exists($this, 'mailContentListItemSeparator')
            && $counter < $itemsCount) {
                $this->mailContentListItemSeparator($message);
            }
            if (method_exists($this, 'mailContentAfterListItem')) {
                $this->mailContentAfterListItem($message);
            }
        }

        if (method_exists($this, 'mailContentAfterList')) {
            $this->mailContentAfterList($message);
        }

        return $message;
    }
}
