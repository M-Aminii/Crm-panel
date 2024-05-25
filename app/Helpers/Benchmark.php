<?php

namespace App\Helpers;

class Benchmark
{
    private $startTime;

    public function start()
    {
        $this->startTime = microtime(true);
    }

    public function end()
    {
        return microtime(true) - $this->startTime;
    }
}
