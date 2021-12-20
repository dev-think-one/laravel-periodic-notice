<?php

namespace PeriodicNotice\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;

class MailMessageBuilder
{
    protected MailMessage $mailMessage;

    protected Collection $entities;

    public function __construct(?MailMessage $mailMessage = null)
    {
        $this->mailMessage = $mailMessage ?? new MailMessage();
    }

    public static function make(...$args): static
    {
        return new static(...$args);
    }

    public function build(): MailMessage
    {
        $this->mailSubject()
             ->mailGreeting()
             ->mailSalutation()
             ->mailContentBeforeList();

        $itemsCount = $this->entities->count();
        $counter    = 0;
        /** @var \PeriodicNotice\Contracts\SendableEntity $entity */
        foreach ($this->entities as $entity) {
            $counter++;

            $this->mailContentBeforeListItem($entity, $counter);

            $this->mailMessage->line("[{$entity->notificationEntityTitle()}]({$entity->notificationEntityWebUrl()})")
                              ->line($entity->notificationEntityDescription());

            if ($counter < $itemsCount) {
                $this->mailContentListItemSeparator($entity, $counter);
            }
            $this->mailContentAfterListItem($entity, $counter);
        }

        if (method_exists($this, 'mailContentAfterList')) {
            $this->mailContentAfterList();
        }

        return $this->mailMessage;
    }

    public function useEntries(Collection $entities): static
    {
        $this->entities = $entities;

        return $this;
    }

    protected function mailSubject(): static
    {
        $this->mailMessage->subject(trans('periodic-notice::notification.mail.subject'));

        return $this;
    }

    protected function mailGreeting(): static
    {
        $this->mailMessage->greeting(trans('periodic-notice::notification.mail.greeting'));

        return $this;
    }

    protected function mailSalutation(): static
    {
        return $this;
    }

    protected function mailContentBeforeList(): static
    {
        $this->mailMessage->line(new HtmlString('<br>'));

        return $this;
    }

    protected function mailContentAfterList(): static
    {
        $this->mailMessage->line(new HtmlString('<br>'))
                          ->action(
                              trans('periodic-notice::notification.mail.action_text'),
                              trans('periodic-notice::notification.mail.action_link')
                          );

        return $this;
    }

    protected function mailContentBeforeListItem($entity, int $itemNumber): static
    {
        return $this;
    }

    protected function mailContentListItemSeparator($entity, int $itemNumber): static
    {
        $this->mailMessage->line(new HtmlString('<br>'))
                          ->line('---------------')
                          ->line(new HtmlString('<br>'));

        return $this;
    }

    protected function mailContentAfterListItem($entity, int $itemNumber): static
    {
        return $this;
    }
}
