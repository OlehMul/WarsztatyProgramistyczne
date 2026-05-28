<?php

namespace forme;

class Task{
private $allowed_categories = ["Praca", "Dom", "Nauka", "Zdrowie", "Inne"];
private $allowed_priorities = ["low", "medium", "high"];
private $allowed_statuses = ["todo", "in progress", "done"];
private $allowed_tags  =["pilne", "zespół", "backend", "frontend"];

private $error_array = [];

//NEED TO ADD TIME CHECK IN MINUTES
   const STATUS_TODO ="todo";
    const STATUS_IN_PROGRESS = "in progress";
    const STATUS_DONE = "done";
    const PRIORITY_LOW = "low";
    const PRIORITY_MEDIUM = "medium";
    const PRIORITY_HIGH = "high";
    private $id;
    private $title;
    private $type;

    private $category;
    private $description;
    private $priority = "medium";
    private $status = "todo";
    private $tags = array();
    private $createdAt = '';
    private $createdBy;
    private $made_up_tags;

    private $estimated_minutes;

    function __construct($type,$title, $createdBy,$estimated_minutes, $description = '', $priority= '', $status= '', $tags= '',$category="Praca", $made_up_tags='')
    {
        if ($priority == self::PRIORITY_LOW || $priority == self::PRIORITY_MEDIUM || $priority == self::PRIORITY_HIGH) {
            $this->priority = $priority;
        } else {
            $this->error_array[] = "Error, invalid priority";
        }
        if ($status == self::STATUS_TODO || $status == self::STATUS_IN_PROGRESS || $status == self::STATUS_DONE) {
            $this->status = $status;
        } else {
            $this->error_array[] = "Error, invalid status";
        }
        if (empty($title)) {
            $this->error_array[] = "Error, empty title";
        }
        if (empty($tags)) {
            $this->error_array[] = "Error, empty tags";
        }
        if (empty($priority)) {
            $this->error_array[] = "Error, empty priority";
        }
        if (empty($status)) {
            $this->error_array[] = "Error, empty status";
        }
        if (!in_array($category, $this->allowed_categories)) {
            $this->error_array[] = "Error, invalid category";
        }
        if($estimated_minutes < 0){
            $this->error_array[] = "Error, invalid minutes";
        }


        if (!empty($made_up_tags)) {
                if (!preg_match("/\#([a-zA-Z0-9_]+)/", $made_up_tags)) {
                    $this->error_array[] = ("nieprawidłowy format tagów");

                }

        }

        if (preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $description)) {           /*dla sprawdzania e-mailów*/
            if (!preg_match('^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}', $description)) {
                $this->error_array[] = "Nieprawidłowy format adresu email";
            }
        }
        if (preg_match('/#\w+/', $description)) {                 /*dla poszukiwania i sprawdzania tagów*/
            if (!preg_match('/#[a-zA-Z0-9_+]/', $description)) {
                $this->error_array[] = "Nieprawidlowy format tagów";
            }
        }

        if (preg_match('/[0-9]{4}[.-][0-1][0-2][.-][0-9]{2}/', $description)) {     /*dla sprawdzania dat*/
            if (!preg_match('^(\d{4}[.-]0[1-9]|1[0-2]|[1-9])[.-]([1-9]|0[1-9]|[1-2]\d|3[0-1]', $description)) {
                $this->error_array[] = "Nieprawidlowy format daty";
            }
        }

        if (preg_match('/#\w+/', $made_up_tags)) {                 /*dla poszukiwania i sprawdzania tagów*/
            if (!preg_match('/#[a-zA-Z0-9_+]/', $made_up_tags)) {
                $this->error_array[] = "Nieprawidlowy format tagów";
            }
        }


