<?php

namespace PeriodicNotice\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class PeriodicPublicationNotification extends Notification implements ShouldQueue
{
    use Queueable;
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

    protected function mailMessageBuilder($notifiable): MailMessageBuilder
    {
        $className = config('periodic-notice.defaults.mail_builder', MailMessageBuilder::class);
        if (!is_a($className, MailMessageBuilder::class, true)) {
            throw new \Exception('Wrong mail builder class');
        }

        return $className::make();
    }

    public function toMail($notifiable)
    {
        return $this->mailMessageBuilder($notifiable)
                    ->useEntries($this->entities)
                    ->build();
    }
}
