<?php

declare(strict_types=1);

namespace PragmaGoTech\Interview;


use InvalidArgumentException;
use Exception;
use PragmaGoTech\Interview\Model\LoanProposal;

class FeeCalculatorImpl implements FeeCalculator
{
    private $breakpoints;

    public function __construct($breakpoints)
    {
        for ($i = 1; $i < count($breakpoints); $i++) {
            if ($breakpoints[$i][0] <= $breakpoints[$i - 1][0]) {
                throw new InvalidArgumentException('Invalid breakpoint configuration');
            }
        }
        $this->breakpoints = $breakpoints;
    }

    public function calculate(LoanProposal $application): float
    {
        $amount = $application->amount();

        if ($amount < 1000 || $amount > 20000) {
            throw new InvalidArgumentException("Loan amount should be between 1,000 PLN and 20,000 PLN");
        }

        list($lower_bound, $upper_bound) = $this->find_bounds($amount);
        $fee = $this->interpolate_fee($amount, $lower_bound, $upper_bound);
        $fee = $this->round_up_fee($fee, $amount);
        return $fee;
    }

    private function find_bounds($amount)
    {
        $lower_bound = null;
        $upper_bound = null;

        foreach ($this->breakpoints as $breakpoint) {
            if ($breakpoint[0] <= $amount) {
                $lower_bound = $breakpoint;
            } elseif ($breakpoint[0] > $amount) {
                $upper_bound = $breakpoint;
                break;
            }
        }

        return array($lower_bound, $upper_bound);

    }

    private function interpolate_fee($amount, $lower_bound, $upper_bound)
    {
        $slope = ($upper_bound[1] - $lower_bound[1]) / ($upper_bound[0] - $lower_bound[0]);
        $intercept = $lower_bound[1] - $slope * $lower_bound[0];
        return $slope * $amount + $intercept;
    }

    private function round_up_fee($fee, $amount)
    {
        echo (string)$fee . "\n";

        $fee = round($fee, 2);
        echo (string)$fee . "\n";
        $total = $fee + $amount;
        echo (string)$amount . "\n";
        echo (string)$total . "\n";
        $rounded_total = ceil($total / 5) * 5;

        // Calculate the difference between the rounded total and the original total
        $difference = $rounded_total - $total;

        // Update the fee by adding the difference
        $fee = $fee + $difference;

        return round($fee);
    }
}
