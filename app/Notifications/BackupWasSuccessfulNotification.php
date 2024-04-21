<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Spatie\Backup\Events\BackupWasSuccessful;
use Spatie\Backup\Notifications\BaseNotification;

class BackupWasSuccessfulNotification extends BaseNotification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public BackupWasSuccessful $event,
    ) {
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(): MailMessage
    {
        $latestBackupPath = $this->backupDestinationProperties()->get('latestBackupPath');
        $mailMessage = (new MailMessage())
            ->from(
                config(
                    'backup.notifications.mail.from.address',
                    config('mail.from.address')
                ),
                config(
                    'backup.notifications.mail.from.name',
                    config('mail.from.name')
                )
            )
            ->subject(
                trans(
                    'backup::notifications.backup_successful_subject',
                    ['application_name' => $this->applicationName()]
                )
            )
            ->line(
                trans(
                    'backup::notifications.backup_successful_body',
                    ['application_name' => $this->applicationName(), 'disk_name' => $this->diskName()]
                )
            );

        $this->backupDestinationProperties()->each(function ($value, $name) use ($mailMessage) {
            $mailMessage->line("{$name}: $value");
        });

        //handle attachment
        $backupDisk = $this->backupDestinationProperties()->get('Disk');
        $backupFolder = $this->backupDestinationProperties()->get('Backup name');
        $latestBackupDate = $this->backupDestinationProperties()->get('Newest backup date');
        $carbonDate = Carbon::createFromFormat('Y/m/d H:i:s', $latestBackupDate);
        $backupFormat = $carbonDate->format('Y-m-d-H-i-s');
        $latestBackupName = "{$backupFolder}/{$backupFormat}.zip";
        $latestBackupPath = Storage::disk($backupDisk)->path($latestBackupName);
        $mailMessage->attach($latestBackupPath);

        return $mailMessage;
    }
}
