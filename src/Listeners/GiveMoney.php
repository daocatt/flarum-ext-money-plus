<?php

namespace Gtdxyz\Money\Listeners;

use Flarum\Foundation\ValidationException;
use Illuminate\Support\Arr;
use Flarum\Locale\Translator;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Contracts\Events\Dispatcher;
use Flarum\User\User;
use Flarum\Post\Event\Posted;
use Flarum\Post\Event\Saving as PostSaving;
use Flarum\Post\Event\Restored as PostRestored;
use Flarum\Post\Event\Hidden as PostHidden;
use Flarum\Post\Event\Deleted as PostDeleted;
use Flarum\Discussion\Event\Started;
use Flarum\Discussion\Event\Restored as DiscussionRestored;
use Flarum\Discussion\Event\Hidden as DiscussionHidden;
use Flarum\Discussion\Event\Deleted as DiscussionDeleted;
use Flarum\User\Event\Saving;
use Flarum\Likes\Event\PostWasLiked;
use Flarum\Likes\Event\PostWasUnliked;
use Gtdxyz\Money\Event\MoneyUpdated;
use Gtdxyz\Money\History\Event\MoneyHistoryEvent;

abstract class AutoRemoveEnum
{
    public const NEVER = 1;
    public const HIDDEN = 1;
    public const DELETED = 1;
}

class GiveMoney
{
    protected $settings;
    protected $events;
    protected $translator;
    protected $autoremove_hidden;
    protected $autoremove_deleted;

    public function __construct(SettingsRepositoryInterface $settings, Dispatcher $events, Translator $translator)
    {
        $this->settings = $settings;
        $this->events = $events;
        $this->translator = $translator;

        $this->autoremove_hidden = (int)$this->settings->get('gtdxyz-money-plus.autoremove_hidden', 1);
        $this->autoremove_deleted = (int)$this->settings->get('gtdxyz-money-plus.autoremove_deleted', 1);
    }

    public function giveMoney(?User $user, $money, ?string $source, ?string $sourceDesc)
    {
        if (!is_null($user)) {
            $user->money = bcadd($user->money, $money);
            $user->save();

            $this->events->dispatch(new MoneyUpdated($user));

            if($source && $sourceDesc){
                $this->events->dispatch(new MoneyHistoryEvent($user, $money, $source, $sourceDesc));
            }
        }
    }

    //check if reduced money when post
    public function postSaving(PostSaving $event)
    {
        $user = $event->post->user;
        $money = (int)$this->settings->get('gtdxyz-money-plus.moneyforpost', 0);

        if($money < 0){
            if(bcadd($user->money, $money) < 0){
                throw new ValidationException([
                    'message' => $this->settings->get('gtdxyz-money-plus.moneyname').$this->translator->trans('gtdxyz-money-plus.forum.error.money_less_than_need')
                ]);
            }
        }
        
    }

    public function postWasPosted(Posted $event)
    {
        // If it's not the first post of a discussion
        if ($event->post['number'] > 1) {
            $minimumLength = (int)$this->settings->get('gtdxyz-money-plus.postminimumlength', 0);

            if (strlen($event->post->content) >= $minimumLength) {
                $money = (int)$this->settings->get('gtdxyz-money-plus.moneyforpost', 0);

                $source = 'POSTWASPOSTED';
                $sourceDesc = $this->translator->trans("gtdxyz-money-plus.history-auto.forum.post-was-posted");
                $this->giveMoney($event->actor, $money, $source, $sourceDesc);
            }
        }
    }

    public function postWasRestored(PostRestored $event)
    {
        if ($this->autoremove_hidden == AutoRemoveEnum::HIDDEN) {
            $minimumLength = (int)$this->settings->get('gtdxyz-money-plus.postminimumlength', 0);

            if (strlen($event->post->content) >= $minimumLength) {
                $money = (int)$this->settings->get('gtdxyz-money-plus.moneyforpost', 0);

                $source = 'POSTWASRESTORED';
                $sourceDesc = $this->translator->trans("gtdxyz-money-plus.history-auto.forum.post-was-restored");

                $this->giveMoney($event->post->user, $money, $source, $sourceDesc);
            }
        }
    }

