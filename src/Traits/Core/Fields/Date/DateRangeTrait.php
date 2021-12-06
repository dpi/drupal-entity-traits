<?php

declare(strict_types=1);

namespace dpi\DrupalEntityTraits\Traits\Core\Fields\Date;

use dpi\DrupalEntityTraits\Attribute\Field;
use dpi\DrupalEntityTraits\Attribute\TimeZone;
use dpi\DrupalEntityTraits\Exception\MissingAttributeException;
use dpi\DrupalEntityTraits\Utility\Attribute;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
use Drupal\datetime_range\Plugin\Field\FieldType\DateRangeFieldItemList;
use Drupal\datetime_range\Plugin\Field\FieldType\DateRangeItem;

trait DateRangeTrait
{
    /**
     * Transforms value of a date range field into a date object.
     *
     * @return array{0:\DateTimeImmutable, 1:\DateTimeImmutable}|null
     *   Tuple of date time objects, or NULL if value was invalid
     *
     * @throws \Exception
     *   Thrown if time zone string in attribute is provided and is not valid
     */
    protected function getDateTimeFromDateRangeField(): ?array
    {
        $drupalField = Attribute::stack(Field::class) ?? throw new MissingAttributeException(Field::class);
        $timeZone = Attribute::stack(TimeZone::class) ?? throw new MissingAttributeException(TimeZone::class);

        return $this->doGetDateTimeFromDateRangeField($drupalField->getFieldName(), $timeZone->getTimeZone());
    }

    /**
     * Transforms value of a date range field into a date object.
     *
     * @param string $fieldName
     *   A field name
     * @param \DateTimeZone|null $timeZone
     *   A time zone, or NULL to use UTC
     *
     * @return array{0:\DateTimeImmutable, 1:\DateTimeImmutable}|null
     *   Tuple of date time objects, or NULL if value was invalid
     *
     * @internal
     */
    private function doGetDateTimeFromDateRangeField(string $fieldName, ?\DateTimeZone $timeZone): ?array
    {
        $fieldList = $this->get($fieldName);
        assert($fieldList instanceof DateRangeFieldItemList);
        $item = $fieldList->first();
        assert(is_null($item) || $item instanceof DateRangeItem);
        if ($item && !$item->isEmpty()) {
            $startDate = $item->get('start_date')->getValue();
            $endDate = $item->get('end_date')->getValue();
            if ($startDate instanceof DrupalDateTime && ($endDate instanceof DrupalDateTime || is_null($endDate))) {
                $dateTimeStart = $startDate->getPhpDateTime();
                $dateTimeEnd = $endDate?->getPhpDateTime() ?? (clone $dateTimeStart);
                $this->setTimeZoneForDateTimeRange($dateTimeStart, $timeZone ?? new \DateTimeZone('UTC'), $fieldName);
                $this->setTimeZoneForDateTimeRange($dateTimeEnd, $timeZone ?? new \DateTimeZone('UTC'), $fieldName);

                return [
                    \DateTimeImmutable::createFromMutable($dateTimeStart),
                    \DateTimeImmutable::createFromMutable($dateTimeEnd),
                ];
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
    protected function setTimeZoneForDateTimeRange(\DateTime $dateTime, \DateTimeZone $timeZone, string $fieldName): void
    {
        $dateTime->setTimezone($timeZone);
    }

    /**
     * Sets value of date objects to a date range field.
     *
     * @param \DateTimeInterface $startDate
     *   A date time object
     * @param \DateTimeInterface $endDate
     *   A date time object
     *
     * @throws \InvalidArgumentException
     *   Thrown when time zones are not the same for start and end objects
     */
    protected function setDateTimeToDateRangeField(\DateTimeInterface $startDate, \DateTimeInterface $endDate): void
    {
        $drupalField = Attribute::stack(Field::class) ?? throw new MissingAttributeException(Field::class);
        $this->doSetDateTimeToDateRangeField($startDate, $endDate, $drupalField->getFieldName());
    }

    /**
     * Sets value of date objects to a date range field.
     *
     * @param \DateTimeInterface $startDate
     *   A date time object
     * @param \DateTimeInterface $endDate
     *   A date time object
     * @param string $fieldName
     *   A field name
     *
     * @throws \InvalidArgumentException
     *   Thrown when time zones are not the same for start and end objects
     *
     * @internal
     */
    private function doSetDateTimeToDateRangeField(\DateTimeInterface $startDate, \DateTimeInterface $endDate, string $fieldName): void
    {
        if ($startDate->getTimezone()->getName() !== $endDate->getTimezone()->getName()) {
            throw new \InvalidArgumentException('Time zones for both objects must be the same.');
        }

        $rawStart = $startDate->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT);
        $rawEnd = $endDate->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT);
        $this->set($fieldName, [
          'value' => $rawStart,
          'end_value' => $rawEnd,
        ]);
    }
}
