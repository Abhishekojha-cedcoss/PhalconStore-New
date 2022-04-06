<?php

namespace App\Components;

use Phalcon\Escaper;

class EscapeClass
{
    public function sanitize($val)
    {
       $escaper = new Escaper();
       foreach($val as $k=>$v)
       {
           $val[$k]=$escaper->escapeHtml($v);
       }
       return $val;
       
    }
}
