<?php
namespace bpaulin\dbexplorer;

/**
* DbEplorer
*/
class DbExplorer
{
    protected $host;
    protected $user;
    protected $pass;
    protected $base;
    protected $table;

    protected $db;

    public function connect($host, $user, $pass, $base, $table)
    {
        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
        $this->base = $base;
        $this->table = $table;
        $this->db = new mysqli($host, $user, $pass, $base);

        return $this->db;
    }

    public function describeTable()
    {
        $retour = array();
        if ($result = $this->db->query("DESCRIBE `".$this->base . "`.`" . $this->table . "`")) {
            while ($row = $result->fetch_assoc()) {
                $retour[] = $row;
            }
        }

        return $retour;
    }

    public function select($field, $strict, $value)
    {
        $query = "SELECT *
                  FROM `".$this->base . "`.`" . $this->table . "`";
        if ($field) {
            if ($strict) {
                $compare = "='{$this->db->escape_string($value)}'";
            } else {
                $compare = "LIKE '%{$this->db->escape_string($value)}%'";
            }
            $query .= "WHERE {$this->db->escape_string($field)} $compare";
        }
        $retour = array();
        if ($result = $this->db->query($query)) {
            while ($row = $result->fetch_assoc()) {
                $retour[] = $row;
            }
        }

        return $retour;
    }
}
