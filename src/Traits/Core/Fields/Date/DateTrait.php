<?php

declare(strict_types=1);

namespace dpi\DrupalEntityTraits\Traits\Core\Fields\Date;

use dpi\DrupalEntityTraits\Attribute\Field;
use dpi\DrupalEntityTraits\Attribute\TimeZone;
use dpi\DrupalEntityTraits\Exception\MissingAttributeException;
use dpi\DrupalEntityTraits\Utility\Attribute;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeFieldItemList;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItem;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;

/**
 * Represents a single value date field.
 *
 * @see \Drupal\datetime\Plugin\Field\FieldType\DateTimeItem
 */
trait DateTrait
{
    /**
     * Transforms value of a date field into a date object.
     *
     * @return \DateTimeImmutable|null
     *   A date time object, or NULL if value was invalid
     *
     * @throws \Exception
     *   Thrown if time zone string in attribute is provided and is not valid
     */
    protected function getDateTimeFromDateTimeField(): ?\DateTimeImmutable
    {
        $drupalField = Attribute::stack(Field::class) ?? throw new MissingAttributeException(Field::class);
        $timeZone = Attribute::stack(TimeZone::class) ?? throw new MissingAttributeException(TimeZone::class);

        return $this->doGetDateTimeFromDateTimeField($drupalField->getFieldName(), $timeZone->getTimeZone());
    }

    /**
     * Transforms value of a date field into a date object.
     *
     * @param string $fieldName
     *   A field name
     * @param \DateTimeZone|null $timeZone
     *   A time zone, or NULL to use UTC
     *
     * @return \DateTimeImmutable|null
     *   A date time object, or NULL if value was invalid
     *
     * @internal
     */
    private function doGetDateTimeFromDateTimeField(string $fieldName, ?\DateTimeZone $timeZone): ?\DateTimeImmutable
    {
        $fieldList = $this->get($fieldName);
        assert($fieldList instanceof DateTimeFieldItemList);
        $item = $fieldList->first();
        assert(is_null($item) || $item instanceof DateTimeItem);
        if ($item && !$item->isEmpty()) {
            $date = $item->get('date')->getValue();
            if ($date instanceof DrupalDateTime) {
                $dateTime = $date->getPhpDateTime();
                $this->setTimeZoneForDateTime($dateTime, $timeZone ?? new \DateTimeZone('UTC'), $fieldName);

                return \DateTimeImmutable::createFromMutable($dateTime);
            }
        }

        return null;
    }

    /**
     * Sets a time zone for a given field.
     *
     * @param \DateTime $dateTime
     *   A date time object
     * @param \DateTimeZone $timeZone
     *   The suggested time zone
     */
    protected function setTimeZoneForDateTime(\DateTime $dateTime, \DateTimeZone $timeZone, string $fieldName): void
    {
        $dateTime->setTimezone($timeZone);
    }

    /**
     * Sets value of a date object to a date field.
     *
     * @param \DateTimeInterface $date
     *   A date time object
     */
    protected function setDateTimeToDateTimeField(\DateTimeInterface $date): void
    {
        $drupalField = Attribute::stack(Field::class) ?? throw new MissingAttributeException(Field::class);
        $this->doSetDateTimeToDateTimeField($date, $drupalField->getFieldName());
    }

    /**
     * Sets value of a date object to a date field.
     *
     * @param \DateTimeInterface $date
     *   A date time object
     * @param string $fieldName
     *   A field name
     *
     * @internal
     */
    private function doSetDateTimeToDateTimeField(\DateTimeInterface $date, string $fieldName): void
    {
        $raw = $date->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT);
        $this->set($fieldName, $raw);
    }
}
