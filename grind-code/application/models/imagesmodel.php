<?
include_once APPPATH . 'libraries/utilities.php';
include_once APPPATH . 'libraries/enumerations.php';
include_once APPPATH . 'libraries/constants.php';

class ImagesModel extends CI_Model {

	function save_image($image) {
		error_log(json_encode($image));
		$image_id = NULL;
		$imgName = $image['tmp_name'];
		$imgType = $image['type'];
		$fp = fopen($imgName, 'r');
		if($fp) {
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
		}
		return $image_id;
	}

	function delete_image($image_id) {
		if($image_id) {
			$sql = "DELETE FROM image WHERE id='".$image_id."'";
			error_log($sql);
			if ($this->db->query($sql) === TRUE) {
				error_log("Deleted image successfully");
			} else {
				error_log("Error: " . $sql . "<br>" . $this->db->error);
			}
		}
	}
};
?>