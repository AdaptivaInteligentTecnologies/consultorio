<?php
/**
 * A group of RadioButton's
 *
 * @version    1.0
 * @package    widget_web
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TRadioGroup extends TField implements IWidget
{
    private $layout = 'vertical';
    private $changeAction;
    private $items;
    protected $formName;
    
    /**
     * Define the direction of the options
     * @param $direction String (vertical, horizontal)
     */
    public function setLayout($dir)
    {
        $this->layout = $dir;
    }
    
    /**
     * Add Items to the RadioButton
     * @param $items An array containing the options
     */
    public function addItems($items)
    {
        if (is_array($items))
        {
            $this->items = $items;
        }
    }
    
    /**
     * Define the action to be executed when the user changes the combo
     * @param $action TAction object
     */
    public function setChangeAction(TAction $action)
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
    }
    
    /**
     * Enable the field
     * @param $form_name Form name
     * @param $field Field name
     */
    public static function enableField($form_name, $field)
    {
        $script = new TElement('script');
        $script->{'language'} = 'JavaScript';
        $script->setUseSingleQuotes(TRUE);
        $script->setUseLineBreaks(FALSE);
        $script->add( " setTimeout(function(){ \$('[name={$field}]').removeAttr('disabled') },1);");
        $script->show();
    }
    
    /**
     * Disable the field
     * @param $form_name Form name
     * @param $field Field name
     */
    public static function disableField($form_name, $field)
    {
        $script = new TElement('script');
        $script->{'language'} = 'JavaScript';
        $script->setUseSingleQuotes(TRUE);
        $script->setUseLineBreaks(FALSE);
        $script->add( " setTimeout(function(){ \$('[name={$field}]').attr('disabled', '') },1);");
        $script->show();
    }
    
    /**
     * clear the field
     * @param $form_name Form name
     * @param $field Field name
     */
    public static function clearField($form_name, $field)
    {
        $script = new TElement('script');
        $script->{'language'} = 'JavaScript';
        $script->setUseSingleQuotes(TRUE);
        $script->setUseLineBreaks(FALSE);
        $script->add( " setTimeout(function(){ \$('[name={$field}]').attr('checked', false) },1);");
        $script->show();
    }
    
    /**
     * Show the widget at the screen
     */
    public function show()
    {
        if ($this->items)
        {
            // iterate the RadioButton options
            foreach ($this->items as $index => $label)
            {
                $button = new TRadioButton($this->name);
                $button->setTip($this->tag->title);
                $button->setValue($index);
                
                // check if contains any value
                if ($this->value == $index)
                {
                    // mark as checked
                    $button->setProperty('checked', '1');
                }
                
                // check whether the widget is non-editable
                if (parent::getEditable())
                {
                    if (isset($this->changeAction))
                    {
                        if (!TForm::getFormByName($this->formName) instanceof TForm)
                        {
                            throw new Exception(TAdiantiCoreTranslator::translate('You must pass the ^1 (^2) as a parameter to ^3', __CLASS__, $this->name, 'TForm::setFields()') );
                        }
                        $string_action = $this->changeAction->serialize(FALSE);
                        $button->setProperty('onChange', "serialform=(\$('#{$this->formName}').serialize());
                                                          ajaxLookup('$string_action&'+serialform, this)");
                    }
                }
                else
                {
                    $button->setEditable(FALSE);
                }
                // create the label for the button
                $obj = new TLabel($label);
                $obj->add($button);
                $obj->show();
                
                if ($this->layout == 'vertical')
                {
                    // shows a line break
                    $br = new TElement('br');
                    $br->show();
                }
                echo "\n";
            }
        }
    }
}
?>