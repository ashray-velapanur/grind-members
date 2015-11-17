<?

class LDAPManagement extends CI_Controller {

    function __construct() {
            parent::__construct();
            
            $this->load->helper("url");
            $this->load->helper("form");

    }

    function authenticate() {
        
        
        // first check to see if the mac address is already stored
        $mac = $_POST["mac"];
        $this->load->model("usermodel");
        $this->load->model("issuesmodel");
        $associatedUserId = $this->usermodel->getUserIdFromMACAddress($mac);
        
        $issueId = $this->issuesmodel->logMemberIssue(0, "Starting sign-in process",  MemberIssueType::SIGNIN);
        $this->issuesmodel->closeMemberIssue($issueId);

        if ($associatedUserId > 0) {
            // have the userId, so simply sign them in
            $issueId = $this->issuesmodel->logMemberIssue($associatedUserId, "User " . $associatedUserId . " is associated with mac address " . $mac,  MemberIssueType::SIGNIN);
            $this->issuesmodel->closeMemberIssue($issueId);
            echo 1;
        
        } else {
            // mac not cached, so attempt to authenticate

            // LDAP variables
            $ldaphost = "50.56.126.47";  // your ldap servers
            $ldapport = 389;             // your ldap server's port number
            
            // Connecting to LDAP
            $ds = ldap_connect($ldaphost, $ldapport) or die("Could not connect to $ldaphost");
            
            if ($ds) {
                if (isset($_POST['u']) && isset($_POST['p'])) {
                    if($bind=@ldap_bind($ds, "uid=".$_POST['u'].",ou=People,dc=grindspaces,dc=com", $_POST['p'])){
                        // cache the mac address for this user; first get the user's ID
                        $associatedUserId = $this->usermodel->getUserIdFromWPLogin($_POST["u"]);
                        if ($mac != "") {
                            $this->usermodel->addMACAddress($associatedUserId, $mac);
                        }
                        $issueId = $this->issuesmodel->logMemberIssue($associatedUserId, "User " . $_POST['u'] . " has be authenticated!",  MemberIssueType::SIGNIN);
                        $this->issuesmodel->closeMemberIssue($issueId);
                        echo 1;
                    }
                    else{
                        $issueId = $this->issuesmodel->logMemberIssue(0, "Could not auth " . $_POST['u'] . "/" . $_POST['p'] . "to the LDAP server",  MemberIssueType::SIGNIN);
                        $this->issuesmodel->closeMemberIssue($issueId);
                        echo 0;
                    }
                } else {
                    $issueId = $this->issuesmodel->logMemberIssue(0, "The user name and password were not set via the post parameters",  MemberIssueType::SIGNIN);
                    $this->issuesmodel->closeMemberIssue($issueId);
                    echo 0;
                }
            } else {
                $issueId = $this->issuesmodel->logMemberIssue(0, "Could not get connection to LDAP server",  MemberIssueType::SIGNIN);
                $this->issuesmodel->closeMemberIssue($issueId);
                echo 5;
            }
        }
    }
}
?>