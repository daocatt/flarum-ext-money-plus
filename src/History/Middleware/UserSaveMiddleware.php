<?php

namespace Gtdxyz\Money\History\Middleware;

use Flarum\Http\RequestUtil;
use Flarum\Locale\Translator;
use Flarum\Post\Post;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\User;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Arr;

use Gtdxyz\Money\History\Event\MoneyHistoryEvent;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class UserSaveMiddleware implements MiddlewareInterface
{
    private $events;
    private $source = "USERWILLBESAVED";
    private $sourceDesc = '';

    public function __construct(Dispatcher $events, Translator $translator)
    {
        $this->events = $events;
        $this->sourceDesc = $translator->trans("gtdxyz-money-plus.history-auto.forum.system-rewards");
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $actor = RequestUtil::getActor($request);
        $userId = Arr::get($request->getParsedBody(), 'data.id');

        $user = User::query()->selectRaw("*, '{$actor->id}' as create_user_id")->where("id", $userId)->first();

        $response = $handler->handle($request);
        $attributes = Arr::get($request->getParsedBody(), 'data.attributes');
        if ($response->getStatusCode() == 200 && strpos($request->getUri(), '/users/') && $request->getMethod() == 'PATCH' && isset($attributes['money']) && $actor->money != $attributes['money']) {
            $money = bcsub($attributes['money'], $user->money);
            $user->init_money = $user->money;
            $user->money = $attributes['money'];
            
            $this->events->dispatch(new MoneyHistoryEvent($user, $money, $this->source, $this->sourceDesc));
        }

        return $response;
    }
}
