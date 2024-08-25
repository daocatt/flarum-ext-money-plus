<?php

namespace Gtdxyz\Money;

use Flarum\Extend;
use Flarum\Api\Serializer\UserSerializer;
use Flarum\User\Event\Registered;
use Flarum\Post\Event\Posted;
use Flarum\Post\Event\Saving as PostSaving;
use Flarum\Post\Event\Restored as PostRestored;
use Flarum\Post\Event\Hidden as PostHidden;
use Flarum\Post\Event\Deleted as PostDeleted;
use Flarum\Discussion\Event\Started as DiscussionStarted;
use Flarum\Discussion\Event\Restored as DiscussionRestored;
use Flarum\Discussion\Event\Hidden as DiscussionHidden;
use Flarum\Discussion\Event\Deleted as DiscussionDeleted;
use Flarum\User\Event\Saving;
use Flarum\Likes\Event\PostWasLiked;
use Flarum\Likes\Event\PostWasUnliked;

use Gtdxyz\Money\History\Api\Controller\ListUserMoneyHistoryController;
use Gtdxyz\Money\History\Event\MoneyAllHistoryEvent;
use Gtdxyz\Money\History\Event\MoneyHistoryEvent;

use Gtdxyz\Money\History\Listeners\MoneyAllHistoryListeners;
use Gtdxyz\Money\History\Listeners\MoneyHistoryListeners;


use Gtdxyz\Money\History\Middleware\DistributeAllHistoryMiddleware;
use Gtdxyz\Money\History\Middleware\MoneyRewardsMiddleware;
use Gtdxyz\Money\History\Middleware\TransferHistoryMiddleware;
use Gtdxyz\Money\History\Middleware\UserSaveMiddleware;

$extend = [
    (new Extend\Frontend('forum'))
        ->js(__DIR__ . '/js/dist/forum.js')
        ->css(__DIR__.'/resources/less/forum.less')
        ->route('/u/{username}/money/history', 'gtdxyz-money-plus.history.forum.nav'),

    (new Extend\Frontend('admin'))
        ->js(__DIR__ . '/js/dist/admin.js')
        ->css(__DIR__.'/resources/less/admin.less'),

    new Extend\Locales(__DIR__ . '/resources/locale'),


    (new Extend\Routes('api'))
        ->get('/users/{id}/money/history', 'user.money.history', ListUserMoneyHistoryController::class),

    (new Extend\ApiSerializer(UserSerializer::class))
        ->attributes(AddUserMoneyAttributes::class),

    (new Extend\Settings())
        ->serializeToForum('gtdxyz-money-plus.moneyname', 'gtdxyz-money-plus.moneyname')
        ->serializeToForum('gtdxyz-money-plus.noshowzero', 'gtdxyz-money-plus.noshowzero'),

    (new Extend\Middleware("api"))
        ->add(DistributeAllHistoryMiddleware::class)
        ->add(MoneyRewardsMiddleware::class)
        ->add(TransferHistoryMiddleware::class)
        ->add(UserSaveMiddleware::class),

    (new Extend\Event())
        ->listen(Registered::class, Listeners\PostRegisterOperations::class)
        ->listen(PostSaving::class, [Listeners\GiveMoney::class, 'postSaving'])
        ->listen(Posted::class, [Listeners\GiveMoney::class, 'postWasPosted'])
        ->listen(PostRestored::class, [Listeners\GiveMoney::class, 'postWasRestored'])
        ->listen(PostHidden::class, [Listeners\GiveMoney::class, 'postWasHidden'])
        ->listen(PostDeleted::class, [Listeners\GiveMoney::class, 'postWasDeleted'])
        ->listen(DiscussionStarted::class, [Listeners\GiveMoney::class, 'discussionWasStarted'])
        ->listen(DiscussionRestored::class, [Listeners\GiveMoney::class, 'discussionWasRestored'])
        ->listen(DiscussionHidden::class, [Listeners\GiveMoney::class, 'discussionWasHidden'])
        ->listen(DiscussionDeleted::class, [Listeners\GiveMoney::class, 'discussionWasDeleted'])
        ->listen(Saving::class, [Listeners\GiveMoney::class, 'userWillBeSaved']),

    (new Extend\Event())
        ->listen(MoneyHistoryEvent::class, MoneyHistoryListeners::class)
        ->listen(MoneyAllHistoryEvent::class, MoneyAllHistoryListeners::class),
];

if (class_exists('Flarum\Likes\Event\PostWasLiked')) {
    $extend[] =
        (new Extend\Event())
            ->listen(PostWasLiked::class, [Listeners\GiveMoney::class, 'postWasLiked'])
            ->listen(PostWasUnliked::class, [Listeners\GiveMoney::class, 'postWasUnliked'])
    ;
}

//#TODO 适配新的签到extension

//#TODO AutoModerator
// if (class_exists('Askvortsov\AutoModerator\Extend\AutoModerator')) {
//     $extend[] =
//         (new \Askvortsov\AutoModerator\Extend\AutoModerator())
//             ->metricDriver('money', AutoModerator\Metric\Money::class)
//             ->actionDriver('money', AutoModerator\Action\Money::class)
//         ;
// }

return $extend;
