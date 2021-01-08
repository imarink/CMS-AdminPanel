<?php
# Name: database.class.php
# File Description: MySQL Class for simple SQL execution
# Author: Ivan SJ
# Web: https://www.ivan-marinkovic.com.hr
# Update: 2009-11-03
# Version: 0.22

/*
 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

class Database{
	
	var $error="";
	var $errno=0;	var $affected_rows=0;
	var $num_rows=0;	var $link_id=0;
	var $query_id=0;
	var $show_error=1;
	var $gone=0;
	var $query_num=0;
	private $server="localhost";
	private $user="";
	private $pass="";
	private $database="";

	function Database($server, $user, $pass, $database){
		$this->server=$server;
		$this->user=$user;
		$this->pass=$pass;
		$this->database=$database;
		$this->connect();
	}
	
	function connect(){
		$this->link_id=@mysql_connect($this->server,$this->user,$this->pass,false);
		
		if (!$this->link_id)
			$this->error("Could not connect to server: <b>$this->server</b>.");
		if(!@mysql_select_db($this->database, $this->link_id))
			$this->error("Could not open database: <b>$this->database</b>.");
	}

	function close() {
		if(!@mysql_close($this->link_id))
			$this->error("Connection close failed.");
	}

	function escape($string) {
		$string = stripslashes($string);
		return mysql_real_escape_string($string);
	}

	function query($sql) {
		$this->query_num++;
		$this->query_id = @mysql_query($sql, $this->link_id);
		if (!$this->query_id) {
			if(mysql_errno($this->link_id)==2006 && $this->gone<6){
				$this->gone++;
				$this->close();
				$this->connect();
				return $this->query($sql);
			}
			$this->error("<b>Error during SQL execution:</b> $sql");
			return 0;
		}

		$this->affected_rows = @mysql_affected_rows($this->link_id);
		$this->num_rows = @mysql_num_rows($this->query_id);

		return $this->query_id;
	}

	function fetch_array($query_id=-1) {
		if ($query_id!=-1) 
			$this->query_id=$query_id;

		if (isset($this->query_id))
			$record = @mysql_fetch_assoc($this->query_id);
		else
			$this->error("Query not found.");
		
		return $record;
	}

	function fetch_first($query_string) {
		$query_id = $this->query($query_string." LIMIT 1");
		$out = $this->fetch_array($query_id);
		$this->free_result($query_id);
		return $out;
	}

	function fetch_all($sql) {
		$query_id = $this->query($sql);
		$out = array();

		while ($row = $this->fetch_array($query_id, $sql))
			$out[] = $row;

		$this->free_result($query_id);
		return $out;
	}

	function free_result($query_id=-1) {
		if ($query_id!=-1)
			$this->query_id=$query_id;

		if($this->query_id!=0 && !@mysql_free_result($this->query_id))
			$this->error("Result ID: <b>$this->query_id</b> could not be freed.");
	}

	function update($table, $data, $where='1') {
		$q="UPDATE `".$table."` SET ";

		foreach($data as $key=>$val) {
			if(strtolower($val)=='null') $q.= "`$key` = NULL, ";
			elseif(strtolower($val)=='now()') $q.= "`$key` = NOW(), ";
			elseif(preg_match("/$key([0-9\+\-])/i", $val) && substr($val, 0, strlen($key))==$key) $q.= "`$key`=".$this->escape($val).", ";
			else $q.= "`$key`='".$this->escape($val)."', ";
		}

		$q = rtrim($q, ', ') . ' WHERE '.$where.';';

		return $this->query($q);
	}

	function delete($table, $where='1') {		return $this->query("DELETE FROM `$table` where $where;");
	}

	function insert($table, $data) {
		$q="INSERT INTO `$table` ";
		$v=''; $n='';

		foreach($data as $key=>$val) {
			$n.="`$key`, ";
			if(strtolower($val)=='null') $v.="NULL, ";
			elseif(strtolower($val)=='now()') $v.="NOW(), ";
			else $v.= "'".$this->escape($val)."', ";
		}

		$q .= "(". rtrim($n, ', ') .") VALUES (". rtrim($v, ', ') .");";

		if($this->query($q))
			return mysql_insert_id();
		else return false;
	}

	function error($msg='') {
		if($this->link_id>0){
			$this->error=mysql_error($this->link_id);
			$this->errno=mysql_errno($this->link_id);
		}
		else{
			$this->error=mysql_error();
			$this->errno=mysql_errno();
		}
		
		if($this->show_error==1)
			die("
			<table align='center' border='1' cellspacing='0' style='background:white;color:black;width:80%;'>
			<tr><th colspan=2>Database Error</th></tr>
			<tr><td align='right' valign='top'>Message:</td><td>$msg</td></tr>
			".(strlen($this->error)>0 ? "<tr><td align='right' valign='top' nowrap>MySQL Error:</td><td>".$this->error."</td></tr><tr><td align='right' valign='top' nowrap>MySQL Errno:</td><td>".$this->errno."</td></tr>":"")."
			</table>");
		else
			die("mysql error");
	}
}
?>
