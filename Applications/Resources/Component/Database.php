<?php
/**
 * Database Component
 *
 * @author  nawa <nawa@yahoo.com>
 * @version 1.0.0
 * @license MIT
 */
namespace MalangPhp\Site\Conf\Component;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\DriverManager;
use MalangPhp\Site\Conf\Aggregator\DatabaseQueryBuilder;

/**
 * Class Database
 * @package MalangPhp\Site\Conf\Component
 */
class Database
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $connection;

    /**
     * @var string
     */
    protected $driver;

    /**
     * @var array
     */
    protected $userParams = [
        'charset' => 'UTF8',
        'collate' => 'utf_general_ci'
    ];

    /**
     * @var string
     */
    protected $tablePrefix = '';

    /**
     * @var string
     */
    protected $quoteIdentifier = '"';

    /**
     * Database constructor.
     * @param array $configDb database Configuration
     * @throws DBALException
     */
    public function __construct(array $configDb)
    {
        $this->userParams = array_merge($this->userParams, $configDb);
        if (!isset($this->userParams['driver'])) {
            throw new DBALException('Driver must be declare.', E_USER_ERROR);
        }
        if (!is_string($this->userParams['driver'])) {
            throw new DBALException('Driver must as a string.', E_USER_ERROR);
        }
        $this->driver = $this->sanitizeSelectedAvailableDriver($this->userParams['driver']);
        if (!$this->driver) {
            throw new DBALException('Selected driver unavailable.', E_USER_ERROR);
        }

        if (isset($this->userParams['prefix']) && is_string($this->userParams['prefix'])) {
            $this->userParams['prefix'] = trim($this->userParams['prefix']);
            $this->tablePrefix = (string) $this->userParams['prefix'];
        }

        if ($this->driver == 'pdo_sqlite' && !isset($this->userParams['path'])) {
            if (!isset($this->userParams['dbname']) || !is_string($this->userParams['dbname'])) {
                throw new DBALException('SQLite database path must be not empty.', E_USER_ERROR);
            }
            $this->userParams['path'] = $this->userParams['dbname'];
        }

        // create new params
        $connectionParams = $this->userParams;
        // unset
        unset($connectionParams['prefix'], $connectionParams['driver']);

        $this->connection = DriverManager::getConnection($connectionParams);
        $this->quoteIdentifier = $this->connection->getDatabasePlatform()->getIdentifierQuoteCharacter();
    }

    /**
     * Getting Doctrine Connection
     *
     * @return \Doctrine\DBAL\Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Get Table Prefix
     *
     * @return string
     */
    public function getTablePrefix()
    {
        return $this->tablePrefix;
    }

    /**
     * Get identifier
     *
     * @return string
     */
    public function getQuoteIdentifier()
    {
        return $this->quoteIdentifier;
    }

    /**
     * Get user params
     *
     * @return array
     */
    public function getUserParams()
    {
        return $this->userParams;
    }

    /**
     * Get Connection params
     *
     * @return array
     */
    public function getConnectionParams()
    {
        return $this->getConnection()->getParams();
    }

    /**
     * Check Database driver available for Doctrine
     * and choose the best driver of sqlsrv an oci
     *
     * @param string $driverName
     * @final
     * @return bool|string return lowercase an fix database driver for \Doctrine\DBAL\Connection
     */
    final public function sanitizeSelectedAvailableDriver($driverName)
    {
        if (is_string($driverName) && trim($driverName)) {
            $driverName = trim(strtolower($driverName));
            if (in_array($driverName, DriverManager::getAvailableDrivers())) {
                // switch to Doctrine fixed db
                switch ($driverName) {
                    case 'sqlite':
                        return 'pdo_sqlite';
                    case 'mysql':
                        return 'mysql';
                    case 'drizzle':
                        return 'drizzle_pdo_mysql';
                    case 'pgsql':
                        return 'pdo_sqlsrv';
                    case 'pdo_oci':
                    case 'oci':
                        return 'oci8';
                    case 'pdo_sqlsrv':
                        return 'sqlsrv';
                }
                return $driverName;
            }
        }

        return false;
    }

    /**
     * Getting QueryBuilder
     *
     * @return DatabaseQueryBuilder
     */
    public function queryBuilder()
    {
        return new DatabaseQueryBuilder($this);
    }

    /**
     * Getting QueryBuilder
     *
     * @return DatabaseQueryBuilder
     */
    public function databaseQueryBuilder()
    {
        return new DatabaseQueryBuilder($this);
    }

    /**
     * Close Connection
     */
    public function close()
    {
        $this->getConnection()->close();
    }

    /**
     * Ping Connection
     *
     * @return bool
     */
    public function ping()
    {
        return $this->getConnection()->ping();
    }

    /**
     * Trimming table for safe usage
     *
     * @param mixed $table
     * @return mixed
     */
    public function trimSelector($table)
    {
        if (is_array($table)) {
            foreach ($table as $key => $value) {
                $table[$key] = $this->trimSelector($value);
            }
            return $table;
        } elseif (is_object($table)) {
            foreach (get_object_vars($table) as $key => $value) {
                $table->{$key} = $this->trimSelector($value);
            }
            return $table;
        }
        if (is_string($table)) {
            $tableArray = explode('.', $table);
            foreach ($tableArray as $key => $value) {
                $tableArray[$key] = trim(
                    trim(
                        trim($value),
                        $this->quoteIdentifier
                    )
                );
            }
            $table = implode('.', $tableArray);
        }

        return $table;
    }

    /**
     * Alternative multi variable type quoted identifier
     *
     * @param mixed $quoteStr
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function quoteIdentifier($quoteStr)
    {
        if ($quoteStr instanceof \Closure || is_resource($quoteStr)) {
            throw new \InvalidArgumentException(
                "Invalid value to be quote, quote value could not be instance of `Closure` or as a `Resource`"
            );
        }

        $quoteStr = $this->trimSelector($quoteStr);
        if (is_array($quoteStr)) {
            foreach ($quoteStr as $key => $value) {
                $quoteStr[$key] = $this->quoteIdentifier($value);
            }
            return $quoteStr;
        } elseif (is_object($quoteStr)) {
            foreach (get_object_vars($quoteStr) as $key => $value) {
                $quoteStr->{$key} = $this->quoteIdentifier($value);
            }
            return $quoteStr;
        }

        return $this->getConnection()->quoteIdentifier($quoteStr);
    }

    /**
     * Alternative multi variable type quoted identifier
     *
     * @param mixed $quoteTable
     * @return mixed
     */
    public function quoteTable($quoteTable)
    {
        return $this->quoteIdentifier($quoteTable);
    }

    /**
     * Prefix CallBack
     *
     * @access private
     * @param  string $table the table
     * @return string
     */
    private function prefixTableCallback($table)
    {
        $prefix = $this->getTablePrefix();
        if (!empty($prefix) && is_string($prefix) && trim($prefix)) {
            $table = (strpos($table, $prefix) === 0)
                ? $table
                : $prefix.$table;
        }

        return $table;
    }

    /**
     * Prefixing table with predefined table prefix on configuration
     *
     * @param mixed $table
     * @param bool  $use_identifier
     * @return array|null|string
     */
    public function prefixTable($table, $use_identifier = false)
    {
        if ($table instanceof \Closure || is_resource($table)) {
            throw new \InvalidArgumentException(
                "Invalid value to be quote, table value could not be instance of `Closure` or as a `Resource`"
            );
        }

        $prefix = $this->getTablePrefix();
        if (is_array($table)) {
            foreach ($table as $key => $value) {
                $table[$key] = $this->prefixTable($value, $use_identifier);
            }
            return $table;
        }
        if (is_object($table)) {
            foreach (get_object_vars($table) as $key => $value) {
                $table->{$key} = $this->prefixTable($value, $use_identifier);
            }
            return $table;
        }
        if (!is_string($table)) {
            return null;
        }
        if (strpos($table, $this->quoteIdentifier) !== false) {
            $use_identifier = true;
        }
        if (!empty($prefix) && is_string($prefix) && trim($prefix)) {
            $tableArray = explode('.', $table);
            $tableArray    = $this->trimSelector($tableArray);
            if (count($tableArray) > 1) {
                $connectionParams = $this->getConnectionParams();
                if (isset($connectionParams['dbname']) && $tableArray[0] == $connectionParams['dbname']) {
                    $tableArray[1] = $this->prefixTableCallback($tableArray);
                }
                if ($use_identifier) {
                    return $this->quoteIdentifier
                    . implode("{$this->quoteIdentifier}.{$this->quoteIdentifier}", $tableArray)
                    . $this->quoteIdentifier;
                } else {
                    return implode(".", $tableArray);
                }
            } else {
                $table = $this->prefixTableCallback($tableArray[0]);
            }
        }

        return $use_identifier
            ? $this->quoteIdentifier.$table.$this->quoteIdentifier
            : $table;
    }

    /**
     * Compile Bindings
     *     Take From CI 3 Database Query Builder, default string Binding use Question mark ( ? )
     *
     * @param   string $sql   sql statement
     * @param   array  $binds array of bind data
     * @return  mixed
     */
    public function compileBindsQuestionMark($sql, $binds = null)
    {
        if (empty($binds) || strpos($sql, '?') === false) {
            return $sql;
        } elseif (! is_array($binds)) {
            $binds = [$binds];
            $bind_count = 1;
        } else {
            // Make sure we're using numeric keys
            $binds = array_values($binds);
            $bind_count = count($binds);
        }
        // Make sure not to replace a chunk inside a string that happens to match the bind marker
        if ($countMatches = preg_match_all("/'[^']*'/i", $sql, $matches)) {
            $countMatches = preg_match_all(
                '/\?/i', # regex
                str_replace(
                    $matches[0],
                    str_replace('?', str_repeat(' ', 1), $matches[0]),
                    $sql,
                    $countMatches
                ),
                $matches, # matches
                PREG_OFFSET_CAPTURE
            );
            // Bind values' count must match the count of markers in the query
            if ($bind_count !== $countMatches) {
                return false;
            }
        } elseif (($countMatches = preg_match_all('/\?/i', $sql, $matches, PREG_OFFSET_CAPTURE)) !== $bind_count) {
            return $sql;
        }

        do {
            $countMatches--;
            $escapedValue = is_int($binds[$countMatches])
                ? $binds[$countMatches]
                : $this->getConnection()->quote($binds[$countMatches]);
            if (is_array($escapedValue)) {
                $escapedValue = '('.implode(',', $escapedValue).')';
            }
            $sql = substr_replace($sql, $escapedValue, $matches[0][$countMatches][1], 1);
        } while ($countMatches !== 0);

        return $sql;
    }

    /**
     * Query using binding optionals statements
     *
     * @uses   compileBindsQuestionMark
     * @param  string $sql
     * @param  mixed  $statement array|string|null
     * @return \Doctrine\DBAL\Driver\Statement
     * @throws \Doctrine\DBAL\DBALException
     */
    public function queryBind($sql, $statement = null)
    {
        $sql = $this->compileBindsQuestionMark($sql, $statement);
        if ($sql == false) {
            throw new DBALException(
                sprintf(
                    'Invalid statement binding count with sql query : %s',
                    $sql->sql
                ),
                E_USER_WARNING
            );
        }

        return $this->getConnection()->query($sql);
    }

    /**
     * Get Doctrine Column of table
     *
     * @param string $tableName
     * @return bool|\Doctrine\DBAL\Schema\Column[]
     */
    public function getTableColumns($tableName)
    {
        if (!is_string($tableName)) {
            throw new \InvalidArgumentException(
                'Invalid table name type. Table name must be as string',
                E_USER_ERROR
            );
        }

        $tableName = trim(strtolower($tableName));
        if ($tableName == '') {
            throw new \InvalidArgumentException(
                'Invalid parameter table name. Table name could not be empty.',
                E_USER_ERROR
            );
        }

        $tableName = $this->prefixTable($tableName);
        if (!in_array($tableName, $this->getConnection()->getSchemaManager()->listTableNames())) {
            return false;
        }

        return $this
            ->getConnection()
            ->getSchemaManager()
            ->listTableColumns($tableName);
    }

    /**
     * Check if table is Exists
     *
     * @param string $tableName
     * @return bool
     */
    public function isTableExist($tableName)
    {
        if (! is_string($tableName) && !is_array($tableName)) {
            throw new \InvalidArgumentException(
                'Invalid table name type. Table name must be as string or array',
                E_USER_ERROR
            );
        }

        $tableName = $this->prefixTable($tableName);
        !is_array($tableName) && $tableName = [$tableName];
        return $this
            ->getConnection()
            ->getSchemaManager()
            ->tablesExist($tableName);
    }
}
