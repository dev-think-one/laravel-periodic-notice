<?php

namespace PeriodicNotice\Notifications;

trait HasDynamicChannels
{
    protected array $defaultChannels = [ 'mail' ];

    protected ?array $channels = null;

    public function setChannels(?array $channels = null): self
    {
        $this->channels = $channels;

        return $this;
    }


    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function via($notifiable)
    {
        return $this->channels ?? $this->defaultChannels;
    }
}
