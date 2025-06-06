<?php

namespace TomatoPHP\FilamentFcmDriver\Notifications;

use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class FCMNotificationService extends Notification
{
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        public string $message,
        public ?string $type = 'fcm-web',
        public ?string $title = null,
        public ?string $icon = null,
        public ?string $image = null,
        public ?string $url = null,
        public ?array $data = [],
        public ?bool $sendToDatabase = true,
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via(mixed $notifiable): array
    {
        return [FcmChannel::class];
    }

    public function toFcm($notifiable): FcmMessage
    {
        return
            new FcmMessage(
                name: $this->data['id'],
                topic: $this->type,
                data: [
                    'id' => $this->data['id'],
                    'actions' => json_encode($this->data['actions']),
                    'body' => $this->data['body'],
                    'color' => $this->data['color'],
                    'duration' => $this->data['duration'],
                    'icon' => $this->data['icon'],
                    'iconColor' => $this->data['iconColor'],
                    'status' => $this->data['status'],
                    'title' => $this->data['title'],
                    'view' => $this->data['view'],
                    'viewData' => json_encode($this->data['viewData']),
                    'data' => json_encode($this->data['data']),
                    'sendToDatabase' => (string) $this->sendToDatabase,
                ],
                custom: [
                    'android' => [
                        'notification' => [
                            'color' => '#0A0A0A',
                        ],
                        'fcm_options' => [
                            'analytics_label' => 'analytics',
                        ],
                    ],
                    'apns' => [
                        'fcm_options' => [
                            'analytics_label' => 'analytics',
                        ],
                    ],
                ],
                notification: new FcmNotification(
                    title: $this->title,
                    body: $this->message,
                    image: $this->image ?? null
                )
            );
    }
}
