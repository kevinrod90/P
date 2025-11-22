<?php
$raw_pass_moving_items = "ITEM DESCRIPTION	SWP	WHOLESALE PRICE																								
REFINED SUGAR PRINCE S49	3450	3380
BROWN SUGAR PRINCE S49	2800	2680
PALM OIL CONTAINER 17KG 	1350	1350
SILVER SWAN SOY SAUCE 19L	870.2	870.2
SILVER SWAN UNO SOY SAUCE	528.2	528.2
COKE REG 1.5L C12	792	726	
SPRITE 1.5L C12		808.2	726
COKE MISMO 290ML C12		210	200	
SPRITE MISMO 290ML C12		214.2	200	
ROYAL MISMO 290ML C12	214.2	200	
Flour 1st class 25kg	1206.95	1126.15
Flour 3rd Class 25kg S100	1151.4	1075.65
CAKE FLOUR 1KG	56.5	55.25";

$lines = explode("\n", $raw_pass_moving_items);
$pass_moving_items_headers = explode("\t", $lines[0]);
$pass_moving_items_data = array_map(function($line) {
    return explode("\t", $line);
}, array_slice($lines, 1));
?>
