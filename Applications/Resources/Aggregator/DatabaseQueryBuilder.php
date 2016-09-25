<?php
/**
 * Database Query Builder Aggregator
 *      as alternative conjunction of Doctrine QueryBuilder
 *      and as fix of table prefix
 *
 * @author  nawa <nawa@yahoo.com>
 * @version 1.0.0
 * @license MIT
 */
namespace MalangPhp\Site\Conf\Aggregator;

use Doctrine\DBAL\Query\QueryBuilder;
use MalangPhp\Site\Conf\Component\Database;

/**
 * Class DatabaseQueryBuilder
 * @package MalangPhp\Site\Conf\Aggregator
 */
class DatabaseQueryBuilder
{
    /**
     * @uses QueryBuilder
     * The query types.
     */
    const SELECT = QueryBuilder::SELECT;
    const DELETE = QueryBuilder::DELETE;
    const UPDATE = QueryBuilder::UPDATE;
    const INSERT = QueryBuilder::INSERT;

    /**
     * @uses QueryBuilder
     * The builder states.
     */
    const STATE_DIRTY = QueryBuilder::STATE_DIRTY;
    const STATE_CLEAN = QueryBuilder::STATE_CLEAN;

    /**
     * @var Database
     */
    protected $databaseObject;

    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

    /**
     * DatabaseQueryBuilder constructor.
     * @param Database $database as database agregator
     */
    public function __construct(Database $database)
    {
        $this->databaseObject = $database;
        $this->queryBuilder = new QueryBuilder($this->databaseObject->getConnection());
    }

    /**
     * Get Database Object Instance
     *
     * @return Database
     */
    public function getDatabaseObject()
    {
        return $this->databaseObject;
    }

    /**
     * Specifies an item that is to be returned in the query result.
     * Replaces any previously specified selections, if any.
     *
     * @param mixed $select The selection expressions.
     *
     * @return DatabaseQueryBuilder
     */
    public function select($select = null)
    {
        $selects = is_array($select) ? $select : func_get_args();
        if (!empty($selects)) {
            $selects = $this->databaseObject->prefixTable($select);
        }

        $this->queryBuilder->select($selects);
        return $this;
    }

    /**
     * Adds an item that is to be returned in the query result.
     *
     * @param mixed $select The selection expression.
     *
     * @return DatabaseQueryBuilder
     */
    public function addSelect($select = null)
    {
        $selects = is_array($select) ? $select : func_get_args();
        if (!empty($selects)) {
            $selects = $this->databaseObject->prefixTable($select);
        }

        $this->queryBuilder->addSelect($selects);

        return $this;
    }

    /**
     * Turns the query being built into a bulk delete query that ranges over
     * a certain table.
     *
     *
     * @param string $delete The table whose rows are subject to the deletion.
     * @param string $alias  The table alias used in the constructed query.
     *
     * @return DatabaseQueryBuilder
     */
    public function delete($delete = null, $alias = null)
    {
        if ($delete) {
            $delete = $this->databaseObject->prefixTable($delete, true);
        }

        $this->queryBuilder->update($delete, $alias);

        return $this;
    }

    /**
     *  Turns the query being built into an insert query that inserts into
     *      a certain table
     *
     * @param string $insert The table into which the rows should be inserted.
     * @return DatabaseQueryBuilder
     */
    public function insert($insert = null)
    {
        if ($insert) {
            $insert = $this->databaseObject->prefixTable($insert, true);
        }
        $this->queryBuilder->insert($insert);

        return $this;
    }

    /**
     * Turns the query being built into a bulk update query that ranges over
     * a certain table
     *
     * @param string $update The table whose rows are subject to the update.
     * @param string $alias  The table alias used in the constructed query.
     *
     * @return DatabaseQueryBuilder
     */
    public function update($update = null, $alias = null)
    {
        if ($update) {
            $update = $this->databaseObject->prefixTable($update, true);
        }

        $this->queryBuilder->update($update, $alias);

        return $this;
    }

    /**
     * Creates and adds a query root corresponding to the table identified by the
     * given alias, forming a cartesian product with any existing query roots.
     *
     * @param string      $from  The table.
     * @param string|null $alias The alias of the table.
     *
     * @return DatabaseQueryBuilder
     */
    public function from($from, $alias = null)
    {
        $from = $this->databaseObject->prefixTable($from, true);
        $this->queryBuilder->from($from, $alias);

        return $this;
    }

    /**
     * Magic Method Call instant into @uses QueryBuilder
     *
     * @param string $name
     * @param array  $arguments
     * @return mixed
     * @throws \BadMethodCallException
     */
    public function __call($name, $arguments)
    {
        if (method_exists($this->queryBuilder, $name)) {
            return call_user_func_array(
                [$this->queryBuilder, $name],
                $arguments
            );
        }

        throw new \BadMethodCallException(
            sprintf('Call to undefined method %s', $name),
            E_USER_ERROR
        );
    }
}
