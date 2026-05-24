<?php

namespace forme;

class UserPreferences{
    private $THEME;
    private $NUM;
    private $SORT;


 private const theme = array('light', 'dark');
 private const num = array(5,10,25);
 private const sort = array("data","priority","title");


function __construct(array $ar){

isset($ar['theme']) ? $this->THEME = $ar['theme'] :$this->theme = self::theme[0];
isset($ar['req']) ? $this->SORT = $ar['req'] : $this->SORT = self::sort[0];
isset($ar['num']) ? $this->NUM = $ar['num']: $this->NUM = self::num[0];
}

function getTheme(){
    return $this->THEME;
}
function getReq(){
    return $this->SORT;
}
function getNum(){
    return $this->NUM;
}
function save(array $data){
    if(isset($data['theme'])){
        if(in_array($data['theme'], self::theme)){
            setcookie("theme", $data['theme'], time() + (86400 * 30), "/");
        }
    }

    if(isset($data["num"])){
        if(in_array($data['num'], self::num)){
            setcookie("num", $data['num'], time() + (86400 * 30), "/");
        }
    }
   if(isset($data["req"])){
       if(in_array($data['req'], self::sort)){
           setcookie("req", $data['req'], time() + (86400 * 30), "/");
       }
   }
}



}