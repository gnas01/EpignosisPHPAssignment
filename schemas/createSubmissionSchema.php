<?php

require_once './core/schema.php';

class CreateSubmissionSchema extends Schema
{
    public string $startDate;
    public string $endDate;
    public string $reason;


    public function rules(): array
    {
        $this->addCustomRule($this->validateDates(), 'The start date must be before the end date');
        $this->addCustomRule($this->validateDatePeriod(), 'The start date must not be in the past');

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

    private function validateDates()
    {
        $startDate = new DateTime($this->startDate);
        $endDate = new DateTime($this->endDate);

        if($startDate > $endDate)
        {
            return false;
        }

        return true;
    }

    private function validateDatePeriod()
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