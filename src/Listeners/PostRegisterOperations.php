<?php

/*
 * give mony after registration.
 */

 namespace Gtdxyz\Money\Listeners;

 use Flarum\Locale\Translator;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\Event\GroupsChanged;
use Flarum\User\Event\Registered;
use Illuminate\Contracts\Events\Dispatcher;

use Gtdxyz\Money\Event\MoneyUpdated;
use Gtdxyz\Money\History\Event\MoneyHistoryEvent;

class PostRegisterOperations
{
    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;

    /**
     * @var Dispatcher
     */
    protected $events;

    /**
     * @var Translator
     */
    protected $translator;

    public function __construct(SettingsRepositoryInterface $settings, Dispatcher $events, Translator $translator)
    {
        $this->settings = $settings;
        $this->events = $events;
        $this->translator = $translator;
    }

    public function handle(Registered $event)
    {
        $user = $event->user;
        $money = (int)$this->settings->get('gtdxyz-money-plus.moneyforregistration', 0);

        if ($user && $money > 0) {

            $source = 'REGISTRATION';
            $sourceDesc = $this->translator->trans("gtdxyz-money-plus.history-auto.forum.registration");

            $user->money = bcadd($user->money, $money);
            $user->save();

            $this->events->dispatch(new MoneyUpdated($user));

            if($source && $sourceDesc){
                $this->events->dispatch(new MoneyHistoryEvent($user, $money, $source, $sourceDesc));
            }
        }
    }
}
