<?php
/**
 * w-slow Class
 *
 * @package w-slow
 * @copyright (C) 2010 John Cox
 *
 * @subpackage API
 */

class wSlow
{

    function __construct($dbuser, $dbpass, $dbhost)
    {
        $this->dbconn = mysql_connect($dbhost, $dbuser, $dbpass);
        if (!$this->dbconn) {
            return false;
        }
    }

    public function setSlowLog()
    {
        $query = "SET GLOBAL slow_query_log = 'ON';";
        if (!mysql_query($query)) return false;
        $query = "SET GLOBAL long_query_time = 1;";
        if (!mysql_query($query)) return false;
        $query = "SET GLOBAL log_output = 'TABLE';";
        if (!mysql_query($query)) return false;
        return true;
    }

    public function truncateSlowLog()
    {
        $query = 'TRUNCATE mysql.slow_log';
        $result = mysql_query($query);
        if ($result){
            $query = "SET GLOBAL slow_query_log = 'OFF';";
            if (!mysql_query($query)) return false;
            $query = "SET GLOBAL long_query_time = 10;";
            if (!mysql_query($query)) return false;
            return true;
        } else {
            return false;
        }
    }

    public function getSlowLog()
    {
        $log = array();
        $query = 'SELECT * FROM mysql.slow_log';
        if ($result = mysql_query($query)){
            while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
                $log[] = array('query_time'     => $row['query_time'],
                               'lock_time'      => $row['lock_time'],
                               'rows_sent'      => $row['rows_sent'],
                               'rows_examined'  => $row['rows_examined'],
                               'db'             => $row['db'],
                               'query'          => $row['sql_text']);
            }
            mysql_free_result($result);
            return $log;
        } else {
            return false;
        }
    }

    public function explainQuery($db, $incoming_query)
    {
        mysql_select_db($db) or die('Could not connect to MySQL database.');

        if ($result = mysql_query("EXPLAIN $incoming_query")){
            while ($row = mysql_fetch_assoc($result)){
                $log[] = array('select_type'    => $row['select_type'],
                               'table'          => $row['table'],
                               'type'           => $row['type'],
                               'possible_keys'  => $row['possible_keys'],
                               'key'            => $row['key'],
                               'key_len'        => $row['key_len'],
                               'ref'            => $row['ref'],
                               'rows'           => $row['rows'],
                               'extra'          => $row['Extra']);
            }
            mysql_free_result($result);
            return $log;
        } else {
            return false;
        }
    }

}
