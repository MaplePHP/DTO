<?php

/**
 * @Package:    MaplePHP Format string
 * @Author:     Daniel Ronkainen
 * @Licence:    Apache-2.0 license, Copyright © Daniel Ronkainen
                Don't delete this comment, its part of the license.
 */

namespace MaplePHP\DTO\Format;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Exception;
use IntlDateFormatter;
use InvalidArgumentException;

final class Clock extends FormatAbstract implements FormatInterface
{
    protected static ?string $defaultLocale = 'en';

    protected static string|DateTimeZone|null $defaultTimezone = null;

    protected ?string $locale = null;
    protected array $parts = [];

    /**
     * Input is mixed data type in the interface because I do not know the type before
     * The class constructor MUST handle the input validation
     * @param string $value
     * @throws Exception
     */
    public function __construct(mixed $value)
    {
        if (is_array($value) || is_object($value)) {
            throw new InvalidArgumentException("Is expecting a string or a convertable string value.", 1);
        }
        $date = new DateTime($value);
        if (!is_null(self::$defaultTimezone)) {
            $date->setTimezone(self::$defaultTimezone);
        }
        parent::__construct($date);
    }

    /**
     * Get Value
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->get();
    }

    /**
     * Init format by adding data to modify/format/traverse
     * @param mixed $value
     * @return self
     * @throws Exception
     */
    public static function value(mixed $value): FormatInterface
    {
        return new Clock((string)$value);
    }

    /**
     * Get current expected locale
     *
     * @return string|null
     */
    public function getLocale(): ?string
    {
        return $this->locale ?? self::$defaultLocale;
    }

    /**
     * Set expected date language using locale
     *
     * Will translate localized month and weekday names
     *
     * Falls back to PHP’s IntlDateFormatter if a static translation is unavailable.
     * This depends on installed system locales, which may cause inconsistencies
     * and be slower than static translations. However, missing locales can be
     * installed for greater control.
     *
     * @param string $localeCode The locale code (e.g., 'sv_SE', 'en_US').
     * @return $this
     */
    public function setLocale(string $localeCode): self
    {
        $this->locale = $localeCode;
        return $this;
    }

    /**
     * Set default date language using locale
     *
     * @param string $localeCode
     * @return void
     */
    public static function setDefaultLocale(string $localeCode): void
    {
        self::$defaultLocale = $localeCode;
    }

    /**
     * Set the timezone
     *
     * @param DateTimeZone|string $timezone
     * @return $this
     * @throws Exception
     */
    public function setTimezone(DateTimeZone|string $timezone): self
    {
        if (!$timezone instanceof DateTimeZone) {
            $timezone = new DateTimeZone($timezone);
        }

        $this->raw = $this->raw->setTimezone($timezone);

        return $this;
    }

    /**
     * Set default timezone
     *
     * @param string|DateTimeZone $timezone
     * @return void
     * @throws \DateInvalidTimeZoneException
     */
    public static function setDefaultTimezone(string|DateTimeZone $timezone): void
    {
        self::$defaultTimezone = $timezone instanceof DateTimeZone ? $timezone : new DateTimeZone($timezone);
    }

    /**
     * Get timezone identifier (e.g., Europe/Stockholm)
     *
     * @return string
     */
    public function timezone(): string
    {
        return $this->raw->getTimezone()->getName();
    }

    /**
     * Format date data
     *
     * @param string $format
     * @param string|null $locale
     * @return object
     */
    public function format(string $format = 'Y-m-d H:i:s', ?string $locale = null): string
    {
        $locale = !is_null($locale) ? $locale : $this->getLocale();
        $translations = $this->getTranslationData($this->raw, $locale);
        if ($translations) {
            return str_replace($translations['find'], $translations['replace'], $this->raw->format($format));
        }
        return $this->raw->format($format);
    }

    /**
     * Get date and time
     *
     * @return string
     */
    public function get(): string
    {
        return $this->dateTime();
    }

    /**
     * Get date and time
     *
     * @return string
     */
    public function dateTime(): string
    {
        return $this->raw->format('Y-m-d H:i:s');
    }

    /**
     * Get date
     *
     * @return string
     */
    public function date(): string
    {
        return $this->raw->format('Y-m-d');
    }

    /**
     * Get hour and minutes
     *
     * @return string
     */
    public function time(): string
    {
        return $this->raw->format('H:i');
    }

    /**
     * Get seconds (with leading zeros)
     *
     * @return string
     */
    public function seconds(): string
    {
        return $this->raw->format('s');
    }

    /**
     * Check if year is leap year
     *
     * @return bool
     */
    public function isLeapYear(): bool
    {
        return (bool)$this->raw->format('L');
    }

    /**
     * Get ISO 8601 week number of year
     *
     * @return int
     */
    public function weekNumber(): int
    {
        return (int)$this->raw->format('W');
    }

    /**
     * Get ISO 8601 formatted date (e.g., 2025-03-20T14:30:00+01:00)
     *
     * @return string
     */
    public function iso(): string
    {
        return $this->raw->format(DateTimeInterface::ATOM);
    }

