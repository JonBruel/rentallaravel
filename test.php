<?php

class ost
{


    public function __construct()
    {
        $fruits = array("d" => ["lemon" => '1 kg', 'level' => 3], "a" => "orange", "b" => "banana", "c" => "apple");



        function test_print($item2, $key)
        {
            echo "$key. $item2<br />\n";
        }


        function test_alter1(&$item1, $key, $prefix)
        {
            //$item1 = "$prefix: $item1";
            if ($key == 'd') $item1 = '';
        }


        echo "Before ...:\n";
        array_walk($fruits, 'test_print');

        array_walk($fruits, [$this, 'test_alter'], 'fruit');
        echo "... and after:\n";

        array_walk($fruits, 'test_print');
    }

    function test_alter(&$item1, $key, $prefix)
    {
        //$item1 = "$prefix: $item1";
        if ($key == 'd') $item1 = '';
    }

}

$c = new ost;
?>