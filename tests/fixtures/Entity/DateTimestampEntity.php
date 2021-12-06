<?php

declare(strict_types=1);

namespace dpi\DrupalEntityTraits\Tests\fixtures\Entity;

use dpi\DrupalEntityTraits\Attribute\Field;
use dpi\DrupalEntityTraits\Attribute\TimeZone;
use dpi\DrupalEntityTraits\Traits\Core\Fields\Date\TimestampTrait;
use Drupal\Core\Entity\ContentEntityBase;

/**
 * Test entity for timestamp trait.
 */
final class DateTimestampEntity extends ContentEntityBase
{
    use TimestampTrait;

    public function bundle()
    {
        return 'testbundle';
    }

    #[Field('my_field')]
    #[TimeZone('Asia/Singapore')]
    public function getMyTimestampAsDateTime(): ?\DateTimeImmutable
    {
        return $this->getDateTimeFromTimestampField();
    }

    /**
     * @return $this
     */
    #[Field('my_field')]
    public function setMyDateTimeToTimestampField(\DateTimeInterface $date)
    {
        $this->setDateTimeToTimestampField($date);

        return $this;
    }
}
