<?php
/**
 * Entry Widget (also known as Edit, Input)
 *
 * @version    1.0
 * @package    widget_web
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TEntry extends TField implements IWidget
{
    public  $id;
    private $mask;
    private $completion;
    private $exitAction;
    private $numericMask;
    private $decimals;
    private $decimalsSeparator;
    private $thousandSeparator;
    protected $formName;
    protected $name;
    
    /**
     * Class Constructor
     * @param  $name name of the field
     */
    public function __construct($name)
    {
        parent::__construct($name);
        $this->numericMask = FALSE;
    }
    
    /**
     * Define the field's mask
     * @param $mask A mask for input data
     */
    public function setMask($mask)
    {
        $this->mask = $mask;
    }
    
    /**
     * Define the field's numeric mask (available just in web)
     * @param $decimals Sets the number of decimal points.
     * @param $decimalsSeparator Sets the separator for the decimal point.
     * @param $thousandSeparator Sets the thousands separator.
     */
    public function setNumericMask($decimals, $decimalsSeparator, $thousandSeparator)
    {
        $this->numericMask = TRUE;
        $this->decimals = $decimals;
        $this->decimalsSeparator = $decimalsSeparator;
        $this->thousandSeparator = $thousandSeparator;
    }
    
    /**
     * Define max length
     * @param  $length Max length
     */
    public function setMaxLength($length)
    {
        if ($length > 0)
        {
            $this->tag-> maxlength = $length;
        }
    }
    
    /**
     * Define options for completion
     * @param $options array of options for completion
     */
    function setCompletion($options)
    {
        $this->completion = $options;
    }
    
    /**
     * Define the action to be executed when the user leaves the form field
     * @param $action TAction object
     */
    function setExitAction(TAction $action)
    {
        if ($action->isStatic())
        {
            $this->exitAction = $action;
        }
        else
        {
            $string_action = $action->toString();
            throw new Exception(TAdiantiCoreTranslator::translate('Action (^1) must be static to be used in ^2', $string_action, __METHOD__));
        }
    }
    
    /**
     * Shows the widget at the screen
     */
    public function show()
    {
        if ($this->mask)
        {
            TPage::include_js('lib/adianti/include/tentry/tentry.js');
        }
        else if ($this->numericMask)
        {
            TPage::include_js('lib/adianti/include/tentry/jquery-imask-min.js');
        }
        
        // define the tag properties
        $this->tag-> name  = $this->name;    // TAG name
        $this->tag-> value = $this->value;   // TAG value
        $this->tag-> type  = 'text';         // input type
        $this->setProperty('style', "width:{$this->size}px", FALSE); //aggregate style info
        
        if ($this->id)
        {
            $this->tag-> id    = $this->id;
        }
        
        // verify if the widget is non-editable
        if (parent::getEditable())
        {
            if (isset($this->exitAction))
            {
                if (!TForm::getFormByName($this->formName) instanceof TForm)
                {
                    throw new Exception(TAdiantiCoreTranslator::translate('You must pass the ^1 (^2) as a parameter to ^3', __CLASS__, $this->name, 'TForm::setFields()') );
                }
                $string_action = $this->exitAction->serialize(FALSE);

                $this->setProperty('exitaction', "serialform=(\$('#{$this->formName}').serialize());
                                                 ajaxLookup('$string_action&'+serialform, document.{$this->formName}.{$this->name})", FALSE);
                $this->setProperty('onBlur', $this->getProperty('exitaction'), FALSE);
            }
            
            if ($this->mask)
            {
                $this->tag-> onKeyPress="return entryMask(this,event,'{$this->mask}')";
            }
        }
        else
        {
            $this->tag-> readonly = "1";
            $this->tag->{'class'} = 'tfield_disabled'; // CSS
            $this->tag-> onmouseover = "style.cursor='default'";
        }
        
        // shows the tag
        $this->tag->show();
        
        if (isset($this->completion))
        {
            $options = json_encode($this->completion);
            $script = new TElement('script');
            $script->add("\$('input[name=\"{$this->name}\"]').autocomplete({source: {$options} });");
            $script->show();
        }
        if ($this->numericMask)
        {
            $script = new TElement('script');
            $script->add("\$('input[name=\"{$this->name}\"]').iMask({
		                        type : 'number',
		                        decDigits   : {$this->decimals},
		                        decSymbol   : '{$this->decimalsSeparator}',
		                        groupSymbol : '{$this->thousandSeparator}'
	                    });");
            $script->show();
        }
    }
}
?>