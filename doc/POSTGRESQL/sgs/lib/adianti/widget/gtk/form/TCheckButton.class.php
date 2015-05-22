<?php
/**
 * CheckButton widget
 *
 * @version    1.0
 * @package    widget_gtk
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TCheckButton extends TField implements IWidget
{
    protected $widget;
    
    /**
     * Class Constructor
     * @param $name Name of the widget
     */
    public function __construct($name)
    {
        parent::__construct($name);
        
        $this->widget = new GtkCheckButton;
        parent::add($this->widget);
        $this->setSize(200);
    }
    
    /**
     * Define if CheckButton is active
     * @param $value A Value indicating if CheckButton is active
     */
    public function setValue($value)
    {
        $this->widget->set_active($value);
    }

    /**
     * Returns if the CheckButton is active
     * @return A boolean indicating if the CheckButton is active
     */
    public function getValue()
    {
        return $this->widget->get_active();
    }
    
    /**
     * Define the Field's size
     * @param $width Field's width in pixels
     */
    public function setSize($width, $height = NULL)
    {
        $this->widget->set_size_request($width, -1);
    }
    
    /**
     * Not implemented
     */
    public function setProperty($name, $value, $replace = TRUE)
    {}
    
    /**
     * Not implemented
     */
    public function getProperty($name)
    {}
}
?>