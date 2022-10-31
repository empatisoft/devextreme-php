<?php
/**
 * Developer: ONUR KAYA
 * Contact: empatisoft@gmail.com
 */

namespace Empatisoft\Devextreme;

use RecursiveArrayIterator;

class Functions {

    /**
     * @var string
     * SQL Query Where Conditions with bind parameters
     */
    private string $conditions = '';

    /**
     * @var string
     * SQL Query Where Conditions with raw values
     */
    private string $conditionsRaw = '';

    /**
     * @var array
     * PDO bind params (key => value)
     */
    private array $bind = [];

    /**
     * @var int
     */
    private int $bindParamCount = 0;

    /**
     * @return array
     * Return filter conditions
     */
    public function getConditions() {
        //$filters = filter_input(INPUT_GET, 'filter');
        $filters = '[[["student_number","contains","224"],"and",[["created_at",">=","2022/10/01 08:28:00"],"and",["created_at","<=","2022/10/31 08:28:00"]]],"and",[[["amount",">=",500],"and",["amount","<",1000]],"or",[["amount",">=",1000],"and",["amount","<",5000]],"or",[["amount",">=",5000],"and",["amount","<",10000]]]]';
        $filters = $filters != null ? json_decode($filters, true) : [];
        $iterator = new RecursiveArrayIterator($filters);
        while ($iterator->valid()) {

            if ($iterator->hasChildren()) {
                $this->conditions .= '(';
                $this->conditionsRaw .= '(';

                foreach ($iterator->getChildren() as $children) {

                    if(is_string($children) && ($children == 'or' || $children == 'and')) {
                        $this->conditions .= " $children ";
                        $this->conditionsRaw .= " $children ";
                    } else {
                        if(is_string($children) && ($children == 'or' || $children == 'and')) {
                            $this->conditions .= " $children ";
                            $this->conditionsRaw .= " $children ";
                        } else {
                            if (count($children) == count($children, COUNT_RECURSIVE)) {
                                if(is_string($children) && ($children == 'or' || $children == 'and')) {
                                    $this->conditions .= " $children ";
                                    $this->conditionsRaw .= " $children ";
                                } else
                                    $this->parseFilter($children);

                            } else {
                                $this->conditions .= '(';
                                $this->conditionsRaw .= '(';
                                if(!empty($children)) {
                                    foreach ($children as $child) {
                                        if(is_string($child) && ($child == 'or' || $child == 'and')) {
                                            $this->conditions .= " $child ";
                                            $this->conditionsRaw .= " $child ";
                                        } else
                                            $this->parseFilter($child);
                                    }
                                }
                                $this->conditions .= ')';
                                $this->conditionsRaw .= ')';
                            }
                        }
                    }
                }
                $this->conditions .= ')';
                $this->conditionsRaw .= ')';
            } else {
                $this->conditions .= ' '.$iterator->current().' ';
                $this->conditionsRaw .= ' '.$iterator->current().' ';
            }
            $this->bindParamCount++;
            $iterator->next();
        }
        return [
            'conditions' => $this->conditions,
            'conditionsRaw' => $this->conditionsRaw,
            'bind' => $this->bind
        ];
    }

    /**
     * @param array $children
     * @return void
     */
    private function parseFilter(array $children): void {
        $key = $children[0] ?? null;
        $operator = $children[1] ?? null;
        $value = $children[2] ?? null;
        $bindKey = $key.'_'.$this->bindParamCount;

        switch ($operator) {
            case "=":
            case "<>":
            case ">":
            case ">=":
            case "<":
            case "<=": {
                $this->conditions .= "$key $operator :$bindKey:";
                $this->conditionsRaw .= "$key $operator '$value'";
                $this->bind[$bindKey] = $value;
                break;
            }
            case "startswith": {
                $this->conditions .= "$key LIKE :$bindKey:";
                $this->conditionsRaw .= "$key LIKE '$value%'";
                $this->bind[$bindKey] = "$value%";
                break;
            }
            case "endswith": {
                $this->conditions .= "$key LIKE :$bindKey:";
                $this->conditionsRaw .= "$key LIKE '%$value'";
                $this->bind[$bindKey] = "%$value";
                break;
            }
            case "contains": {
                $this->conditions .= "$key LIKE :$bindKey:";
                $this->conditionsRaw .= "$key LIKE '%$value%'";
                $this->bind[$bindKey] = "%$value%";
                break;
            }
            case "notcontains": {
                $this->conditions .= "$key NOT LIKE :$bindKey:";
                $this->conditionsRaw .= "$key NOT LIKE '%$value%'";
                $this->bind[$bindKey] = "%$value%";
                break;
            }
            default: {

            }
        }
        $this->bindParamCount++;
    }

}
