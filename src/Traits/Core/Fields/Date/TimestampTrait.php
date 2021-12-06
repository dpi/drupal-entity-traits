<?php

declare(strict_types=1);

namespace dpi\DrupalEntityTraits\Traits\Core\Fields\Date;

use dpi\DrupalEntityTraits\Attribute\Field;
use dpi\DrupalEntityTraits\Attribute\TimeZone;
use dpi\DrupalEntityTraits\Exception\MissingAttributeException;
use dpi\DrupalEntityTraits\Utility\Attribute;
use Drupal\Core\Field\ChangedFieldItemList;
use Drupal\Core\Field\Plugin\Field\FieldType\TimestampItem;

/**
 * Represents a single value timestamp field.
 *
 * @see \Drupal\Core\Field\Plugin\Field\FieldType\TimestampItem
 */
trait TimestampTrait
{
    /**
     * Transforms value of a timestamp field into a date object.
     *
     * @return \DateTimeImmutable|null
     *   A date time object, or NULL if value was invalid
     *
     * @throws \Exception
     *   Thrown if time zone string in attribute is provided and is not valid
     */
    protected function getDateTimeFromTimestampField(): ?\DateTimeImmutable
    {
        $drupalField = Attribute::stack(Field::class) ?? throw new MissingAttributeException(Field::class);
        $timeZone = Attribute::stack(TimeZone::class) ?? throw new MissingAttributeException(TimeZone::class);

        return $this->doGetDateTimeFromTimestampField($drupalField->getFieldName(), $timeZone->getTimeZone());
    }

    /**
     * Transforms value of a timestamp field into a date object.
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
    private function doGetDateTimeFromTimestampField(string $fieldName, ?\DateTimeZone $timeZone): ?\DateTimeImmutable
    {
        $fieldList = $this->get($fieldName);
        assert($fieldList instanceof ChangedFieldItemList);
        $item = $fieldList->first();
        assert(is_null($item) || $item instanceof TimestampItem);
        if ($item && !$item->isEmpty()) {
            // Oddly 'value' can be either true int or numeric string, such as
            // via ChangedItem.
            /** @var string|null $value */
            // @phpstan-ignore-next-line
            $value = $item->value ?? null;
            $timestamp = (int) ($value ?? 0);
            if ($timestamp >= 0) {
                /** @var \DateTime $dateTime */
                $dateTime = \DateTime::createFromFormat('U', (string) $timestamp);
                $this->setTimeZoneForTimestamp($dateTime, $timeZone ?? new \DateTimeZone('UTC'), $fieldName);

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
     * @param string $fieldName
     *   A field name
     */
    protected function setTimeZoneForTimestamp(\DateTime $dateTime, \DateTimeZone $timeZone, string $fieldName): void
    {
        $dateTime->setTimezone($timeZone);
    }

    /**
     * Sets value of a date object to a timestamp field.
     *
     * @param \DateTimeInterface $date
     *   A date time object
     */
    protected function setDateTimeToTimestampField(\DateTimeInterface $date): void
    {
        $drupalField = Attribute::stack(Field::class) ?? throw new MissingAttributeException(Field::class);
        $this->doSetDateTimeToTimestampField($date, $drupalField->getFieldName());
    }

    /**
     * Sets value of a date object to a timestamp field.
     *
     * @param \DateTimeInterface $date
     *   A date time object
     * @param string $fieldName
     *   A field name
     *
     * @internal
     */
    private function doSetDateTimeToTimestampField(\DateTimeInterface $date, string $fieldName): void
    {
        $raw = $date->getTimestamp();
        $this->{$fieldName} = $raw;
    }
}
