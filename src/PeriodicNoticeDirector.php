<?php

namespace PeriodicNotice;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class PeriodicNoticeDirector
{
    protected ?string $periodType = null;

    protected array $periodTypesMap = [];

    protected ?\Closure $queryToGetEntries = null;

    protected \Closure|string|null $notificationClass = null;

    public function __construct(
        protected string $group = 'default'
    ) {
    }

    public static function defaults(...$attributes): static
    {
        return new static(...$attributes);
    }

    public function useQueryToGetEntries(\Closure|string $callback): static
    {
        if (is_callable($callback)) {
            $this->queryToGetEntries = $callback;
        } elseif (is_a($callback, Model::class, true)) {
            $this->queryToGetEntries = fn () => $callback::query();
        }

        return $this;
    }

    public function usePeriodType(?string $periodType): static
    {
        $this->periodType = $periodType;

        return $this;
    }

    public function usePeriodTypesMap(array $periodTypesMap): static
    {
        $this->periodTypesMap = $periodTypesMap;

        return $this;
    }

    public function useNotificationClass(\Closure|string $callback): static
    {
        $this->notificationClass = $callback;

        return $this;
    }

    public function periodTypesMap(): array
    {
        return $this->periodTypesMap ?? [];
    }

    public function allowedPeriodTypes(): array
    {
        return array_keys($this->periodTypesMap());
    }

    public function periodStartDateTime(): ?\DateTimeInterface
    {
        if ($this->periodType
            && ($periodInSeconds = $this->periodTypesMap[$this->periodType] ?? 0)) {
            return Carbon::now()->subSeconds($periodInSeconds);
        }

        return null;
    }

    public function notificationClass(): string
    {
        if (is_callable($this->notificationClass)) {
            return call_user_func_array($this->notificationClass, [$this->periodType, $this->group]);
        }

        if (is_a($this->notificationClass, \Illuminate\Notifications\Notification::class, true)) {
            return $this->notificationClass;
        }

        return config('periodic-notice.defaults.notification');
    }

    public function findEntries(Model $user)
    {
        $publicationStartDate = $this->periodStartDateTime();

        if (!$publicationStartDate || !is_callable($this->queryToGetEntries)) {
            return Collection::make();
        }

        $query = call_user_func_array($this->queryToGetEntries, [$this->periodType, $this->group])
            ->doesntSentInPeriodicNotice($user, $this->group)
            ->releasedAfter($publicationStartDate, $this->group);

        return $query->get();
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Model|\PeriodicNotice\Contracts\NotificationReceiver  $user
     * @return void
     */
    public function sendPeriodicalNotification(Model $user)
    {
        $entries = $this->findEntries($user);

        if ($entries->count() > 0) {
            $notificationClass = $this->notificationClass();
            $user->notify(new $notificationClass($entries));

            $data = [
                'group'   => $this->group,
                'sent_at' => Carbon::now(),
                'meta'    => [
                    'type'         => $this->periodType,
                    'notification' => $notificationClass,
                ],
            ];

            /** @var \Illuminate\Database\Eloquent\Model $entry */
            foreach ($entries as $entry) {
                $user->periodicSentEntries()->create(array_merge([
                    'sendable_type' => $entry->getMorphClass(),
                    'sendable_id'   => $entry->getKey(),
                ], $data));
            }
        }
    }
}
