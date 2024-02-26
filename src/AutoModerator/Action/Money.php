<?php

namespace Gtdxyz\Money\AutoModerator\Action;

use Askvortsov\AutoModerator\Action\ActionDriverInterface;
use Gtdxyz\Money\Event\MoneyUpdated;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Contracts\Support\MessageBag;
use Flarum\User\User;

class Money implements ActionDriverInterface
{
    public function translationKey(): string
    {
        return 'gtdxyz-money-plus.admin.automoderator.action_name';
    }

    public function availableSettings(): array
    {
        return [
            'money' => 'gtdxyz-money-plus.admin.automoderator.metric_name',
        ];
    }

    public function validateSettings(array $settings, Factory $validator): MessageBag
    {
        return $validator->make($settings, [
            'money' => 'required|numeric',
        ])->errors();
    }

    public function extensionDependencies(): array
    {
        return ['gtdxyz-flarum-ext-money-plus'];
    }

    public function execute(User $user, array $settings = [], User $lastEditedBy = null)
    {
        $money = $settings['money'] ?? 0;
        $money = $money;

        $user->money = bcadd($user->money,$money);
        $user->save();

        resolve('events')->dispatch(new MoneyUpdated($user));
    }
}
