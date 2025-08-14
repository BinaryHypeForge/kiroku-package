<?php

namespace BinaryHype\Kiroku\Tests;

use BinaryHype\Kiroku\KirokuServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn(string $modelName) => 'Spatie\\LoginLink\\Tests\\TestSupport\\Database\\Factories\\' . class_basename($modelName) . 'Factory'
        );
    }

    protected function getPackageProviders($app): array
    {
        return [
            KirokuServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        $this->setUpDatabase();
    }

    protected function setUpDatabase(): self
    {
        config()->set('database.default', 'testing');

        config()->set('app.key', 'base64:LjpSHzPr1BBeuRWrlUcN2n2OWZ36o8+VpTLZdHcdG7Q=');

        return $this;
    }
}
