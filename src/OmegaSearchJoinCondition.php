<?php

namespace JordJD\LaravelOmegaSearch;


class OmegaSearchJoinCondition
{
    protected $localTable;
    protected $joinedTable;
    protected $localColumn;
    protected $foreignColumn;
    protected $operator;

    /**
     * OmegaSearchJoinCondition constructor.
     * @param string $localTable
     * @param string $joinedTable
     * @param string localColumn
     * @param string $foreignColumn
     * @param string operator
     */
    public function __construct($localTable, $joinedTable, $localColumn, $foreignColumn, $operator)
    {
        $this->localTable = $localTable;
        $this->joinedTable = $joinedTable;
        $this->localColumn = $localColumn;
        $this->foreignColumn = $foreignColumn;
        $this->operator = $operator;
    }

    /**
     * @return string
     */
    public function getSqlStatement(){
        return $this->localTable . '.' . $this->localColumn . ' '
            . $this->operator . ' '
            . $this->joinedTable . '.' . $this->foreignColumn;
    }
}