        $this->title = $title;
        $this->createdBy = $createdBy;
        $this->description = $description;
        $this->estimated_minutes = $estimated_minutes;
        $this->type = $type;
        if(is_array($tags)){
            $this->tags = $tags;
            if(!empty($made_up_tags)) {
                $this->tags = array_merge($this->tags, explode(' ', $made_up_tags));
            }
        } else {
            // Handle string tags
            $this->tags = !empty($tags) ? explode(',', $tags) : [];
            if(!empty($made_up_tags)) {
                $this->tags = array_merge($this->tags, explode(' ', $made_up_tags));
            }
        }
// Remove any empty values and sort
        $this->tags = array_filter($this->tags);
        sort($this->tags);
        $this->category = $category;
        $this->createdAt = date('Y-m-d H:i:s', time());

    }
    function extractTags(){      /*dla znalezienia tagów*/
        $this->tags =preg_replace( '/#([a-zA-Z0-9_]+)/', '<mark>$0</mark>',$this->tags);
        return $this->tags;
    }


 public function formatTaskDescription() {        /*For correct format in desc*/
        // Zamiana URL na linki HTML
        $this->description = preg_replace(
            '/\b(?:https?|ftp):\/\/[a-z0-9-+&@#\/%?=~_|!:,.;]*[a-z0-9-+&@#\/%=~_|]/i',
            '<a href="$0" target="_blank">$0</a>',
            $this->description
        );

        // Wykrywanie i formatowanie tagów
     $this->description = preg_replace(
            '/#([a-zA-Z0-9_]+)/',
            '<b class="tag">$0</b>',
            $this->description
        );


        // Wykrywanie i formatowanie list punktowanych
     $this->description = preg_replace(
            '/^[\s]*[-*+][\s]+(.+)$/m',
            '<li>$1</li>',
         $this->description
        );

        // Owijanie list w znaczniki <ul></ul>
        if (strpos($this->description, '<li>') !== false) {
            $this->description = '<ul>' . $this->description . '</ul>';
            $this->description = str_replace('</ul><ul>', '', $this->description);
        }

     $this->description = preg_replace('/[0-9]{3}-[0-9]{3}-[0-9]{4}/','<u>$0</u>' ,$this->description);  /*sprawdza czy jest w opisie numer telefonu*/

     $this->description = preg_replace('/[0-9]{4}[.-][0-1][0-2][.-][0-9]{2}/','<u>$0</u>' ,$this->description);  /*sprawdza czy jest data*/

     $this->description = preg_replace('/[0-9]{2}[-:][0-9]{2}/','<u>$0</u>' ,$this->description);       /*sprawdza czy są podane godziny*/
        return $this->description;
    }


    public function getEstimatedMinutes()
    {
        return $this->estimated_minutes;
    }

    public function getErrorArray(): array
    {
        return $this->error_array;
    }

    public function setErrorArray(string $error): void
    {
        $this->error_array[] = $error;
    }


    public function getId()
    {
        return $this->id;
    }

    public function getAllowedCategories(): array
    {
        return $this->allowed_categories;
    }

    public function getCategory(): mixed
    {
        return $this->category;
    }


    public function getMadeUpTags()
    {
        return $this->made_up_tags;
    }


    public function getAllowedPriorities(): array
    {
        return $this->allowed_priorities;
    }

    public function getAllowedStatuses(): array
    {
        return $this->allowed_statuses;
    }

    public function getAllowedTags(): array
    {
        return $this->allowed_tags;
    }


    public function getTitle()
    {
        return $this->title;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getTags()
    {
        return $this->tags;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getCreatedBy()
    {
        return $this->createdBy;
    }





    public function getStatus(): string
    {
        return $this->status;
    }


    public function setTitle($title): void{
        $this->title = $title;
    }

    public function setDescription( $description): void
    {
        $this->description = $description;
    }

    public function changeStatus($status): void{
        $this->status = $status;
    }

    public function setTags( $tags): void
    {
        $this->tags = $tags;
    }

    public function getPriority(): string
    {
        return $this->priority;
    }

    public function setPriority(string $priority): void
    {
        $this->priority = $priority;
    }
   public function  toArray(){
        $ar = [
           "title" => $this->title,
            "category"=>$this->category,
           "description" => $this->description,
           "priority" => $this->priority,
           "status" => $this->status,
           "tags" => $this->tags,
            "estimated_minutes" => $this->estimated_minutes,
           "created_at" => $this->createdAt,
           "created_by" => $this->createdBy
       ];

return $ar;
   }


    public function getType()
    {
        return $this->type;
    }


    public function setId($id): void
    {
        $this->id = $id;
    }

    static function fromArray(array $data){
        $tags = json_decode($data["tags"] ?? [], true);
        if(!is_array($tags)){
            $tags = [];
        }
        $task = new Task(
            $data['type'],
            $data['title'],
            $data['created_by'],
            $data['estimated_minutes'] ?? '',
            $data['description'] ?? '',
            $data['priority'] ?? 'medium',
            $data['status'] ?? 'todo',
           $tags,
            $data['category'] ?? 'Praca',
            ''



        );
        $task->setId($data['id']);
        return $task;
    }

    public function setCreatedAt(string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }


}