<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class DueDateCalculatorTest extends TestCase
{
    public function testAddAnHour(): void
    {
        $expectedResult = new DateTime('2021-09-17 17:00');
        $actualStartDate = new DateTime('2021-09-17 16:00');
        $actualTurnaroundInHour = 1;
        $this->assertEquals($expectedResult, DueDateCalculator::calculateDueDate($actualStartDate, $actualTurnaroundInHour));
    }

    public function testAddMoreHours(): void
    {
        $expectedResult = new DateTime('2021-09-21 11:00');
        $actualStartDate = new DateTime('2021-09-20 09:00');
        $actualTurnaroundInHour = 10;
        $this->assertEquals($expectedResult, DueDateCalculator::calculateDueDate($actualStartDate, $actualTurnaroundInHour));
    }

    public function testAddAnHourOnWorkTimeStart(): void
    {
        $expectedResult = new DateTime('2021-09-20 17:00');
        $actualStartDate = new DateTime('2021-09-20 09:00');
        $actualTurnaroundInHour = 8;
        $this->assertEquals($expectedResult, DueDateCalculator::calculateDueDate($actualStartDate, $actualTurnaroundInHour));
    }

    public function testAddAnHourOnWorkTimeStart2(): void
    {
        $expectedResult = new DateTime('2021-09-21 11:00');
        $actualStartDate = new DateTime('2021-09-20 09:00');
        $actualTurnaroundInHour = 10;
        $this->assertEquals($expectedResult, DueDateCalculator::calculateDueDate($actualStartDate, $actualTurnaroundInHour));
    }

    public function testAddOneWeekOnMonday(): void
    {
        $expectedResult = new DateTime('2021-09-24 17:00');
        $actualStartDate = new DateTime('2021-09-20 09:00');
        $actualTurnaroundInHour = 40;
        $this->assertEquals($expectedResult, DueDateCalculator::calculateDueDate($actualStartDate, $actualTurnaroundInHour));
    }

    public function testAddMoreThanAWeek(): void
    {
        $expectedResult = new DateTime('2021-09-27 11:00');
        $actualStartDate = new DateTime('2021-09-20 09:00');
        $actualTurnaroundInHour = 42;
        $this->assertEquals($expectedResult, DueDateCalculator::calculateDueDate($actualStartDate, $actualTurnaroundInHour));
    }

    public function testAddMoreHoursByOverflow(): void
    {
        $expectedResult = new DateTime('2021-09-23 10:00');
        $actualStartDate = new DateTime('2021-09-21 16:00');
        $actualTurnaroundInHour = 10;
        $this->assertEquals($expectedResult, DueDateCalculator::calculateDueDate($actualStartDate, $actualTurnaroundInHour));
    }

    public function testAddTwoHourByOverflowOnFriday(): void
    {
        $expectedResult = new DateTime('2021-09-20 10:00');
        $actualStartDate = new DateTime('2021-09-17 16:00');
        $actualTurnaroundInHour = 2;
        $this->assertEquals($expectedResult, DueDateCalculator::calculateDueDate($actualStartDate, $actualTurnaroundInHour));
    }

    public function testAddMoreHoursByOverflowOnFriday(): void
    {
        $expectedResult = new DateTime('2021-09-21 11:00');
        $actualStartDate = new DateTime('2021-09-17 16:00');
        $actualTurnaroundInHour = 11;
        $this->assertEquals($expectedResult, DueDateCalculator::calculateDueDate($actualStartDate, $actualTurnaroundInHour));
    }

    public function testAddAnHourWithMinutes(): void
    {
        $expectedResult = new DateTime('2021-09-20 09:10');
        $actualStartDate = new DateTime('2021-09-17 16:10');
        $actualTurnaroundInHour = 1;
        $this->assertEquals($expectedResult, DueDateCalculator::calculateDueDate($actualStartDate, $actualTurnaroundInHour));
    }

    public function testAddAnHourWithSeconds(): void
    {
        $expectedResult = new DateTime('2021-09-20 09:10:10');
        $actualStartDate = new DateTime('2021-09-17 16:10:10');
        $actualTurnaroundInHour = 1;
        $this->assertEquals($expectedResult, DueDateCalculator::calculateDueDate($actualStartDate, $actualTurnaroundInHour));
    }

    public function testAddAnHourWithSecondsByOverflow(): void
    {
        $expectedResult = new DateTime('2021-09-20 09:00:01');
        $actualStartDate = new DateTime('2021-09-17 16:00:01');
        $actualTurnaroundInHour = 1;
        $this->assertEquals($expectedResult, DueDateCalculator::calculateDueDate($actualStartDate, $actualTurnaroundInHour));
    }

    public function testAddOneYear(): void
    {
        $yearInWorkHours = 365 * 8;
        $expectedResult = new DateTime('2023-02-10 16:00:01');
        $actualStartDate = new DateTime('2021-09-17 16:00:01');
        $actualTurnaroundInHour = $yearInWorkHours;
        $this->assertEquals($expectedResult, DueDateCalculator::calculateDueDate($actualStartDate, $actualTurnaroundInHour));
    }

    public function testAddMoreThanAWeekOnFriday(): void
    {
        $expectedResult = new DateTime('2021-09-24 11:00');
        $actualStartDate = new DateTime('2021-09-17 09:00');
        $actualTurnaroundInHour = 42;
        $this->assertEquals($expectedResult, DueDateCalculator::calculateDueDate($actualStartDate, $actualTurnaroundInHour));
    }

    public function testCannotPassNotPositiveNumberAsWorkingHour(): void
    {
        $this->expectExceptionMessage('Turnaround hours has to be a positive number!');
        DueDateCalculator::calculateDueDate(new DateTime('2021-09-17 10:00'), 0);
    }

    public function testCanOnlyBeInWorkTime(): void
    {
        $this->expectExceptionMessage('Start date has to be in the work time!');
        DueDateCalculator::calculateDueDate(new DateTime('2021-09-17 17:00'), 1);
    }

    public function testCanOnlyBeInWorkdays(): void
    {
        $this->expectExceptionMessage('Start date cannot be weekend!');
        DueDateCalculator::calculateDueDate(new DateTime('2021-09-18 10:00'), 1);
    }

    public function testCanOnlyPassIntForWorkingHour(): void
    {
        $this->expectException(TypeError::class);
        DueDateCalculator::calculateDueDate(new DateTime('2021-09-17 17:00'), 'I have to be an int');
    }

    public function testCanOnlyPassDateTimeForStartDate(): void
    {
        $this->expectException(TypeError::class);
        DueDateCalculator::calculateDueDate('I have to be an instance of DateTime', 1);
    }
}
