<?php

declare(strict_types=1);

use App\Handler\NotFoundHandler;
use Yiisoft\Definitions\DynamicReference;
use Yiisoft\Definitions\Reference;
use Yiisoft\Injector\Injector;
use Yiisoft\Middleware\Dispatcher\MiddlewareDispatcher;
use Yiisoft\Yii\Http\Application;

/** @var array $params */

return [
    Application::class => [
        '__construct()' => [
            'dispatcher' => DynamicReference::to(static function (Injector $injector) use ($params) {
                return ($injector->make(MiddlewareDispatcher::class))
                    ->withMiddlewares($params['yiisoft/yii/http']['middlewares']);
            }),
            'fallbackHandler' => Reference::to(
                $params['yiisoft/yii/http']['notFoundHandler'] ?? NotFoundHandler::class,
            ),
        ],
    ],
];
