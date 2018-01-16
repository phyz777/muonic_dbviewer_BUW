<?php

namespace BUW\PpSkVw;

interface DbAccessIfc
{
    public function getDataFromDb($args);
    public function writeDataToDb($args);
    public function dbToHtml($args);
    public function dbToJSON($args);
    public function backupDbInFile($f_name, $args);
    public function restoreDbFromFile($f_name, $args);
}
