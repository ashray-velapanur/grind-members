<?php
// Location in repository: member/ci/application/models/spacemodel.php

include_once APPPATH . 'libraries/utilities.php';
include_once APPPATH . 'libraries/enumerations.php';
include_once APPPATH . 'libraries/constants.php';

class SpaceModel extends CI_Model {

	// TODO: Show available capacity in name
	function wpGetSpacesList() {
		$sql = array();
		$sql[] = "select space.id as id, concat_ws(' ', location.name, '-', space.name) as name from space";
		$sql[] = "join location on space.location_id = location.id";
		//$sql[] = "where space.is_bookable = 1";
		//$sql[] = "and space.capacity > 0";
		$sql[] = "and space.is_inactive = 0";

		$query = $this->db->query(implode(' ', $sql));

		$results = array();
		foreach ($query->result_array() as $row) {
			$results[$row['id']] = $row['name'];
		}

		$query->free_result();
		return $results;
	}
    
    function getSpaceList(){
        $sql = "SELECT space.id, location.name as location_name, space.name, space.ss_schedule, space.description from space left join location on location.id=space.location_id";
        $query = $this->db->query($sql);
        $results = $query->result_array();
        $ret = array();
        foreach($results as $key => $space){
            $ret[$space['location_name']][$space['id']] = array('name' => $space['name'],'ss_schedule' => $space['ss_schedule'], 'description' => $space['description']);
        }
        return $ret;
    }

}