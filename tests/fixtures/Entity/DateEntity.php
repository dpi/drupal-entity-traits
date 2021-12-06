<?php

declare(strict_types=1);

namespace dpi\DrupalEntityTraits\Tests\fixtures\Entity;

use dpi\DrupalEntityTraits\Attribute\Field;
use dpi\DrupalEntityTraits\Attribute\TimeZone;
use dpi\DrupalEntityTraits\Traits\Core\Fields\Date\DateTrait;
use Drupal\Core\Entity\ContentEntityBase;

/**
 * Test entity for date trait.
 */
final class DateEntity extends ContentEntityBase
{
    use DateTrait;

    public function bundle()
    {
        return 'testbundle';
    }

    #[Field('my_field')]
    #[TimeZone('Asia/Singapore')]
    public function getMyDateFieldAsDateTime(): ?\DateTimeImmutable
    {
        return $this->getDateTimeFromDateTimeField();
    }

    /**
     * @return $this
     */
    #[Field('my_field')]
    public function setMyDateTimeToDateField(\DateTimeInterface $date)
    {
        $this->setDateTimeToDateTimeField($date);

        return $this;
    }
}
