<?php

namespace BUW\PpSkVw;

use Exception;

abstract class DbAccess
{
    public function __construct($h, $u, $p, $b, $t)
    {
        $this->db_host = $h;
        $this->db_user = $u;
        $this->db_pass = $p;
        $this->db_base = $b;
        $this->db_type = $t;
        if(!array_key_exists($t, self::$db_prefix)){
            throw new \Exception("Unsupported database type");
        }
        $this->db_handle = $this->openDbConn();
    }
    public function __destruct()
    {
        $this->db_handle = $this->closeDbConn();
    }

    abstract protected function openDbConn();
    abstract protected function closeDbConn();

    protected function getDbHost()
    {
        return $this->db_host;
    }
    protected function getDbUser()
    {
        return $this->db_user;
    }
    protected function getDbPass()
    {
        return $this->db_pass;
    }
    protected function getDbBase()
    {
        return $this->db_base;
    }
    protected function getDbType()
    {
        return $this->db_type;
    }
    public static function listDbTypes()
    {
        foreach (array_keys(self::$db_prefix) as $d) {
            echo($d . "<br/>\n");
        }
    }
    private $db_host;
    private $db_user;
    private $db_pass;
    private $db_base;
    private $db_type;
    protected static $db_prefix = array(
        "CUBRID"=>"cubrid:", "Firebird"=>"firebird:", "MySQL"=>"mysql:",
        "Oracle"=>"oci:", "DB2"=>"odbc:", "OBDC"=>"obdc:",
        "DB2/OBDC"=>"obdc:", "PostgreSQL"=>"pgsql:"
    );
    protected $db_handle;
}
