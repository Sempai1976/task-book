<?php

class InstallDB
{
    private $hostname;
    private $username;
    private $password;
    private $database;
    private $connect;

    function __construct()
    {
        include(__DIR__."/../../config/database.php");
        
        $this->hostname = $db['hostname'];
        $this->username = $db['username'];
        $this->password = $db['password'];
        $this->database = $db['database'];
        $this->connect = $this->getConnection();
    }
    
    private function getConnection()
    {
        $conn = new \mysqli($this->hostname, $this->username, $this->password, $this->database);
        
        if ($conn->connect_errno) {
            trigger_error("Problem with connecting to database.");
        }
        
        $conn->set_charset("utf8");
        return $conn;
    }
    
    public function check_database() 
    {
		$sqlFile =__DIR__."/../sql/schema.sql";
        if ( ! file_exists($sqlFile))
		{
			return FALSE;
		}

		$tables = [];
        $sql = "SHOW TABLES FROM `$this->database`";
        if ($result = $this->connect->query($sql)) {
            while ($row = $result->fetch_row()) {
                $tables[] = $row[0];
            }
        }

        if (count($tables) == 0) {
            $this->install_tables($sqlFile);
        }
	}

    protected function install_tables($file) 
    {
//      $this->connect->query("SET NAMES `utf8`");
		$sqlFileData = '';
		if (function_exists('file_get_contents'))
		{
			$sqlFileData = file_get_contents($file);
		}

        if ( empty($sqlFileData))
		{
		    if ( ! $fp = @fopen($file, 'rb'))
		    {
			    return FALSE;
		    }

		    flock($fp, LOCK_SH);

		    $sqlFileData = '';
		    if (filesize($file) > 0)
		    {
			    $sqlFileData =& fread($fp, filesize($file));
		    }

		    flock($fp, LOCK_UN);
		    fclose($fp);

		    if ( empty($sqlFileData))
		    {
			    return FALSE;
		    }
		}

        $queries = explode(";\n", str_replace(';\r\n', ';\n', $sqlFileData));

        foreach ($queries as $q) {
            $q = trim($q);

            if (!empty($q)) {
                $this->connect->query($q);
            }
        }
        
        rename($file, __DIR__."/../sql/_schema.sql");
	}
}