    public function postWasHidden(PostHidden $event)
    {
        if ($this->autoremove_hidden == AutoRemoveEnum::HIDDEN) {
            $minimumLength = (int)$this->settings->get('gtdxyz-money-plus.postminimumlength', 0);

            if (strlen($event->post->content) >= $minimumLength) {
                $money = (int)$this->settings->get('gtdxyz-money-plus.moneyforpost', 0);

                $source = 'POSTWASHIDDEN';
                $sourceDesc = $this->translator->trans("gtdxyz-money-plus.history-auto.forum.post-was-hidden");

                $this->giveMoney($event->post->user, -$money, $source, $sourceDesc);
            }
        }
    }

    public function postWasDeleted(PostDeleted $event)
    {
        if ($this->autoremove_deleted == AutoRemoveEnum::DELETED && $event->post->type == 'comment') {
            $minimumLength = (int)$this->settings->get('gtdxyz-money-plus.postminimumlength', 0);

            if (strlen($event->post->content) >= $minimumLength) {
                $money = (int)$this->settings->get('gtdxyz-money-plus.moneyforpost', 0);

                $source = 'POSTWASDELETED';
                $sourceDesc = $this->translator->trans("gtdxyz-money-plus.history-auto.forum.post-was-deleted");

                $this->giveMoney($event->post->user, -$money, $source, $sourceDesc);
            }
        }
    }

    public function discussionWasStarted(Started $event)
    {
        $money = (int)$this->settings->get('gtdxyz-money-plus.moneyfordiscussion', 0);
        
        $source = 'DISCUSSIONWASSTARTED';
        $sourceDesc = $this->translator->trans("gtdxyz-money-plus.history-auto.forum.discussion-was-started");

        $this->giveMoney($event->actor, $money, $source, $sourceDesc);
    }

    public function discussionWasRestored(DiscussionRestored $event)
    {
        if ($this->autoremove_hidden == AutoRemoveEnum::HIDDEN) {
            $money = (int)$this->settings->get('gtdxyz-money-plus.moneyfordiscussion', 0);

            $source = 'DISCUSSIONWASRESTORED';
            $sourceDesc = $this->translator->trans("gtdxyz-money-plus.history-auto.forum.discussion-was-restored");

            $this->giveMoney($event->discussion->user, $money, $source, $sourceDesc);
        }
    }

    public function discussionWasHidden(DiscussionHidden $event)
    {
        if ($this->autoremove_hidden == AutoRemoveEnum::HIDDEN) {
            $money = (int)$this->settings->get('gtdxyz-money-plus.moneyfordiscussion', 0);

            $source = 'DISCUSSIONWASHIDDEN';
            $sourceDesc = $this->translator->trans("gtdxyz-money-plus.history-auto.forum.discussion-was-hidden");

            $this->giveMoney($event->discussion->user, -$money, $source, $sourceDesc);
        }
    }

    public function discussionWasDeleted(DiscussionDeleted $event)
    {
        if ($this->autoremove_deleted == AutoRemoveEnum::DELETED) {
            $money = (int)$this->settings->get('gtdxyz-money-plus.moneyfordiscussion', 0);

            $source = 'DISCUSSIONWASDELETED';
            $sourceDesc = $this->translator->trans("gtdxyz-money-plus.history-auto.forum.discussion-was-deleted");

            $this->giveMoney($event->discussion->user, -$money, $source, $sourceDesc);
        }
    }

    public function userWillBeSaved(Saving $event)
    {
        $attributes = Arr::get($event->data, 'attributes', []);

        if (array_key_exists('money', $attributes)) {
            $user = $event->user;
            $actor = $event->actor;
            $actor->assertCan('edit_money', $user);
            $user->money = (int)$attributes['money'];
            $this->events->dispatch(new MoneyUpdated($user));

            //history logo by middleware
        }
    }

    public function postWasLiked(PostWasLiked $event)
    {
        $money = (int)$this->settings->get('gtdxyz-money-plus.moneyforlike', 0);

        $source = 'POSTWASLIKED';
        $sourceDesc = $this->translator->trans("gtdxyz-money-plus.history-auto.forum.post-was-liked");

        $this->giveMoney($event->post->user, $money, $source, $sourceDesc);
    }

    public function postWasUnliked(PostWasUnliked $event)
    {
        $money = (int)$this->settings->get('gtdxyz-money-plus.moneyforlike', 0);

        $source = 'POSTWASUNLIKED';
        $sourceDesc = $this->translator->trans("gtdxyz-money-plus.history-auto.forum.post-was-unliked");

        $this->giveMoney($event->post->user, -$money, $source, $sourceDesc);
    }
}
