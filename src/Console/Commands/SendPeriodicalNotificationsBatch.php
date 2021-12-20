<?php

namespace PeriodicNotice\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use PeriodicNotice\Contracts\NotificationReceiver;

class SendPeriodicalNotificationsBatch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'periodic-notice:send:batch
    {type : Period type}
    {receiver : Receiver class}
    {--G|group=default : Group of notifications}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send periodic notice to receivers by period type.';

    /**
     * Execute the console command.
     *
     * @return int
     *
     */
    public function handle()
    {
        $type = $this->argument('type');
        /**
         * @var class-string<Model> $receiverClass
         */
        $receiverClass = $this->argument('receiver');
        $group         = $this->option('group');

        if (!$this->isValidReceiverClass($receiverClass)) {
            $originalReceiverClass = $receiverClass;
            $receiverClass         = Relation::getMorphedModel($receiverClass);
            if (!$this->isValidReceiverClass($receiverClass)) {
                $this->error("Please specify correct receiver. Currently specified: [{$originalReceiverClass}]");

                return 1;
            }
        }

        /**
         * @var \PeriodicNotice\Contracts\NotificationReceiver $receiverObject
         * @psalm-suppress UndefinedClass
         */
        $receiverObject         = new $receiverClass();
        $periodicNoticeDirector = $receiverObject->periodicNoticeDirector($group);

        $allowedPeriodTypes = $periodicNoticeDirector->allowedPeriodTypes();

        if (empty($type)
            || !in_array($type, $allowedPeriodTypes)
        ) {
            $allowedPeriodTypesString = implode(', ', $allowedPeriodTypes);
            $this->error("Please specify correct period type. Currently specified: [{$type}]. Allowed types: [{$allowedPeriodTypesString}]");

            return 2;
        }

        /** @psalm-suppress UndefinedClass */
        $receiverClass::query()->withNotificationPeriodType($type)
                      ->chunk(200, function ($receivers) use ($group) {
                          /** @var \PeriodicNotice\Contracts\NotificationReceiver $receiver */
                          foreach ($receivers as $receiver) {
                              $receiver->sendPeriodicalNotification($group);
                          }
                      });

        return 0;
    }

    protected function isValidReceiverClass($receiverClass): bool
    {
        return is_string($receiverClass)
               && is_a($receiverClass, NotificationReceiver::class, true)
               && is_a($receiverClass, Model::class, true);
    }
}
