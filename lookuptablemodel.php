<?
include_once APPPATH . 'libraries/utilities.php';
include_once APPPATH . 'libraries/enumerations.php';
include_once APPPATH . 'libraries/constants.php';

class LookupTableModel extends CI_Model {

    function getTableEntries($lookup, $activeOnly = true, $longOrShortDescription = 0, $includeBlank = false) {
        $retval = array();
        $sql = "select id, " . ($longOrShortDescription == 0 ? "description" : "short_description") .
         " from " . $lookup . "_lu" . ($activeOnly ? " where is_inactive = 0;" : ";"); 
        $query = $this->db->query($sql);
        $entries = $query->result();
        //echo $this->db->last_query() . "<br />";
        if ($includeBlank) $retval[0] = "";
        foreach($entries as $entry) {
            //echo "added " . $entry->description . " from $lookup<br />";
            $retval[$entry->id] = ($longOrShortDescription == 0 ? $entry->description : $entry->short_description); 
        }
        return $retval;
    }
    
    function getPseudoLookupTableEntries($tableName, $idColumnName = "id", $descriptionColumnName = "name", $whereClause = "", $orderClause = "", $includeBlankOption = false) {
        $retval = array();
        $sql = "select $idColumnName as id, $descriptionColumnName as description from $tableName";
        if ($whereClause != "") $sql .= " where $whereClause";
        $sql .= " order by " . ($orderClause == "" ? $descriptionColumnName : $orderClause);
        
        $query = $this->db->query($sql);
        $entries = $query->result();
        //echo $this->db->last_query() . "<br />";
        if ($includeBlankOption) $retval[0] = "";
        foreach($entries as $entry) {
            //echo "added " . $entry->description . " from $lookup<br />";
            $retval[$entry->id] = $entry->description; 
        }
        return $retval;
    }
}    
?>