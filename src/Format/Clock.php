<?php

/**
 * @Package:    MaplePHP Format string
 * @Author:     Daniel Ronkainen
 * @Licence:    Apache-2.0 license, Copyright © Daniel Ronkainen
                Don't delete this comment, its part of the license.
 */

namespace MaplePHP\DTO\Format;

use Exception;
use IntlDateFormatter;
use InvalidArgumentException;

final class Clock extends FormatAbstract implements FormatInterface
{
    static protected ?string $defaultLocale = 'en';
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

        parent::__construct(new \DateTime($value));
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
     * @param string $localeCode
     * @return $this
     */
    public function setLanguage(string $localeCode): self
    {
        $this->locale = $localeCode;
        return $this;
    }

    // Alias to setLanguage
    public function setLocale(string $localeCode): self
    {
        return $this->setLanguage($localeCode);
    }

    /**
     * Set default date language using locale
     *
     * @param string $localeCode
     * @return void
     */
    static public function setDefaultLanguage(string $localeCode): void
    {
        self::$defaultLocale = $localeCode;
    }

    // Alias to setDefaultLanguage
    static public function setDefaultLocale(string $localeCode): void
    {
        self::setDefaultLanguage($localeCode);
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
        if($translations) {
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
     * Get Value
     * @return string
     */
    public function __toString(): string
    {
        return $this->get();
    }

    /**
     * Return translation find array
     *
     * @param \DateTime $date
     * @param string $locale
     * @return array
     */
    protected function getTranslationData(\DateTime $date, string $locale): array
    {

        if($locale !== "en") {
            $translations = $this->getTranslation($locale);
            if($translations === false) {
                $formatters = $this->getLocaleTranslation($locale);
            }
        }

        if(!isset($translations) && !isset($formatters)) {
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
        if($translationFile !== false) {
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
                'weekdayShort'=> new IntlDateFormatter($locale, IntlDateFormatter::FULL, IntlDateFormatter::NONE, null, null, 'E')
            ];
        }
        return $this->parts[$locale];
    }
}
