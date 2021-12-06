<?php

declare(strict_types=1);

namespace dpi\DrupalEntityTraits\Tests\fixtures\Entity;

use dpi\DrupalEntityTraits\Attribute\Field;
use dpi\DrupalEntityTraits\Attribute\TimeZone;
use dpi\DrupalEntityTraits\Traits\Core\Fields\Date\DateRangeTrait;
use Drupal\Core\Entity\ContentEntityBase;

/**
 * Test entity for date range trait.
 */
final class DateRangeEntity extends ContentEntityBase
{
    use DateRangeTrait;

    public function bundle()
    {
        return 'testbundle';
    }

    #[
        Field('my_field'),
        TimeZone('Asia/Singapore'),
    ]
    public function getMyDateRangeFieldAsDateTime(): ?array
    {
        return $this->getDateTimeFromDateRangeField();
    }

    /**
     * @return $this
     */
    #[Field('my_field')]
    public function setMyDateTimeToDateRangeField(\DateTimeInterface $startDate, \DateTimeInterface $endDate)
    {
        $this->setDateTimeToDateRangeField($startDate, $endDate);

        return $this;
    }
}
