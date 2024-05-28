<?php

class UserHelper
{

    public function tempFunction()
    {
        echo "Im here";
        $a = 5;
        return $a + 5;
    }

    public function primeFunction($num){
        if($num<2) return false;
        else{
            for($i=2; $i*$i<=$num; $i++){
                if($num%$i == 0) return false;
            }
        }
        return true;
    }

}