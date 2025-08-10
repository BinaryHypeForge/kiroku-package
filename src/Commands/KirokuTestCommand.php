<?php

namespace BinaryHype\Kiroku\Commands;

use BinaryHype\Kiroku\Actions\SendExceptionToAPI;
use Illuminate\Console\Command;

class KirokuTestCommand extends Command
{
    public $signature = 'kiroku:test';

    public $description = 'Send a test exception to Kiroku';

    public function handle(): int
    {
        $this->info('Sending test exception to Kiroku...');

        try {
            (new SendExceptionToAPI())(new \Exception('This is a test exception from Kiroku command'));
            $this->info('Test exception sent successfully');
        } catch (\Throwable $ex) {
            $this->error($ex->getMessage());
        }

        return self::SUCCESS;
    }
}
