<?php
    class Database{
        private $host = "silva.computing.dundee.ac.uk";
        private $db_name = "22ac3d05";
        private $username = "";
        private $password = "";
        private $connection;

        function __construct($dbuser = "default"){
            $db_access = fopen("db_access.txt", "r");
            $credentials = array();

            while(!feof($db_access)){
                $tokens = explode(":", fgets($db_access));
                $credentials[$tokens[0]] = $tokens[1];
            }

            $this->username = trim($credentials[$dbuser]);
            $this->password = trim($credentials["password"]);
        }

        function connect(){
            try{
                $this->connection = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->db_name,
                $this->username, 
                $this->password);
            }
            catch(PDOException $e){
                echo $this->username;
                echo $this->password;
                die();
            }
        }

        function GetPasswordHash($emailIn){
            $q = $this->connection->prepare("CALL GetPasswordHash(:emailIn)");
            $q->bindParam(":emailIn", $emailIn);

            $q->execute();

            foreach($q->fetchAll() as $result){
                return $result["password"];
            }

            throw new InvalidArgumentException("Invalid email.");
        }

        function GetEmployeeRole($emailIn){
            $q = $this->connection->prepare("CALL GetEmployeeRole(:emailIn)");
            $q->bindParam(":emailIn", $emailIn);

            $q->execute();

            foreach($q->fetchAll() as $result){
                return $result["roleID"];
            }

            throw new InvalidArgumentException("Invalid email.");
        }
    }