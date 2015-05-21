<?php
/**
 * Abstract Record Lookup Widget: Creates a lookup field used to search values from associated entities
 *
 * @version    1.0
 * @package    widget_generic
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TDBSeekButton extends TSeekButton
{
    /**
     * Class Constructor
     * @param  $name name of the form field
     * @param  $database name of the database connection
     * @param  $form name of the parent form
     * @param  $model name of the Active Record to be searched
     * @param  $display_field name of the field to be searched and shown
     * @param  $receive_key name of the form field to receive the primary key
     * @param  $receive_display_field name of the form field to receive the "display field"
     
     */
    public function __construct($name, $database, $form, $model, $display_field, $receive_key, $receive_display_field)
    {
        parent::__construct($name);
        
        $obj = new TStandardSeek;
        
        // define the action parameters
        $action = new TAction(array($obj, 'onSetup'));
        $action->setParameter('database',      $database);
        $action->setParameter('parent',        $form);
        $action->setParameter('model',         $model);
        $action->setParameter('display_field', $display_field);
        $action->setParameter('receive_key',   $receive_key);
        $action->setParameter('receive_field', $receive_display_field);
        parent::setAction($action);
    }
}
?>