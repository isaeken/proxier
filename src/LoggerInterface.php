<?php

namespace IsaEken\Proxier;

interface LoggerInterface
{
    public function log(string $url): void;
}
