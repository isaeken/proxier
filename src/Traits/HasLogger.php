<?php

namespace IsaEken\Proxier\Traits;

use IsaEken\Proxier\Contracts\Logger;
use IsaEken\Proxier\Loggers\NullLogger;

trait HasLogger
{
    private Logger|null $logger = null;

    /**
     * Set logger.
     *
     * @param Logger $logger
     * @return static
     */
    public function setLogger(Logger $logger): static
    {
        $this->logger = $logger;
        return $this;
    }

    /**
     * Get logger.
     *
     * @return Logger
     */
    public function getLogger(): Logger
    {
        return $this->logger ??= new NullLogger();
    }
}
