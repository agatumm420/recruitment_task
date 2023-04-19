<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PragmaGoTech\Interview\FeeCalculatorImpl;
use PragmaGoTech\Interview\Model\LoanProposal;

final class FeeCalculatorTest extends TestCase
{
    private $breakpoints;

    protected function setUp(): void
    {
        $this->breakpoints = [
            [1000, 50],
            [5000, 200],
            [10000, 400],
            [20000, 800]
        ];
        $this->differentBreakpoints=[
            [1100, 20],
            [5200, 200],
            [10500, 420],
            [20100, 810]
        ];
    }

    public function testCalculateFee(): void
    {
        $calculator = new FeeCalculatorImpl($this->breakpoints);

        $loanProposal = new LoanProposal(3500);
        $fee = $calculator->calculate($loanProposal);

        $this->assertEquals(145.0, $fee);
    }

    public function testCalculateFeeRoundedUp(): void
    {
        $calculator = new FeeCalculatorImpl($this->breakpoints);

        $loanProposal = new LoanProposal(4000);
        $fee = $calculator->calculate($loanProposal);

        $this->assertEquals(165, $fee);
    }

    public function testCalculateFeeAtBreakpoint(): void
    {
        $calculator = new FeeCalculatorImpl($this->breakpoints);

        $loanProposal = new LoanProposal(10000);
        $fee = $calculator->calculate($loanProposal);

        $this->assertEquals(400, $fee);
    }
    public function testCalculateFeeMinimumLoanAmount(): void
    {
        $calculator = new FeeCalculatorImpl($this->breakpoints);

        $loanProposal = new LoanProposal(1000);
        $fee = $calculator->calculate($loanProposal);

        $this->assertEquals(50, $fee);
    }

    public function testCalculateFeeMaximumLoanAmount(): void
    {
        $calculator = new FeeCalculatorImpl($this->breakpoints);

        $loanProposal = new LoanProposal(20000);
        $fee = $calculator->calculate($loanProposal);

        $this->assertEquals(800, $fee);
    }
    //different breakpoints
    public function testCalculateFeeDiff(): void
    {
        $calculator = new FeeCalculatorImpl($this->differentBreakpoints);

        $loanProposal = new LoanProposal(3500);
        $fee = $calculator->calculate($loanProposal);

        $this->assertEquals(130, $fee);
    }

    public function testCalculateFeeRoundedUpDiff(): void
    {
        $calculator = new FeeCalculatorImpl($this->differentBreakpoints);

        $loanProposal = new LoanProposal(4000);
        $fee = $calculator->calculate($loanProposal);

        $this->assertEquals(150., $fee);
    }

    public function testCalculateFeeAtBreakpointDiff(): void
    {
        $calculator = new FeeCalculatorImpl($this->differentBreakpoints);

        $loanProposal = new LoanProposal(10500);
        $fee = $calculator->calculate($loanProposal);

        $this->assertEquals(420, $fee);
    }
    public function testCalculateFeeBelowMinimum(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Loan amount should be between 1,000 PLN and 20,000 PLN');

        $calculator = new FeeCalculatorImpl($this->breakpoints);
        $loanProposal = new LoanProposal(500); // Below the minimum
        $calculator->calculate($loanProposal);
    }

    public function testCalculateFeeAboveMaximum(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Loan amount should be between 1,000 PLN and 20,000 PLN');

        $calculator = new FeeCalculatorImpl($this->breakpoints);
        $loanProposal = new LoanProposal(25000); // Above the maximum
        $calculator->calculate($loanProposal);
    }

    public function testCalculateFeeWithInvalidBreakpoints(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid breakpoint configuration');

        $invalidBreakpoints = [
            [1000, 50],
            [5000, 200],
            [20000, 800], // This breakpoint should be after the 10000 breakpoint
            [10000, 400]
        ];

        new FeeCalculatorImpl($invalidBreakpoints);

    }
    public function testThreeBreakpoints(): void
    {
        $breakpoints = [
            [1000, 50],
            [10000, 400],
            [20000, 800]
        ];

        $calculator = new FeeCalculatorImpl($breakpoints);

        $loanProposal = new LoanProposal(3500);
        $fee = $calculator->calculate($loanProposal);

        $this->assertEquals(150.0, $fee);
    }

    public function testFiveBreakpoints(): void
    {
        $breakpoints = [
            [1000, 50],
            [5000, 200],
            [10000, 400],
            [15000, 600],
            [20000, 800]
        ];

        $calculator = new FeeCalculatorImpl($breakpoints);

        $loanProposal = new LoanProposal(3500);
        $fee = $calculator->calculate($loanProposal);

        $this->assertEquals(145.0, $fee);
    }

}
