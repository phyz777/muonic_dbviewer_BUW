<?php

namespace BUW\PpSkVw;

use PDO;

class MysqlDbAccess extends DbAccess implements DbAccessIfc
{
    protected function openDbConn()
    {
        return new \PDO(
            parent::$db_prefix[$this->getDbType()] . "host=" . $this->getDbHost() . ";dbname=" . $this->getDbBase(),
            $this->getDbUser(), $this->getDbPass()
        );
    }
    protected function closeDbConn()
    {
        return null;
    }

    //Returns an associative, multi dimensional array
    //  Keys are supported request types
    //  Values are 2d arrays, where [0] gives the original request and [1] the reply
    public function getDataFromDb($args)
    {
        $result_arr = [];
        $tmp_pdo_obj = null;
        if (!empty($args["query"])) {
            $tmp_pdo_obj = $this->db_handle->query($args["query"]);
            $result_arr["query"] = [$args["query"], $tmp_pdo_obj->fetchAll(PDO::FETCH_BOTH)];
        }
        if (!empty($args["multiQuery"])) {
            $tmp_arr = [];
            foreach ($args["multiQuery"] as $q) {
                $tmp_pdo_obj = $this->db_handle->query($q);
                array_push($tmp_arr, $tmp_pdo_obj->fetchAll(PDO::FETCH_BOTH));
            }
            $result_arr["multiQuery"] = [$args["multiQuery"], $tmp_arr];
        }
        if (!empty($args["table"])) {
            $tmp_pdo_obj = $this->db_handle->query("SELECT * FROM " . $args["table"] . ";");
            $result_arr["table"] = [$args["table"], $tmp_pdo_obj->fetchAll(PDO::FETCH_BOTH)];
        }
        if (!empty($args["multiTable"])) {
            $tmp_arr = [];
            foreach ($args["multiTable"] as $t) {
                $tmp_pdo_obj = $this->db_handle->query("SELECT * FROM " . $t . ";");
                array_push($tmp_arr, $tmp_pdo_obj->fetchAll(PDO::FETCH_BOTH));
            }
            $result_arr["multiTable"] = [$args["multiTable"], $tmp_arr];
        }
        if (!empty($args["describe"])) {
            $tmp_pdo_obj = $this->db_handle->query("DESCRIBE " . $args["describe"] . ";");
            $result_arr["describe"] = [$args["describe"], $tmp_pdo_obj->fetchAll(PDO::FETCH_BOTH)];
        }
        if (!empty($args["multiDescribe"])) {
            $tmp_arr = [];
            foreach ($args["multiDescribe"] as $d) {
                $tmp_pdo_obj = $this->db_handle->query("DESCRIBE " . $d . ";");
                array_push($tmp_arr, $tmp_pdo_obj->fetchAll(PDO::FETCH_BOTH));
            }
            $result_arr["multiDescribe"] = [$args["multiDescribe"], $tmp_arr];
        }
        if (!empty($args["show"])) {
            $tmp_pdo_obj = $this->db_handle->query("SHOW " . $args["show"] . ";");
            $result_arr["show"] = [$args["show"], $tmp_pdo_obj->fetchAll(PDO::FETCH_BOTH)];
        }
        if (!empty($args["multiShow"])) {
            $tmp_arr = [];
            foreach ($args["multiShow"] as $s) {
                $tmp_pdo_obj = $this->db_handle->query("SHOW " . $s . ";");
                array_push($tmp_arr, $tmp_pdo_obj->fetchAll(PDO::FETCH_BOTH));
            }
            $result_arr["multiShow"] = [$args["multiShow"], $tmp_arr];
        }
  /*    if(!empty($args["template"])){
        array_push($tmp_arr["template"], null);
      }
      if(!empty($args["multiTemplate"])){
        foreach($args["multiTemplate"] as $s){
          array_push($tmp_arr["multiTemplate"], null);
        }
      }*/
        return $result_arr;
    }
    public function writeDataToDb($args)
    {
        ;
    }
    public function dbToHtml($args)
    {
        $tbl_ind = $this->db_handle->query("SHOW TABLES;")->fetchAll(PDO::FETCH_ASSOC);
        $tbl_arr = ["tables"=>$tbl_ind];
        $info_arr = [];
        foreach ($tbl_ind as $ind) {
            $t = $ind["Tables_in_" . $this->getDbBase()];
            if (array_key_exists('table', $args) && $t !== $args['table']) {
              continue;
            }
            if (array_key_exists('where_field', $args) && array_key_exists('where_value', $args)) {
              $tbl_arr[$t]=$this->db_handle->query(
                "SELECT * FROM " . $t . " WHERE `" . $args['where_field'] . "`='" . $args['where_value'] . "';"
              )->fetchAll(PDO::FETCH_ASSOC);
            } else {
              $tbl_arr[$t]=$this->db_handle->query("SELECT * FROM " . $t . ";")->fetchAll(PDO::FETCH_ASSOC);
            }
            $tbl_info = $this->db_handle->query("DESCRIBE " . $t . ";")->fetchAll(PDO::FETCH_ASSOC);
            $info_arr[$t] = [];
            foreach ($tbl_info as $col) {
                if ($col["Key"] == "MUL") {
                    array_push($info_arr[$t], $col["Field"]);
                }
            }
        }

        $result_str  = "  <div id=\"tablesDiv\" data-ng-controller=\"tablesController\">\n";

        //iterate over tables
        foreach ($tbl_ind as $ind) {
            $t = $ind["Tables_in_" . $this->getDbBase()];
            if (!empty($tbl_arr[$t])) {
                $result_str .= "  <h1 data-ng-init=\"hide_" . $t . "=false\" data-ng-click=\"hide_" . $t . "=!hide_" . $t . "\">" . $t . "</h1>\n";
                $result_str .= "  <div data-ng-class=\"{hideTable: hide_" . $t . "}\">";
                $result_str .= "  <table data-ng-class=\"{hideTable: hide_" . $t . "}\">\n";
                $result_str .= "    <tr data-ng-class=\"{hideTable: hide_" . $t . "}\">\n";
                //add table header
                foreach (array_keys($tbl_arr[$t][0]) as $h) {
                    $result_str .= "      <th data-ng-class=\"{hideTable: hide_" . $t . "}\">" . $h . "</th>\n";
                }
                $result_str .= "    </tr>\n";
                //iterate over rows
                foreach ($tbl_arr[$t] as $r) {
                    $result_str .= "    <tr data-ng-class=\"{hideTable: hide_" . $t . "}\">\n";
                    //iterate over cols
                    foreach ($r as $k=>$c) {
                        if (in_array($k, $info_arr[$t])) {
                            $result_str .= "      <td data-ng-class=\"{hideTable: hide_" . $t . "}\" data-ng-click=\"rdrct('" . $t . "','" . $k . "','" . $c . "')\">" . $c . "</td>\n"; //TODO: Add custom class
                        } else {
                            $result_str .= "      <td data-ng-class=\"{hideTable: hide_" . $t . "}\" data-ng-click=\"rdrct('" . $t . "','" . $k . "','" . $c . "')\">" . $c . "</td>\n";
                        }
                    }
                    $result_str .= "    </tr>\n";
                }
                $result_str .= "  </table>\n";
                $result_str .= "  </div>";
            }
        }

        $result_str .= "  </div>\n";

        print_r($result_str);
    }
    public function dbToJSON($args)
    {
        $tbl_ind = $this->db_handle->query("SHOW TABLES;")->fetchAll(PDO::FETCH_ASSOC);
        $ret_arr = ["tables"=>$tbl_ind];
        foreach($tbl_ind as $ind){
            $t = $ind["Tables_in_" . $this->getDbBase()];
            $ret_arr[$t]=$this->db_handle->query("SELECT * FROM " . $t . ";")->fetchAll(PDO::FETCH_ASSOC);
        }
        return json_encode($ret_arr, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT | JSON_PRESERVE_ZERO_FRACTION);
    }
    public function backupDbInFile($f_name, $args)
    {
        ;
    }
    public function restoreDbFromFile($f_name, $args)
    {
        ;
    }
    public function debugFunction($args)
    {
        ;
    }
}
