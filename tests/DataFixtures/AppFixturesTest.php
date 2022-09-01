<?php

namespace App\Tests\DataFixtures;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\StringInput;

class AppFixturesTest extends WebTestCase
{

    /**
     * @covers AppFixture::load
     */

    private static $application;

    public function initializeTest(): void
    {
        self::runCommand("doctrine:database:create --env=test");
        self::runCommand("doctrine:schema:update --force --env=test");
        self::runCommand("doctrine:fixtures:load --env=test -n");
    }

    public function tearDownTest(): void
    {
        self::runCommand("doctrine:fixtures:load --env=test -n");
    }

    private function runCommand($command): int
    {
        $command = sprintf('%s --quiet', $command);

        return self::getApplication()->run(new StringInput($command));
    }

    protected static function getApplication(): Application
    {
        if (null === self::$application) {
            $kernel = static::createKernel();
            self::$application = new Application($kernel);
            self::$application->setAutoExit(false);
        }
        return self::$application;
    }
}
