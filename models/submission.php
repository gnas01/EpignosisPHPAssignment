<?php

class Submission
{
    public int $id;
    public string $dateSubmitted;
    public string $vacationStart;
    public string $vacationEnd;
    public string $reason;
    public string $status;

    public function getDaysRequested()
    {
        $start = new DateTime($this->vacationStart);
        $end = new DateTime($this->vacationEnd);

        $interval = $start->diff($end);

        return $interval->format('%a');
    }
}

?>