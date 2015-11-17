<?php
/* Get the query string "q" variable -- this is what the user typed in. */
$q = $_GET['q'];

/* Run some sort of searching operation.
			- Usually you will be searching a database and then pulling back results.
			- In this case, we're going to just use a simple array as our data source and find any matches. */
include('sample_data.php');
$results = array();
foreach ($fruits as $name => $data)
{
	if (stripos($name, $q) !== false)
	{
		$results[$name] = $data;
	}
}

/* Get the data into a format that Smart Suggest will read (see documentation). */
$final = array('header' => array(), 'data' => array());
$final['header'] = array(
													'title' => 'Fruit Search',		# Appears at the top of Smart Suggest result box
													'num' => count($results),			# Displayed as the total number of results.
													'limit' => 5									# An arbitrary number that you want to limit the results to.
												);
foreach ($results as $name => $data)
{
	$final['data'][] = array(
														'primary' => $name,																						# Title of result row
														'secondary' => $data['description'],													# Description below title on result row
														'image' => $data['image'],																		# Optional URL of 40x40px image
														'onclick' => 'alert(\'You clicked on the '.$name.' fruit!\');'	# JavaScript to call when this result is clicked on
													);
}

/* Output JSON */
header('Content-type: application/json');
echo json_encode(array($final)); # Put it as the first result in an array, since we only have one category here
die();
?>