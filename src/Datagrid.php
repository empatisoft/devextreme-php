<?php
/**
 * Developer: ONUR KAYA
 * Contact: empatisoft@gmail.com
 */

namespace Empatisoft\Devextreme;

class Datagrid {

    /**
     * @var Functions
     */
    private Functions $functions;

    /**
     * @var string
     * SQL Query Where Conditions with bind parameters
     */
    private string $conditions = '';

    /**
     * @var string
     * SQL Query Where Conditions with values
     */
    private string $conditionsRaw = '';

    /**
     * @var array
     * PDO bind params (key => value)
     */
    private array $bind = [];

    public function __construct()
    {
        $this->functions = new Functions();

        /**
         * Check filter conditions
         */
        $filters = $this->functions->getConditions();
        $this->conditions = $filters['conditions'] ?? '';
        $this->conditionsRaw = $filters['conditionsRaw'] ?? '';
        $this->bind = $filters['bind'] ?? [];
    }


}
