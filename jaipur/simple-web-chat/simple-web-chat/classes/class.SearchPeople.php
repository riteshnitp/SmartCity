<?php
/**
 * This class contains logic to find people
*/
class SearchPeople
{
    /**
     * Find groups and contacts of user
     * @param name name of user
    */
    function findPeople($name, $qk)
    {
        global $site_path, $fsds_obj;

        /*$op = array();
        if(strpos($_SERVER['SERVER_SIGNATURE'], '(Win') !== false) {
            $rtn = exec('dir /B '.$site_path.'files'.DIRECTORY_SEPARATOR.'un'.DIRECTORY_SEPARATOR.' | findstr '.$qk.'.*\.u', $op, $rv);
        } else {
            $rtn = exec('find \''.$site_path.'files'.DIRECTORY_SEPARATOR.'un'.DIRECTORY_SEPARATOR.'\' -name '.escapeshellarg($qk.'*.u'), $op, $rv);
        }*/
        $op = $fsds_obj->search('users', '', $qk.'*.u');
        if(is_array($op) && count($op) > 0) {
            $op = array_map(create_function('$val', 'return str_replace(\'.u\',\'\',basename($val));'), $op);
            // $fl = $site_path.'files'.DIRECTORY_SEPARATOR.'un'.DIRECTORY_SEPARATOR.$name.'.u';
            $uresnm = $name.'.u';
            $udtls = array();
            // if(is_file($fl) && file_exists($fl)) {
            if($fsds_obj->exists('users', $uresnm)) {
                // $udtl = file_get_contents($fl);
                $udtl = $fsds_obj->get('users', $uresnm);
                if(trim($udtl) != '') {
                    $udtls = @ json_decode($udtl, 1);
                    if(!is_array($udtls)) { $udtls = array(); }
                }
            }
            if(!isset($udtls['con']) || !is_array($udtls['con'])) { $udtls['con'] = array(); }
            if(!isset($udtls['conr']) || !is_array($udtls['conr'])) { $udtls['conr'] = array(); }
            $ops = array();
            foreach($op as $key => $val) {
                if(strpos($_SERVER['SERVER_SIGNATURE'], '(Win') !== false) {
                    $val = trim(substr($val, strrpos($val, ' ')));
                }
                if($val != $name && !in_array($val, $udtls['con']) && !in_array($val, $udtls['conr'])) {
                    $ops[$key] = $val;
                }
            }
            $op = $ops;
            unset($ops);
            if(array_search($name, $op) !== false) {
                $k = array_search($name, $op);
                unset($op[$k]);
                $op = array_values($op);
            }
        }
        return $op;
    }
}
?>