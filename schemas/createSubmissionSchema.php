<?php

namespace schemas;

use DateTime;
use core\Schema;

require_once './core/schema.php';

/** Schema used to validate the data from
 * the create submission form. */
class CreateSubmissionSchema extends Schema
{
    public string $startDate;
    public string $endDate;
    public string $reason;

    
    public function rules(): array
    {
        $this->addCustomRule($this->validateDates(), 'The start date must be before the end date');
        $this->addCustomRule($this->validateDatePeriod(), 'The start date must after today');

        return [
            'startDate' => [
                'required' => true,
                'date' => true
            ],
            'endDate' => [
                'required' => true,
                'date' => true
            ],
            'reason' => [
                'required' => true,
                'min' => 3,
                'max' => 1000
            ],
        ];
    }

    /** Custom rule to validate if the
     * start date is before the end date.
     */
    private function validateDates(): bool
    {
        $startDate = new DateTime($this->startDate);
        $endDate = new DateTime($this->endDate);

        if($startDate > $endDate)
        {
            return false;
        }

        return true;
    }

    /** Custom rule to validate if the date period
     * is after today.
     */
    private function validateDatePeriod(): bool
    {
        $startDate = new DateTime($this->startDate);

        $currentDate = new DateTime();

        if($startDate < $currentDate)
        {
            return false;
        }

        return true;
    }
}

?>