<?
include_once APPPATH . 'libraries/utilities.php';
include_once APPPATH . 'libraries/enumerations.php';
include_once APPPATH . 'libraries/constants.php';

class ImagesModel extends CI_Model {

	function save_image($image) {
		$image_id = NULL;
		$imgName = $image['tmp_name'];
		$imgType = $image['type'];
		$fp = fopen($imgName, 'r');
		$content = fread($fp, filesize($imgName));
		$content = addslashes($content);
		fclose($fp);
		
		$data = array(
			'content' => $content,
			'type' => $imgType
		);
		$sql = "INSERT INTO image (content, type) VALUES('$content', '$imgType')";
		error_log($sql);
		if ($this->db->query($sql) === TRUE) {
			$image_id = $this->db->insert_id();
			error_log("Image added");
		} else {
			error_log("Error: " . $this->db->error);
		}
		return $image_id;
	}
};
?>