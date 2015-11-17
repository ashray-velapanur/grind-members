<h1>Date Diff Test</h1>
<?

$d1 = "2011-07-01";
$d2 = "2011-07-05";

echo "<p>Getting the difference between $d1 and $d2</p>";
echo "<p>There are " . date_diff($d1, $d2) . " day(s) between them.</p>";

echo str_replace("\n", "<br />", "this is a test
this is a test\n\nLove,\n--zy.");

function date_diff($date1, $date2) { 
    $current = $date1; 
    $datetime2 = date_create($date2); 
    $count = 0; 
    while(date_create($current) < $datetime2){ 
        $current = gmdate("Y-m-d", strtotime("+1 day", strtotime($current))); 
        $count++; 
    } 
    return $count; 
} 


?>