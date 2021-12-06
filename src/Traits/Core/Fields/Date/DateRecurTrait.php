<?php

declare(strict_types=1);

namespace dpi\DrupalEntityTraits\Traits\Core\Fields\Date;

use dpi\DrupalEntityTraits\Attribute\Field;
use dpi\DrupalEntityTraits\Exception\MissingAttributeException;
use dpi\DrupalEntityTraits\Utility\Attribute;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\date_recur\Plugin\Field\FieldType\DateRecurFieldItemList;
use Drupal\date_recur\Plugin\Field\FieldType\DateRecurItem;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;

trait DateRecurTrait
{
    /**
     * Transforms value of a date recur field into a date object.
     *
     * @return array{0:\DateTimeImmutable, 1:\DateTimeImmutable}|null
     *   Tuple of date time objects, or NULL if value was invalid
     */
    protected function getDateTimeFromDateRecurField(): ?array
    {
        $drupalField = Attribute::stack(Field::class) ?? throw new MissingAttributeException(Field::class);

        return $this->doGetDateTimeFromDateRecurField($drupalField->getFieldName());
    }

    /**
     * Transforms value of a date recur field into a date object.
     *
     * @param string $fieldName
     *   A field name
     *
     * @return array{0:\DateTimeImmutable, 1:\DateTimeImmutable}|null
     *   Tuple of date time objects, or NULL if value was invalid
     *
     * @internal
     */
    private function doGetDateTimeFromDateRecurField(string $fieldName): ?array
    {
        $fieldList = $this->get($fieldName);
        assert($fieldList instanceof DateRecurFieldItemList);
        $item = $fieldList->first();
        assert(is_null($item) || $item instanceof DateRecurItem);
        if ($item && !$item->isEmpty()) {
            $startDate = $item->get('start_date')->getValue();
            $endDate = $item->get('end_date')->getValue();
            if ($startDate instanceof DrupalDateTime && ($endDate instanceof DrupalDateTime || is_null($endDate))) {
                $dateTimeStart = $startDate->getPhpDateTime();
                $dateTimeEnd = $endDate?->getPhpDateTime() ?? (clone $dateTimeStart);

                return [
                    \DateTimeImmutable::createFromMutable($dateTimeStart),
                    \DateTimeImmutable::createFromMutable($dateTimeEnd),
                ];
            }
        }

        return null;
    }

    /**
     * Sets value of date objects to a date recur field.
     *
     * @param \DateTimeInterface $startDate
     *   A date time object
     * @param \DateTimeInterface $endDate
     *   A date time object
     * @param string|null $rrule
     *   An optional RRULE string
     *
     * @throws \InvalidArgumentException
     *   Thrown when time zones are not the same for start and end objects
     */
    protected function setDateTimeToDateRecurField(\DateTimeInterface $startDate, \DateTimeInterface $endDate, ?string $rrule = null): void
    {
        $drupalField = Attribute::stack(Field::class) ?? throw new MissingAttributeException(Field::class);
        $this->doSetDateTimeToDateRecurField($startDate, $endDate, $rrule, $drupalField->getFieldName());
    }

    /**
     * Sets value of date objects to a date range field.
     *
     * @param \DateTimeInterface $startDate
     *   A date time object
     * @param \DateTimeInterface $endDate
     *   A date time object
     * @param string|null $rrule
     *   An optional RRULE string
     * @param string $fieldName
     *   A field name
     *
     * @throws \InvalidArgumentException
     *   Thrown when time zones are not the same for start and end objects
     *
     * @internal
     */
    private function doSetDateTimeToDateRecurField(\DateTimeInterface $startDate, \DateTimeInterface $endDate, ?string $rrule, string $fieldName): void
    {
        if ($startDate->getTimezone()->getName() !== $endDate->getTimezone()->getName()) {
            throw new \InvalidArgumentException('Time zones for both objects must be the same.');
        }

        // These need to be converted to UTC per storage.
        $utc = new \DateTimeZone('UTC');
        $startDateMutable = \DateTime::createFromInterface($startDate)->setTimezone($utc);
        $endDateMutable = \DateTime::createFromInterface($endDate)->setTimezone($utc);
        $rawStart = $startDateMutable->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT);
        $rawEnd = $endDateMutable->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT);
        $this->set($fieldName, [
            'value' => $rawStart,
            'end_value' => $rawEnd,
            'rrule' => $rrule ?? '',
            'timezone' => $startDate->getTimezone()->getName(),
        ]);
    }
}