    /**
     * Get RFC 2822 formatted date (e.g., Thu, 20 Mar 2025 14:30:00 +0100)
     *
     * @return string
     */
    public function rfc(): string
    {
        return $this->raw->format(DateTime::RFC2822);
    }

    /**
     * Get AM/PM format of time (e.g., 02:30 PM)
     *
     * @return string
     */
    public function time12Hour(): string
    {
        return $this->raw->format('h:i A');
    }

    /**
     * Get difference in days from today (negative if in the past, positive if future)
     *
     * @return int
     * @throws \DateMalformedStringException
     */
    public function diffInDays(): int
    {
        $today = new DateTimeImmutable('today', $this->raw->getTimezone());
        return (int)$today->diff($this->raw)->format('%r%a');
    }

    /**
     * Check if the date is today
     *
     * @return bool
     * @throws \DateMalformedStringException
     */
    public function isToday(): bool
    {
        return $this->raw->format('Y-m-d') === (new DateTimeImmutable('today', $this->raw->getTimezone()))->format('Y-m-d');
    }

    /**
     * Get hour and minutes
     *
     * @return string
     */
    public function timestamp(): string
    {
        return $this->raw->getTimestamp();
    }

    /**
     * Get year
     *
     * @param bool $shorthand
     * @return string
     */
    public function year(bool $shorthand = false): string
    {
        return $this->raw->format($shorthand ? 'y' : 'Y');
    }

    /**
     * Get month
     *
     * @return string
     */
    public function month(): string
    {
        return $this->raw->format("m");
    }

    /**
     * Get full name of month (e.g., January)
     *
     * @return string
     */
    public function monthName(): string
    {
        return $this->raw->format('F');
    }

    /**
     * Get shorthand name of month (e.g., Jan)
     *
     * @return string
     */
    public function shortMonthName(): string
    {
        return $this->raw->format('M');
    }

    /**
     * Get day
     *
     * @return string
     */
    public function day(): string
    {
        return $this->raw->format("d");
    }

    /**
     * Get day of the week (numeric, 1 for Monday through 7 for Sunday)
     *
     * @return int
     */
    public function dayOfWeek(): int
    {
        return (int)$this->raw->format('N');
    }

    /**
     * Get full name of the weekday (e.g., Monday)
     *
     * @return string
     */
    public function weekday(): string
    {
        return $this->raw->format('l');
    }

    /**
     * Get short name of weekday (e.g., Mon)
     *
     * @return string
     */
    public function shortWeekday(): string
    {
        return $this->raw->format('D');
    }

    /**
     * Return translation find array
     *
     * @param DateTime $date
     * @param string $locale
     * @return array
     */
    protected function getTranslationData(DateTime $date, string $locale): array
    {

        if ($locale !== "en") {
            $translations = $this->getTranslation($locale);
            if ($translations === false) {
                $formatters = $this->getLocaleTranslation($locale);
            }
        }

        if (!isset($translations) && !isset($formatters)) {
            return [];
        }

        return [
            'find' => [$date->format('F'), $date->format('M'), $date->format('l'), $date->format('D')],
            'replace' => [
                $translations['months'][$date->format('F')] ?? $formatters['monthFull']->format($this->raw),
                $translations['monthsShort'][$date->format('M')] ?? $formatters['monthShort']->format($this->raw),
                $translations['weekdays'][$date->format('l')] ?? $formatters['weekdayFull']->format($this->raw),
                $translations['weekdaysShort'][$date->format('D')] ?? $formatters['weekdayShort']->format($this->raw),
            ]
        ];
    }

    /**
     * Get static translation if exists
     *
     * @param string $locale
     * @return array|false
     */
    protected function getTranslation(string $locale): array|false
    {
        $translationFile = realpath(__DIR__ . "/../lang/$locale.php");
        if ($translationFile !== false) {
            return require $translationFile;
        }
        return false;
    }

    /**
     * Retrieves localized month and weekday names.
     *
     * Falls back to PHP’s IntlDateFormatter if a static translation is unavailable.
     * This depends on installed system locales, which may cause inconsistencies
     * and be slower than static translations. However, missing locales can be
     * installed for greater control.
     *
     * @param string $locale The locale code (e.g., 'sv_SE', 'en_US').
     * @return array IntlDateFormatter instances for full/short month and weekday names.
     */
    protected function getLocaleTranslation(string $locale): array
    {
        if (!isset($this->parts[$locale])) {
            $this->parts[$locale] = [
                'monthFull'   => new IntlDateFormatter($locale, IntlDateFormatter::LONG, IntlDateFormatter::NONE, null, null, 'MMMM'),
                'monthShort'  => new IntlDateFormatter($locale, IntlDateFormatter::MEDIUM, IntlDateFormatter::NONE, null, null, 'MMM'),
                'weekdayFull' => new IntlDateFormatter($locale, IntlDateFormatter::FULL, IntlDateFormatter::NONE, null, null, 'EEEE'),
                'weekdayShort' => new IntlDateFormatter($locale, IntlDateFormatter::FULL, IntlDateFormatter::NONE, null, null, 'E')
            ];
        }
        return $this->parts[$locale];
    }
}
