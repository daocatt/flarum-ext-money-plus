<?php

namespace Gtdxyz\Money\History\Api\Serializer;

use Flarum\Api\Serializer\AbstractSerializer;
use Flarum\Api\Serializer\BasicUserSerializer;
use Flarum\Api\Serializer\PostSerializer;
use Flarum\Post\Post;
use Flarum\Settings\SettingsRepositoryInterface;
use Gtdxyz\Money\History\Model\UserMoneyHistory;

class MoneyHistorySerializer extends AbstractSerializer
{
    protected $type = 'userMoneyHistory';

    protected function getDefaultAttributes($data){
        $attributes = [
            'id' => $data->id,
            'type' => $data->type,
            'money' => $data->money,
            'user_id' => $data->user_id,
            'source_desc' => $data->source_desc,
            'last_money' => $data->last_money,
            'balance_money' => $data->balance_money,
            'create_user_id' => $data->create_user_id,
            'change_time' => date("Y-m-d H:i:s", strtotime($data->change_time))
        ];

        return $attributes;
    }

    protected function User($moneyHistory){
        return $this->hasOne($moneyHistory, BasicUserSerializer::class);
    }

    protected function createUser($moneyHistory){
        return $this->hasOne($moneyHistory, BasicUserSerializer::class);
    }
}
