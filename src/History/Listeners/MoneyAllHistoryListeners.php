<?php

namespace Gtdxyz\Money\History\Listeners;

use Gtdxyz\Money\History\Event\MoneyAllHistoryEvent;
use Gtdxyz\Money\History\Model\UserMoneyHistory;

class MoneyAllHistoryListeners extends HistoryListeners
{
    protected $source;
    protected $sourceDesc;

    public function handle(MoneyAllHistoryEvent $event) {
        $this->source = $event->source;
        $this->sourceDesc = $event->sourceDesc;
        $insert = [];

        foreach ($event->list as $item) {
            $insert[] = [
                "user_id" => $item->id,
                "type" => $event->money > 0 ? "C" : "D",
                "money" => $event->money > 0 ? $event->money : -$event->money,
                "source" => $this->source,
                "source_desc" => $this->sourceDesc,
                "balance_money" => isset($item->init_money) ? $item->init_money : $item->money - $event->money,
                "last_money" => $event->money,
                "create_user_id" => isset($item->create_user_id) ? $item->create_user_id : $item->id,
                "change_time" => Date("Y-m-d H:i:s")
            ];
        }

        UserMoneyHistory::query()->insert($insert);
    }
}
