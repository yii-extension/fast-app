<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use Psr\Container\ContainerInterface;
use Simple\App\Tests\UnitTester;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\CommandLoader\ContainerCommandLoader;
use Symfony\Component\Console\Tester\CommandTester;
use Yiisoft\Config\Config;
use Yiisoft\Config\ConfigPaths;
use Yiisoft\Config\Modifier\RecursiveMerge;
use Yiisoft\Config\Modifier\ReverseMerge;
use Yiisoft\Di\Container;
use Yiisoft\Di\ContainerConfig;
use Yiisoft\Yii\Console\ExitCode;
use Yiisoft\Yii\Runner\ConfigFactory;

final class HelloCest
{
    private ContainerInterface $container;

    public function _before(UnitTester $I): void
    {
        $config = $this->getConfig();

        $this->container = new Container(
            ContainerConfig::create()
                ->withDefinitions($config->get('console'))
                ->withProviders($config->get('providers'))
        );
    }

    public function testExecute(UnitTester $I): void
    {
        $app = new Application();
        $params = $this->getConfig()->get('params');

        $loader = new ContainerCommandLoader(
            $this->container,
            $params['yiisoft/yii-console']['commands']
        );

        $app->setCommandLoader($loader);

        $command = $app->find('hello');

        $commandCreate = new CommandTester($command);

        $commandCreate->setInputs(['yes']);

        $I->assertEquals(ExitCode::OK, $commandCreate->execute([]));

        $output = $commandCreate->getDisplay(true);

        $I->assertStringContainsString('Hello!', $output);
    }

    private function getConfig(): Config
    {
        return ConfigFactory::create(new ConfigPaths(dirname(__DIR__, 2), 'config'), null);
    }
}
