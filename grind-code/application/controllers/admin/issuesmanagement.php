<?

class IssuesManagement extends CI_Controller {

    function __construct() {
            parent::__construct();
            
            $this->load->helper("url");
            $this->load->helper("form");
			$this->load->helper("nav_location");
			$this->load->helper("admin_layout");

    }

    function index() {
        $this->load->model("issuesmodel");
        $issueId = $this->uri->segment(4);

        $data["issues"] = $this->issuesmodel->getMemberIssues(true, 0, 5);
        $data["admin_only"] = true;
        
		display('admin/issues',$data,"Issues");
    }
    
    function closeIssue() {
        
    }
}
?>
        