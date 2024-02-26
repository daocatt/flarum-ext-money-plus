<?php

namespace Gtdxyz\Money\History\Listeners;

use Gtdxyz\Money\History\Event\MoneyHistoryEvent;

class MoneyHistoryListeners extends HistoryListeners
{
    protected $source = "";
    protected $sourceDesc = "";

    public function handle(MoneyHistoryEvent $event) {
        $this->source = $event->source;
        $this->sourceDesc = $event->sourceDesc;

        $this->exec($event->user, $event->money);
    }
}
