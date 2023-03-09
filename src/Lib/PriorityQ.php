<?php

namespace App\PlusCourtChemin\Lib;

class PriorityQ extends \SplPriorityQueue
{
    public function compare($priority1,$priority2){
        if ($priority1 == $priority2) {return 0;}

        if ($priority1<$priority2) {return 1;}
        return -1;
    }
}