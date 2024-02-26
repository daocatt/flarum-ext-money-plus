<?php

namespace Gtdxyz\Money\AutoModerator\Metric;

use Askvortsov\AutoModerator\Metric\MetricDriverInterface;
use Gtdxyz\Money\Event\MoneyUpdated;
use Flarum\User\User;

class Money implements MetricDriverInterface
{
    public function translationKey(): string
    {
        return 'gtdxyz-money-plus.admin.automoderator.metric_name';
    }

    public function extensionDependencies(): array
    {
        return ['gtdxyz-flarum-ext-money-plus'];
    }

    public function eventTriggers(): array
    {
        return [
            MoneyUpdated::class => function (MoneyUpdated $event) {
                return $event->user;
            },
        ];
    }

    public function getValue(User $user): int
    {
        return floor($user->money);
    }
}
