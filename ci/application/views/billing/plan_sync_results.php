<?
/**
 * Results of the plan sync
 * 
 * output from a chron job
 *
 * @joshcampbell
 * @view template
 */
 ?>
 
 <? foreach($results as $row){

 	echo $row["name"] . ": " . $row["response"]["action"] . "{".$row["response"]["status"]."}";
 	echo "\n";
 	}
 ?>