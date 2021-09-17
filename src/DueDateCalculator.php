<?php

declare(strict_types=1);

class DueDateCalculator
{
    private const START_WORKING_TIME = 9;
    private const END_WORKING_TIME = 17;

    private const HOUR_TO_SECOND = 3600;

    /**
     * Calculates the issue end date.
     *
     * @param DateTime $issueStartDate
     * @param integer $turnaroundInHour
     * @return DateTime $issueEndDate
     */
    public static function calculateDueDate(DateTime $issueStartDate, int $turnaroundInHour): DateTime
    {
        static::validateIssueStartDate($issueStartDate);
        static::validateTurnaround($turnaroundInHour);

        $turnaroundInSec = $turnaroundInHour * static::HOUR_TO_SECOND;
        $issueEndDate = new DateTime($issueStartDate->format('Y-m-d H:i:s'));

        $sumInSec = 0;
        while ($sumInSec != $turnaroundInSec) {
            if (static::isWeekend($issueEndDate)) {
                $issueEndDate = new DateTime($issueEndDate->format('Y-m-d') . static::START_WORKING_TIME . ':00 +2 days');
                continue;
            }

            $remainingTimeInSec = $turnaroundInSec - $sumInSec;
            $workTimeEndDate = new DateTime($issueEndDate->format('Y-m-d') . static::END_WORKING_TIME . ':00');
            $diffInSec = $workTimeEndDate->getTimestamp() - $issueEndDate->getTimestamp();

            if ($diffInSec < $remainingTimeInSec) {
                $sumInSec += $diffInSec;
                $issueEndDate = new DateTime($issueEndDate->format('Y-m-d') . static::START_WORKING_TIME . ':00 +1 days');
            } else {
                $sumInSec += $remainingTimeInSec;
                $issueEndDate->modify("+{$remainingTimeInSec} second");
            }
        }

        return $issueEndDate;
    }

    private static function validateIssueStartDate(DateTime $date)
    {
        if (static::isWeekend($date)) {
            throw new Exception('Start date cannot be weekend!');
        }

        $startHour = $date->format('G');
        if ($startHour < static::START_WORKING_TIME || $startHour >= static::END_WORKING_TIME) {
            throw new Exception('Start date has to be in the work time!');
        }
    }

    private static function validateTurnaround(int $turnaroundInHour)
    {
        if ($turnaroundInHour <= 0) {
            throw new Exception('Turnaround hours has to be a positive number!');
        }
    }

    private static function isWeekend(DateTime $date): bool
    {
        return $date->format('N') >= 6;
    }
}
