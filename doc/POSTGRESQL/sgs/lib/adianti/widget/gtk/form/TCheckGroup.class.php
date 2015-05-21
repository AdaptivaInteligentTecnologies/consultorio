<?php
/**
 * A group of CheckButton's
 *
 * @version    1.0
 * @package    widget_gtk
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TCheckGroup extends TField implements IWidget
{
    private $checks;
    private $items;
    private $changeAction;
    protected $widget;
    protected $formName;
    
    /**
     * Class Constructor
     * @param $name Name of the widget
     */
    public function __construct($name)
    {
        parent::__construct($name);
        
        $this->widget = new GtkHBox;
        $this->widget->set_border_width(0);
        
        parent::add($this->widget);
        $this->setLayout('vertical');
    }
    
    /**
     * Define wich check button will be active
     * @param $items An array indicating wich check buttons will be active
     */
    public function setValue($items)
    {
        if ($this->checks)
        {
            foreach ($this->checks as $key => $check)
            {
                if (in_array($key, (array) $items))
                {
                    $check->set_active(TRUE);
                }
            }
        }
    }
    
    /**
     * Returns the current active radio button
     */
    public function getValue()
    {
        $returns = array();
        foreach ($this->checks as $key => $radio)
        {
            if ($radio->get_active())
            {
                $returns[] = $key;
            }
        }
        return $returns;
    }
    
    /**
     * Define the widget's size
     * @param $width Widget's size in pixels
     */
    public function setSize($width, $height = NULL)
    {
        $this->widget->set_size_request($width,-1);
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
     * Define the direction of the CheckButtons
     * @param $direction A string 'vertical' or 'horizontal'
     */
    public function setLayout($layout = 'horizontal')
    {
        parent::remove($this->widget);
        if ($layout == 'horizontal')
            $this->widget = new GtkHBox(FALSE, 0);
        else
            $this->widget = new GtkVBox(FALSE, 0);
        parent::add($this->widget);
        
        // keep items even removing the container
        if (is_array($this->items))
        {
            $this->addItems($this->items);
        }
    }
    
    /**
     * Add items to the check group
     * @param $items An indexed array containing the options
     */
    public function addItems($items)
    {
        if (is_array($items))
        {
            $this->items = $items;
            foreach ($items as $index=>$label)
            {
                $this->checks[$index] = new GtkCheckButton($label);
                $this->widget->pack_start($this->checks[$index], FALSE, FALSE, 0);
            }
        }
    }
    
    /**
     * clearSelection
     */
    public function clearSelection()
    {
        if ($this->checks)
        {
            foreach ($this->checks as $key => $check)
            {
                $check->set_active(FALSE);
            }
        }
    }
    
    /**
     * Define the action to be executed when the user changes the combo
     * @param $action TAction object
     */
    function setChangeAction(TAction $action)
    {
        if ($action->isStatic())
        {
            $this->changeAction = $action;
        }
        else
        {
            $string_action = $action->toString();
            throw new Exception(TAdiantiCoreTranslator::translate('Action (^1) must be static to be used in ^2', $string_action, __METHOD__));
        }
        
        if ($this->checks)
        {
            foreach ($this->checks as $key => $check)
            {
                $check->connect('clicked', array($this, 'onExecuteExitAction'));
            }
        }
    }
    
    /**
     * Execute the exit action
     */
    public function onExecuteExitAction()
    {
        if (!TForm::getFormByName($this->formName) instanceof TForm)
        {
            throw new Exception(TAdiantiCoreTranslator::translate('You must pass the ^1 (^2) as a parameter to ^3', __CLASS__, $this->wname, 'TForm::setFields()') );
        }
        
        if (isset($this->changeAction) AND $this->changeAction instanceof TAction)
        {
            $callback = $this->changeAction->getAction();
            $param = (array) TForm::retrieveData($this->formName);
            call_user_func($callback, $param);
        }
    }
    
    /**
     * Clear the field
     * @param $form_name Form name
     * @param $field Field name
     */
    public static function clearField($form_name, $field)
    {
        $form = TForm::getFormByName($form_name);
        if ($form instanceof TForm)
        {
            $field = $form->getField($field);
            if ($field instanceof IWidget)
            {
                $field->clearSelection();
            }
        }
    }
    
    /**
     * Register a tip
     * @param $text Tooltip Text
     */
    function setTip($text)
    {
        if ($this->checks)
        {
            foreach ($this->checks as $key => $check)
            {
                if (method_exists($check, 'set_tooltip_text'))
                {
                    $check->set_tooltip_text($text);
                }
                else
                {
                    $tooltip = TooltipSingleton::getInstance();
                    $tooltip->set_tip($check, $text);
                }
            }
        }
    }
}
?>
