<?php

namespace JordJD\LaravelOmegaSearch;

use Illuminate\Support\Str;

class OmegaSearchJoin
{
    protected $joinedTable;
    protected $localTable;
    protected $conditions = [];
    protected $joinType;

    /**
     * OmegaSearchJoin constructor.
     * @param string $joinedTable
     * @param string $localTable
     * @param string $joinType
     */
    public function __construct($localTable, $joinedTable, $joinType = 'JOIN')
    {
        $this->joinedTable = $joinedTable;
        $this->localTable = $localTable;
        $this->joinType = $joinType;
    }

    /**
     * @param string $localTable
     * @param string $joinedTable
     * @param string $foreignKey
     * @param string|null $localKey
     * @return OmegaSearchJoin
     */
    public static function joinTableByForeignKey($localTable, $joinedTable, $foreignKey = 'id', $localKey = false)
    {
        if (!$localKey) {
            $localKey = Str::singular($joinedTable).'_id';
        }

        $join = new self($localTable, $joinedTable, 'LEFT JOIN');
        $join->addCondition($localKey, '=', $foreignKey);

        return $join;
    }

    /**
     * @param string $localColumn
     * @param string $operator
     * @param string $foreignColumn
     * @return $this
     */
    public function addCondition($localColumn, $operator, $foreignColumn)
    {
        $this->conditions[] = new OmegaSearchJoinCondition(
            $this->localTable,
            $this->joinedTable,
            $localColumn,
            $foreignColumn,
            $operator
        );

        return $this;
    }

    /**
     * @return string
     */
    public function getSqlStatement()
    {
        $conditionStatements = array_map(function (OmegaSearchJoinCondition $condition) {
            return $condition->getSqlStatement();
        }, $this->conditions);

        return $this->joinType . ' ' . $this->joinedTable . ' ON ' . implode(' AND ', $conditionStatements);
    }
}
