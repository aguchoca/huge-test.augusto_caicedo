<?php
//As unit test settting own randon commands and fire them in a loop.
include('drawing.class.php');
$sketch = new Drawing();

$test_com = ["C 18 6",
            "L 1 2 10 2",
            "L 6 3 6 4",
            "R 10 1 15 3",
            "B 3 3 *",
            "C 24 9",
            "L 9 3 9 10",
            "L 1 7 24 7",
            "B 4 6 *",
            "B 1 8 t",
            "B 21 8 c",
            "Q"
            ];


foreach($test_com as $com) {
    echo $com . "\n";
    echo $sketch->main($com);
}



?>