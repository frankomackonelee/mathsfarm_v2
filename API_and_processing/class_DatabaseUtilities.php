<?php
	/**
	 * A class to hold the database connection and functions to assist with any error reporting
	 * for queries etc which might be needed
	 */
	class DatabaseUtilities {
		protected $_queryLog = array();
		protected $_errorLog = array();
		protected $_sql;
		protected $_keyList;
		protected $_valueList;
		protected $_conditionList;
		protected static $mysqli;
		private $_success = TRUE;
	
		function __construct() {
			self::$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DB);
			if (self::$mysqli->connect_error) {
			    $this->$_success = FALSE;
				$this->_errorLog[] = "Problem connecting to database";
			}
			// Change character set to utf8
			mysqli_set_charset(self::$mysqli,"utf8");
		}
	
		public function printQueryLog()
		{
			$text = "";
			foreach ($this->_queryLog as $loggedItem) {
				$text .= "sql: ".$loggedItem['sql']."<br> count: ".$loggedItem['count']."<br>";
			}
			return $text;
		}
		
		public function printErrorLog()
		{
			$text = "";
			foreach ($this->_errorLog as $loggedItem) {
				$text .= "<br>error: ".$loggedItem;
			}
			return $text;
		}
	
		public function getSql()
		{
			return $this->_sql;
		}
	
		public function getSuccess()
		{
			return $this->_success;
		}		
		
		public function escapeMe($stringOrArrayOfStrings)
		{
			$output;
			if (is_array($stringOrArrayOfStrings)) {
				$output = array();
				foreach ($stringOrArrayOfStrings as $key => $str) {
					$output[$key] = mysqli_real_escape_string(self::$mysqli, $str);
				}
			} else {
				$output = mysqli_real_escape_string( self::$mysqli, $stringOrArrayOfStrings);
			}
			return $output;
		}
		
		protected function makeLists($arr, $valueSpacer)
		{
			$this->_keyList = "";
			$this->_valueList = "";
			$count = 0;
			$arrLength = count($arr);
			foreach ($arr as $key => $value) {
				//TODO need to escape column and value carefully
				$spacer = ($count===0 ? "" : $valueSpacer);
				$this->_keyList .= $spacer . $key;
				/*
				$done = FALSE;
				//TODO: this checks the type of the individula value, it doesn't look at the type of the column, which could cause problems...
				//also checks that the string doesn't start and end with an inverted comma
				if(gettype($value) == "string"){
					//(strrpos($value, "'") == 0 && stripos($value, "'") == (strlen($value)-1))
					if ($arrLength != 1){
						$this->_valueList .= $spacer . '"' . $value . '"';
						$done = TRUE;
					}
				}
				 * 
				 */
				$this->_valueList .= $spacer . $value;
				$count++;
			}		
		}
	
		protected function makeConditions($arr, $setNotation)
		{
			$count = 0;
			$this->_conditionList = "";
			foreach ($arr as $key => $value) {
				//TODO need to escape column and value carefully
				$spacer = ($count===0 ? "" : $setNotation);
				$this->_conditionList .= $spacer.$key."=" . $value;
				$count++;
			}		
		}	
		
		protected function queryDatabase($sql)
		{
			//$sql = mysqli_real_escape_string(self::$mysqli, $sql);
			return self::$mysqli->query($sql);	
		}
	}

?>