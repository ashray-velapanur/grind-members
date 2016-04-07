<?php
class Cron extends CI_Controller {

    public function message($to = 'World')
    {
        echo "Hello {$to}!".PHP_EOL;
    }

    public function reset_checkins() {
        error_log('cron job: resetting checkin count');
        $sql = "update cobot_spaces set checkins=0";
        $this->db->query($sql);
    }
}
?>