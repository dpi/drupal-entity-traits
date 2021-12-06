<?php

declare(strict_types=1);

namespace dpi\DrupalEntityTraits\Tests\fixtures\Entity;

use dpi\DrupalEntityTraits\Attribute\Field;
use dpi\DrupalEntityTraits\Traits\Core\Fields\Date\DateRecurTrait;
use Drupal\Core\Entity\ContentEntityBase;

/**
 * Test entity for date recur trait.
 */
final class DateRecurEntity extends ContentEntityBase
{
    use DateRecurTrait;

    public function bundle()
    {
        return 'testbundle';
    }

    #[Field('my_field')]
    public function getMyDateRecurFieldAsDateTime(): ?array
    {
        return $this->getDateTimeFromDateRecurField();
    }

    /**
     * @return $this
     */
    #[Field('my_field')]
    public function setMyDateTimeToDateRecurField(\DateTimeInterface $startDate, \DateTimeInterface $endDate, ?string $rrule = null)
    {
        $this->setDateTimeToDateRecurField($startDate, $endDate, $rrule);

        return $this;
    }
}
