<?php

declare(strict_types=1);

namespace dpi\DrupalEntityTraits\Attribute;

#[\Attribute(\Attribute::TARGET_METHOD)]
class TimeZone
{
    protected \DateTimeZone $timeZone;

    /**
     * Constructs a new TimeZone.
     *
     * @param string $timeZone
     *   A time zone string
     *
     * @throws \Exception
     *   Exception when time zone is not recognised
     */
    public function __construct(string $timeZone)
    {
        $this->timeZone = new \DateTimeZone($timeZone);
    }

    public function getTimeZone(): \DateTimeZone
    {
        return $this->timeZone;
    }
}
