<?php

namespace QR_Code\Types;

use QR_Code\Contracts\CodeType;
use QR_Code\Exceptions\EmptyEventSummaryException;
use QR_Code\Exceptions\InvalidEventDateException;
use QR_Code\Util\AbstractGenerator;

/**
 * Class QR_CalendarEvent
 *
 * QR Code Generator for PHP is distributed under MIT
 * Copyright (C) 2018 Bruno Vaula Werneck <brunovaulawerneck at gmail dot com>
 */
class QR_CalendarEvent extends AbstractGenerator implements CodeType
{
    const DATETIME_FORMAT = 'Ymd\THis\Z';

    protected $dateTimeStart;

    protected $dateTimeEnd;

    protected $summary;

    protected $description;

    protected $location;

    /**
     * QR_CalendarEvent constructor.
     *
     * @throws \QR_Code\Exceptions\EmptyEventSummaryException
     * @throws \QR_Code\Exceptions\InvalidEventDateException
     */
    public function __construct(\DateTime $dateTimeStart, \DateTime $dateTimeEnd, string $summary, string $description = '', string $location = '')
    {
        $this->validate($dateTimeStart, $dateTimeEnd, $summary);

        $this->dateTimeStart = $dateTimeStart;
        $this->dateTimeEnd = $dateTimeEnd;
        $this->summary = $summary;
        $this->description = $description;
        $this->location = $location;
    }

    protected function validateDateTimeEnd(\DateTime $start, \DateTime $end): bool
    {
        return $end > $start;
    }

    protected function validateSummary(string $summary): bool
    {
        return trim($summary) !== '';
    }

    /**
     * @throws \QR_Code\Exceptions\EmptyEventSummaryException
     * @throws \QR_Code\Exceptions\InvalidEventDateException
     */
    protected function validate(\DateTime $start, \DateTime $end, string $summary): void
    {
        if ($this->validateDateTimeEnd($start, $end) === false) {
            throw new InvalidEventDateException('Event end date and time must be higher than Event start');
        }

        if ($this->validateSummary($summary) === false) {
            throw new EmptyEventSummaryException('Event Summary cannot be empty');
        }
    }

    /**
     * Get Formatted QR Code String
     */
    public function getCodeString(): string
    {
        $response = "BEGIN:VCALENDAR\n";
        $response .= "VERSION:1.0\n";
        $response .= "BEGIN:VEVENT\n";

        $response .= 'DTSTART:'.$this->dateTimeStart->format(self::DATETIME_FORMAT)."\n";
        $response .= 'DTEND:'.$this->dateTimeEnd->format(self::DATETIME_FORMAT)."\n";

        $response .= "SUMMARY:{$this->summary}\n";
        if ($this->description) {
            $response .= "DESCRIPTION:{$this->description}\n";
        }
        if ($this->location) {
            $response .= "LOCATION:{$this->location}\n";
        }

        $response .= "END:VEVENT\n";
        $response .= 'END:VCALENDAR';

        return $response;
    }
}
