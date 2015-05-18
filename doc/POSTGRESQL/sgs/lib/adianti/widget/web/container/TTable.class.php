<?php
/**
 * Table Container: Allows the developer to organize the widgets according to a table layout, using rows and columns without using borders
 *
 * @version    1.0
 * @package    widget_web
 * @subpackage container
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TTable extends TElement
{
    /**
     * Class Constructor
     */
    public function __construct()
    {
        parent::__construct('table');
    }

    /**
     * Add a new row (TTableRow object) to the table
     * @return TTableRow
     */
    public function addRow()
    {
        // creates a new Table Row
        $row = new TTableRow;
        // add this row to the table element
        parent::add($row);
        return $row;
    }
    
    /**
     * Add a new row (TTableRow object) with many cells
     * @param $cells Each argument is a row cell
     * @return TTableRow
     */
    public function addRowSet()
    {
        // creates a new Table Row
        $row = $this->addRow();
        
        $args = func_get_args();
        if ($args)
        {
            foreach ($args as $arg)
            {
                if (is_array($arg))
                {
                    call_user_func_array(array($row, 'addMultiCell'), $arg);
                }
                else
                {
                    $row->addCell($arg);
                }
            }
        }
        return $row;
    }
}
?>