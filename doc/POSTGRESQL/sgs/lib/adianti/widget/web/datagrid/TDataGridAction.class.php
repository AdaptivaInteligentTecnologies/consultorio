<?php
/**
 * Represents an action inside a datagrid
 *
 * @version    1.0
 * @package    widget_web
 * @subpackage datagrid
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TDataGridAction extends TAction
{
    private $image;
    private $label;
    private $field;

    /**
     * Define an icon for the action
     * @param $image  The Image path
     */
    public function setImage($image)
    {
        $this->image = $image;
    }
    
    /**
     * Returns the icon of the action
     */
    public function getImage()
    {
        return $this->image;
    }
    
    /**
     * define the label for the action
     * @param $label A string containing a text label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }
    
    /**
     * Returns the text label for the action
     */
    public function getLabel()
    {
        return $this->label;
    }
    
    /**
     * Define wich Active Record's property
     * will be passed along with the action
     * @param $field Active Record's property
     */
    public function setField($field)
    {
        $this->field = $field;
    }
    
    /**
     * Returns the Active Record's property that 
     * will be passed along with the action
     */
    public function getField()
    {
        return $this->field;
    }
    
    /**
     * Converts the action into an URL
     * @param  $format_action = format action with document or javascript (ajax=no)
     */
    public function serialize($format_action = TRUE)
    {
        if (is_array($this->action) AND is_object($this->action[0]))
        {
            if (isset( $_REQUEST['offset'] ))
            {
                $this->setParameter('offset',     $_REQUEST['offset'] );
            }
            if (isset( $_REQUEST['limit'] ))
            {
                $this->setParameter('limit',      $_REQUEST['limit'] );
            }
            if (isset( $_REQUEST['page'] ))
            {
                $this->setParameter('page',       $_REQUEST['page'] );
            }
            if (isset( $_REQUEST['first_page'] ))
            {
                $this->setParameter('first_page', $_REQUEST['first_page'] );
            }
            if (isset( $_REQUEST['order'] ))
            {
                $this->setParameter('order',      $_REQUEST['order'] );
            }
        }
        return parent::serialize($format_action);
    }
}
?>