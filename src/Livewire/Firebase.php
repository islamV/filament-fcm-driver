<?php

namespace TomatoPHP\FilamentFcmDriver\Livewire;

use Detection\MobileDetect;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Livewire\Attributes\On;
use Livewire\Component;

class Firebase extends Component
{
    #[On('fcm-token')]
    public function fcmToken(string $token)
    {
        $detect = new MobileDetect;
        if (auth()->user()) {
            $user = auth()->user();
            $getToken = $user->setFCM($detect->isMobile() ? 'fcm-mobile' : 'fcm-web')->userTokensFcm()->where('provider', $detect->isMobile() ? 'fcm-mobile' : 'fcm-web')->first();
            if ($getToken) {
                $getToken->provider_token = $token;
                $getToken->save();
            } else {
                $user->setFCM($detect->isMobile() ? 'fcm-mobile' : 'fcm-web')->userTokensFcm()->create([
                    'provider' => $detect->isMobile() ? 'fcm-mobile' : 'fcm-web',
                    'provider_token' => $token,
                ]);
            }
        }
    }

    #[On('fcm-notification')]
    public function fcmNotification(mixed $data)
    {
        $actions = [];
        if (isset($data['data'])) {
            if (isset($data['data']['actions']) && is_object(json_decode($data['data']['actions']))) {
                foreach (json_decode($data['data']['actions']) as $action) {
                    $actions[] = Action::make($action->name)
                        ->color($action->color)
                        ->eventData($action->eventData)
                        ->icon($action->icon)
                        ->iconPosition($action->iconPosition)
                        ->iconSize($action->iconSize)
                        ->outlined($action->isOutlined)
                        ->disabled($action->isDisabled)
                        ->label($action->label)
                        ->url($action->url)
                        ->close($action->shouldClose)
                        ->size($action->size)
                        ->tooltip($action->tooltip)
                        ->view($action->view)
                        ->markAsUnread($action->shouldMarkAsUnRead ?? false)
                        ->markAsRead($action->shouldMarkAsRead ?? false);
                }
            }
        }

        if (isset($data['data']['sendToDatabase']) && $data['data']['sendToDatabase'] === '1') {
            Notification::make($data['data']['id'])
                ->title($data['data']['title'])
                ->actions($actions)
                ->body($data['data']['body'])
                ->icon($data['data']['icon'] ?? null)
                ->iconColor($data['data']['iconColor'] ?? null)
                ->color($data['data']['color'] ?? null)
                ->duration($data['data']['duration'] ?? null)
                ->send()
                ->sendToDatabase(auth()->user());
        } else {
            Notification::make($data['data']['id'])
                ->title($data['data']['title'])
                ->actions($actions)
                ->body($data['data']['body'])
                ->icon($data['data']['icon'] ?? null)
                ->iconColor($data['data']['iconColor'] ?? null)
                ->color($data['data']['color'] ?? null)
                ->duration($data['data']['duration'] ?? null)
                ->send();
        }
    }

    public function render()
    {
        return view('filament-fcm-driver::firebase-base');
    }
}
