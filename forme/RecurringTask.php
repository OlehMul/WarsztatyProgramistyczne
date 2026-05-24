<?php

namespace forme;

class RecurringTask extends Task
{

    private $interval;
    private $next;


    function __construct($title, $createdBy, $description, $priority, $status, $tags, $category, $made_up_tags, $interval)
    {
        parent::__construct($title, $createdBy, $description, $priority, $status, $tags, $category, $made_up_tags);
        $this->interval = $interval;
        $this->next = $this->nextOccurrence();
    }

    function nextOccurrence()
    {
        $date = new \DateTime($this->getCreatedAt());
        if ($this->interval == "daily") {
            $date->modify("+1 day");

        } else if ($this->interval == "weekly") {
            $date->modify("+1 week");
        } else {
            $date->modify("+1 month");
        }
        $formattedDate = date('Y-m-d H:i:s', $date->getTimestamp());

        return $formattedDate;
    }

    #[\Override]
    public function changeStatus($status): void
    {
        if ($status == "done") {
            parent::changeStatus("todo");
            $this->setCreatedAt($this->next);
            $this->next = $this->nextOccurrence();
        }else{
            parent::changeStatus($status);
        }
    }


    public function getInterval()
    {
        return $this->interval;
    }


    public function getNext()
    {
        return $this->next;
    }




}