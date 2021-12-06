<?php

declare(strict_types=1);

namespace dpi\DrupalEntityTraits\Tests\Attribute;

use dpi\DrupalEntityTraits\Attribute\TimeZone;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \dpi\DrupalEntityTraits\Attribute\TimeZone
 */
final class TimeZoneTest extends TestCase
{
    public function testTimeZone(): void
    {
        $attribute = new TimeZone('Asia/Singapore');
        $this->assertEquals('Asia/Singapore', $attribute->getTimeZone()->getName());
    }

    public function testTimeZoneException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unknown or bad timezone (Asia/SingaporeBlah)');
        new TimeZone('Asia/SingaporeBlah');
    }
}
