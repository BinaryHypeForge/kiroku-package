<?php

namespace BinaryHype\Kiroku\Commands;

use Illuminate\Console\Command;

class KirokuCommand extends Command
{
    public $signature = 'kiroku';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
