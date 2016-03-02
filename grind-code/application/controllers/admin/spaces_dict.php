<?
$environments = array("dev","prod");
$environmentsToSpaces = array("dev" => array("grind-park-avenue", "pirates-lasalle", "pirates-downtown", "pirates-broadway-30", "pirates-broadway"),
							  "prod" => array("grind-park-ave", "grind-lasalle-washington", "grind-broadway-39th"));
$environmentsToAccessToken = array("dev" => "3f0064f3d5f81c5100a4c62b070053f0badf3bf1c8d3f0bea32e59152b7d56d6",
							  	   "prod" => "bc19670d82bed73e62bfdf4cb67bb031ed9bbbdda702d2e5ad3ec29b0f5a11d5");
$spaceValMap = array("grind-park-avenue" => array('name' => 'PARK AVE'),
					 "pirates-lasalle" => array('name' => 'LA SALLE'),
					 "pirates-downtown" => array('name' => 'DOWNTOWN'),
					 "pirates-broadway-30" => array('name' => 'BROADWAY 30'),
					 "pirates-broadway" => array('name' => 'BROADWAY'),
					 "grind-park-ave" => array('name' => 'Grind Park Ave/29th'),
					 "grind-lasalle-washington" => array('name' => 'Grind LaSalle/Washington'),
					 "grind-broadway-39th" => array('name' => 'Grind Broadway/39th'));
$spaceToMainArea = array("grind-park-avenue" => 'c306ddebd5f7f14637bb85a29378b101',
						 "pirates-lasalle" => '5a96701a08a584f27bd710467ef0ab73',
						 "pirates-downtown" => '5a96701a08a584f27bd710467ef094ba',
						 "pirates-broadway-30" => 'd343f141b022d8e6d00298af5580eb7f',
						 "pirates-broadway" => 'd343f141b022d8e6d00298af5580e288',
						 "grind-park-ave" => 'c442be18f4974e41580c481804db4222',
						 "grind-lasalle-washington" => '5a96701a08a584f27bd710467ea7fc76',
						 "grind-broadway-39th" => '5a96701a08a584f27bd710467e1a7d99');
$spaceParams = array("capacity", "imgName", "lat", "long", "address", "rate", "name", "description");
$defaultParamValues = array(
							'capacity' => 100,
							'imgName' => 'grind-park-avenue.png',
							'lat' => '40.618',
							'long' => '-78.116',
							'address' => '419 Park Avenue South New York NY 10016',
							'rate' => 55,
							'name' => 'PARK AVE',
							'description' => 'Description Here...');

$environmentSpaces = array();
foreach ($environments as $environment) {
	foreach ($environmentsToSpaces[$environment] as $space) {
		$currSpaceMap = array();
		foreach ($spaceParams as $param) {
			$currParamMap = $spaceValMap[$space];
			$currParamVal = $defaultParamValues[$param];
			if (array_key_exists($param, $currParamMap)) {
			    $currParamVal = $currParamMap[$param];
			}
			if ($param == 'imgName') {
				$currParamVal = $space.'.png';
			}
			$currSpaceMap[$param] = $currParamVal;
		}
		$currSpaceMap['id'] = $space;
		$environmentSpaces[$space] = $currSpaceMap;
	}
}
?>