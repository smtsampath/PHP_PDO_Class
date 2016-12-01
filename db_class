<?php

/**
 * @author Thushara Sathkumara
 * 
 */

require_once 'info.inc.php';

class Database {

    public static $m_pInstance;     # @object, Store the single instance of database
    private $host = DB_HOST;        # database server
    private $user = DB_USER;        # database login name
    private $pass = DB_PASS;        # database login password
    private $dbname = DB_NAME;      # database name
    private $pdo;                   # @object,  The PDO object
    private $stmt;                  # @object, PDO statement object
    private $isConnected = FALSE;   # @bool, Connected to the database
    private $params;                # @array, The parameters of the SQL query
    private $affected_rows = 0;     # @int, number of rows affected by SQL query
    private $results;               # @array, results of query

    /**
     *  Default Constructor
     *
     * 	1. Connect to database.
     * 	2. Creates the parameter array.
     */

    public function __construct() {
        $this->Connect();
        $this->params = array();
    }

    /**
     * 	This method makes connection to the database.
     *
     * 	1. Reads the database settings from a ini file.
     * 	2. Puts  the ini content into the settings array.
     * 	3. Tries to connect to the database.
     * 	4. If connection failed, exception is displayed and a log file gets created.
     */
    public function Connect() {
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname;
        $options = array(
            PDO::ATTR_PERSISTENT => true,
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
        );
        # Create a new PDO instanace
        try {
            $this->pdo = new PDO($dsn, $this->user, $this->pass, $options);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            # Connection succeeded, set the boolean to true.
            $this->isConnected = TRUE;
        } catch (PDOException $e) {
            # Return ErroCode Exception
            if(DEBUG) { # if debug mode is True
                $err = $e->getMessage();
            } else {
                # Send error message to the server log
                error_log($e->getMessage());
                $err = "Error Connecting To The Server";
            }
            throw new ErrorCodeException($err);
        }
    }

    /*
     *   disconnect from database
     *
     */

    public function Disconnect() {
        $this->pdo = NULL;
        $this->isConnected = FALSE;
    }

    /**
     * 	Every method which needs to execute a SQL query uses this method.
     *
     * 	1. If not connected, connect to the database.
     * 	2. Prepare Query.
     * 	3. Parameterize Query.
     * 	4. Execute Query.
     * 	5. On exception : Return Error Info.
     * 	6. Reset the Parameters.
     */
    private function Init($query, $params = "") {
        # Connect to database
        if (!$this->isConnected) {
            $this->Connect();
        }

        try {
            # Prepare query
            $this->stmt = $this->pdo->prepare($query);

            # Add parameters to the parameter array
            $this->bindMore($params);

            # Bind parameters
            if (!empty($this->params)) {
                foreach ($this->params as $param => $value) {
                    $type = PDO::PARAM_STR;
                    switch ($value[1]) {
                        case is_int($value[1]):
                            $type = PDO::PARAM_INT;
                            break;
                        case is_bool($value[1]):
                            $type = PDO::PARAM_BOOL;
                            break;
                        case is_null($value[1]):
                            $type = PDO::PARAM_NULL;
                            break;
                    }
                    # Add type when binding the values to the column
                    $this->stmt->bindValue($value[0], $value[1], $type);
                }
            }

            # Execute SQL
            $this->stmt->execute();
        } catch (PDOException $e) {
            # Return ErroCode Exception
            $err = ErrorCode::create(ErrorCode::DATABASE_ERROR, $e->getMessage());
            throw new ErrorCodeException($err);
        }

        # Reset the parameters
        $this->params = array();
    }

    /**
     * 	@void
     *
     * 	Add the parameter to the parameter array
     * 	@param string $param
     * 	@param string $value
     */
    public function bind($param, $value) {
        $this->params[sizeof($this->params)] = [":" . $param, $value];
    }

    /**
     * 	@void
     *
     * 	Add more parameters to the parameter array
     * 	@param array $parray
     */
    public function bindMore($parray) {
        if (empty($this->params) && is_array($parray)) {
            $columns = array_keys($parray);
            foreach ($columns as $i => &$column) {
                $this->bind($column, $parray[$column]);
            }
        }
    }

    /**
     *  If the SQL query  contains a SELECT or SHOW statement it returns an array containing all of the result set row
     * 	If the SQL statement is a DELETE, INSERT, or UPDATE statement it returns the number of affected rows
     *
     *  @param  string $query
     * 	@param  array  $params
     * 	@return mixed
     */
    public function query($query, $params = null) {

        # Query brake down
        $query = trim(str_replace("\r", " ", $query));

        $err = $this->Init($query, $params);
        $rawStatement = explode(" ", preg_replace("/\s+|\t+|\n+/", " ", $query));

        # Which SQL statement is used and convert to lower case
        $statement = strtolower($rawStatement[0]);

        if ($statement === 'insert' || $statement === 'update' || $statement === 'delete') {
            $this->affected_rows = $this->stmt->rowCount();
        }
        if ($statement === 'select' || $statement === 'show') {
            $this->results = $this->stmt->fetchAll();
        }
    }

    /**
     *  Returns the last inserted id.
     *  @return int
     */
    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }

    /**
     *  returns the number of rows for select or affected rows for update/delete.
     *  @return int
     */
    public function rowCount() {
        return $this->stmt->rowCount();
    }

    /**
     * 	fetches and returns results one line at a time from last query
     *
     *  @param  int    $row is the row number to retrieve, defaults to the first row
     * 	@return (array) fetched record(s)
     */
    public function single($row = 0) {
        if (!is_null($this->results) && $row >= 0 && $row < count($this->results)) {
            $record = $this->results[$row];
        } else {
            $record = null;
        }

        return $record;
    }

    /**
     * 	returns all the results (not one row) for the last query
     * 	@return (array) assoc array of ALL fetched results
     */
    public function resultset() {
        return $this->results;
    }

    /**
     *  Starts the transaction
     *  @return boolean, true on success or false on failure
     */
    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }

    /**
     *  Execute Transaction
     *  @return boolean, true on success or false on failure
     */
    public function executeTransaction() {
        return $this->pdo->commit();
    }

    /**
     *  Rollback of Transaction
     *  @return boolean, true on success or false on failure
     */
    public function rollBack() {
        return $this->pdo->rollBack();
    }

    /**
     *  dumps the the information that was contained in the Prepared Statement.
     *  @return array
     */
    public function debugDumpParams() {
        return $this->stmt->debugDumpParams();
    }

    /**
     *  singleton function;
     *  @return object
     */
    public static function getInstance() {
        if (!self::$m_pInstance) {
            self::$m_pInstance = new Database();
        }
        return self::$m_pInstance;
    }
}
