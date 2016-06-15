<?
$environments = array("dev","prod");
$environmentsToSpaces = array("dev" => array("grind-park-avenue", "pirates-lasalle", "pirates-downtown", "pirates-broadway-30", "pirates-broadway"),
							  "prod" => array("grind-park-ave", "grind-lasalle-washington", "grind-broadway-39th"));
$environmentsToAccessToken = array("dev" => "d1a9e9cb5360ccf114630818aaf0f6c057a13f49bec1c666513a26e5da050730",
							  	   "prod" => "6cc06d0a4eaeb4ee3e26af9e9931b07f720454ffcb0293fabd5aaba98828ae65");
$spaceValMap = array("grind-park-avenue" => array('name' => 'PARK AVE'),
					 "pirates-lasalle" => array('name' => 'LA SALLE'),
					 "pirates-downtown" => array('name' => 'DOWNTOWN'),
					 "pirates-broadway-30" => array('name' => 'BROADWAY 30'),
					 "pirates-broadway" => array('name' => 'BROADWAY'),
					 "grind-park-ave" => array('name' => 'Park Ave/29th'),
					 "grind-lasalle-washington" => array('name' => 'LaSalle'),
					 "grind-broadway-39th" => array('name' => 'Broadway/39th'));
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
							'description' => '<div style=\"color:#CCD1D9;font-family:\'Montserrat\', sans-serif;font-size: 14px;\">'.
												 '<p>'.
												 	"Grind Park is Grind's first location, built in 2011. It is predominantly open, non-dedicated space, but contains Team Rooms and Conference Rooms in addition, making it a home for almost all of Grind's varied memberships.".
												 '</p>'.
												 '<h6 style=\'color:#F15A40;\'>AMENITIES</h6>'.
												 '<ol>'.
												 	'<li>One 8 person conference room</li>'.
												 	'<li>One 4-5 person conference room</li>'.
												 	'<li>Two 2-3 person chat rooms</li>'.
												 	'<li>Private phone booths</li>'.
												 	'<li>Kitchen</li>'.
												 	'<li>Event Space</li>'.
												 	'<li>Intelligentsia coffee and tea</li>'.
												 	'<li>Purified water</li>'.
												 	'<li>Complimentary snacks</li>'.
												 	'<li>AV equipment</li>'.
												 	'<li>High speed WiFi and ethernet</li>'.
												 	'<li>Printer, copier and scanner access</li>'.
												 	'<li>Full time, on-site community management</li>'.
												 	'<li>Access to the national Grind network</li>'.
												 	'<li>Storage (for monthly subscribers)</li>'.
												 '</ol>'.
											'</div>'
							);

$spacePlansMap = array(
				"dev" => array(
				'pirates-broadway' => '6fe700cd7bf83143c199a44fa02edad8',
				'pirates-broadway-30' => 'a3973ddc12829361f3c2b375aef30d2c',
				'pirates-downtown' => '6fe700cd7bf83143c199a44fa0110123',
				'pirates-lasalle' => 'a3973ddc12829361f3c2b375aeb67691',
				'grind-park-avenue' => 'e5b600eee3b540ddf47dae1f0a71c7d3'),
				"prod" => array(
				'grind-broadway-39th' => '8055361a86538eac54ef5a46ef62beda',
				'grind-lasalle-washington' => '8055361a86538eac54ef5a46effc0279',
				'grind-park-ave' => 'da3041b37c8a05b04b59250713e2656f')
				);

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