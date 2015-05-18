<?php
/**
 * Button Widget
 *
 * @version    1.0
 * @package    widget_gtk
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TButton extends TField implements IWidget
{
    protected $widget;
    
    /**
     * Class Constructor
     * @param $name Name of the widget
     */
    public function __construct($name)
    {
        parent::__construct($name);
        
        $this->widget = new GtkButton;
        parent::add($this->widget);
    }
    
    /**
     * Define the field's value
     * @param $value A string containing the field's value
     */
    public function setValue($value)
    {
        $this->widget->set_label($value);
    }
    
    /**
     * Returns the field's value
     */
    public function getValue()
    {
        return $this->widget->get_label();
    }
    
    /**
     * Define the widget's size
     * @param $size Widget's size in pixels
     */
    public function setSize($width, $height = NULL)
    {
        $this->widget->set_size_request($width, $height);
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
    
    /**
     * Define the action of the button
     * @param  $action  TAction object
     * @param  $label   Button's label
     */
    public function setAction(TAction $action, $label)
    {
        $this->widget->set_label($label);
        $this->widget->connect_simple('clicked', array($this, 'onExecute' ), $action);
    }
    
    /**
     * Define the icon of the button
     * @param  $image  image path
     */
    public function setImage($image)
    {
        if (file_exists('lib/adianti/images/'.$image))
        {
            $imagem = GtkImage::new_from_file('lib/adianti/images/'.$image);
        }
        else
        {
            $imagem = GtkImage::new_from_file('app/images/'.$image);
        }
        $this->widget->set_image($imagem);
    }
    
    /**
     * Define the label of the button
     * @param  $label button label
     */
    public function setLabel($label)
    {
        $this->widget->set_label($label);
    }
    
    /**
     * Execute the action
     * @param  $action callback to be executed
     * @ignore-autocomplete on
     */
    public function onExecute($action)
    {
        $callb = $action->getAction();
        
        if (is_object($callb[0]))
        {
            $object = $callb[0];
            call_user_func($callb, $action->getParameters());
            
            //aquip, este IF estava acima do call_user_func
            if (method_exists($object, 'show'))
            {
                if ($object->get_child())
                {
                    $object->show();
                }
            }
        }
        else
        {
            $class  = $callb[0];
            $method = $callb[1];
            TApplication::executeMethod($class, $method, $action->getParameters());
        }
    }
}
?>