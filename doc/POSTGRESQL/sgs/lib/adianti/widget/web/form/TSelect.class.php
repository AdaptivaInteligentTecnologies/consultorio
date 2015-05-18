<?php
/**
 * Select Widget
 *
 * @version    1.0
 * @package    widget_web
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TSelect extends TField implements IWidget
{
    private   $changeAction;
    private   $id;
    protected $height;
    protected $items; // array containing the combobox options
    protected $formName;
    
    /**
     * Class Constructor
     * @param  $name widget's name
     */
    public function __construct($name)
    {
        // executes the parent class constructor
        parent::__construct($name);
        $this->id   = 'tselect_'.uniqid();
        
        TPage::include_css('lib/adianti/include/tcombo/tcombo.css');
        
        // creates a <select> tag
        $this->tag = new TElement('select');
        $this->tag->{'class'} = 'tcombo'; // CSS
        $this->tag->{'multiple'} = '1';
    }
    
    
    /**
     * Disable multiple selection
     */
    public function disableMultiple()
    {
        unset($this->tag->{'multiple'});
        $this->tag->{'size'} = 3;
    }
    
    /**
     * Add items to the select
     * @param $items An indexed array containing the combo options
     */
    public function addItems($items)
    {
        if (is_array($items))
        {
            $this->items = $items;
        }
    }
    
    /**
     * Define the Field's width
     * @param $width Field's width in pixels
     * @param $height Field's height in pixels
     */
    public function setSize($width, $height = NULL)
    {
        $this->size = $width;
        $this->height = $height;
    }
    
    /**
     * Return the post data
     */
    public function getPostData()
    {
        if (isset($_POST[$this->name]))
        {
            if ($this->tag->{'multiple'})
            {
                return $_POST[$this->name];
            }
            else
            {
                return $_POST[$this->name][0];
            }
        }
        else
        {
            return array();
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
     * Reload combobox items after it is already shown
     * @param $formname form name (used in gtk version)
     * @param $name field name
     * @param $items array with items
     */
    public static function reload($formname, $name, $items)
    {
        $script = new TElement('script');
        $script->{'language'} = 'JavaScript';
        $script->setUseSingleQuotes(TRUE);
        $script->setUseLineBreaks(FALSE);
        $code = '$(function() {';
        $code .= '$(\'select[name="'.$name.'[]"]\').html("");';
        foreach ($items as $key => $value)
        {
            $code .= '$("<option value=\''.$key.'\'>'.$value.'</option>").appendTo(\'select[name="'.$name.'[]"]\');';
        }
        $code.= '});';
        $script->add($code);
        $script->show();
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
        $script->add( " try { document.getElementsByName('{$field}[]')[0].removeAttribute('disabled'); } catch (e) { } " );
        $script->add( " try { document.{$form_name}.{$field}.className = 'tcombo'; } catch (e) { } " );
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
        $script->add( " try { document.getElementsByName('{$field}[]')[0].setAttribute('disabled', true); } catch (e) { } " );
        $script->add( " try { document.{$form_name}.{$field}.className = 'tcombo_disabled'; } catch (e) { } " );
        $script->show();
    }
    
    /**
     * Clear the field
     * @param $form_name Form name
     * @param $field Field name
     */
    public static function clearField($form_name, $field)
    {
        $script = new TElement('script');
        $script->{'language'} = 'JavaScript';
        $script->setUseSingleQuotes(TRUE);
        $script->setUseLineBreaks(FALSE);
        $script->add( " try { document.getElementsByName('{$field}[]')[0].value = '' } catch (e) { } " );
        $script->show();
    }
    
    /**
     * Shows the widget
     */
    public function show()
    {
        // define the tag properties
        $this->tag-> name  = $this->name.'[]';    // tag name
        $this->tag-> style = "width:{$this->size}px;height:{$this->height}px";  // size in pixels
        
        // creates an empty <option> tag
        $option = new TElement('option');
        $option->add('');
        $option-> value = '';   // tag value
        // add the option tag to the combo
        $this->tag->add($option);
        
        if ($this->items)
        {
            // iterate the combobox items
            foreach ($this->items as $chave => $item)
            {
                if (substr($chave, 0, 3) == '>>>')
                {
                    $optgroup = new TElement('optgroup');
                    $optgroup-> label = $item;
                    // add the option to the combo
                    $this->tag->add($optgroup);
                }
                else
                {
                    // creates an <option> tag
                    $option = new TElement('option');
                    $option-> value = $chave;  // define the index
                    $option->add($item);      // add the item label
                    
                    // verify if this option is selected
                    if (@in_array($chave, (array) $this->value))
                    {
                        // mark as selected
                        $option-> selected = 1;
                    }
                    
                    if (isset($optgroup))
                    {
                        $optgroup->add($option);
                    }
                    else
                    {
                        $this->tag->add($option);
                    }                    
                }
            }
        }
        
        // verify whether the widget is editable
        if (parent::getEditable())
        {
            if (isset($this->changeAction))
            {
                if (!TForm::getFormByName($this->formName) instanceof TForm)
                {
                    throw new Exception(TAdiantiCoreTranslator::translate('You must pass the ^1 (^2) as a parameter to ^3', __CLASS__, $this->name, 'TForm::setFields()') );
                }
                $string_action = $this->changeAction->serialize(FALSE);
                $this->setProperty('onChange', "serialform=(\$('#{$this->formName}').serialize());
                                              ajaxLookup('$string_action&'+serialform, this)");
            }
        }
        else
        {
            // make the widget read-only
            $this->tag-> readonly = "1";
            $this->tag->{'class'} = 'tfield_disabled'; // CSS
        }
        // shows the combobox
        $this->tag->show();
    }
}
?>