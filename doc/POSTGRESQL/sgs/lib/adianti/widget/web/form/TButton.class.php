<?php
/**
 * Button Widget
 *
 * @version    1.0
 * @package    widget_web
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TButton extends TField implements IWidget
{
    private $action;
    private $label;
    private $image;
    private $properties;
    private $functions;
    protected $formName;
    
    /**
     * Define the action of the button
     * @param  $action TAction object
     * @param  $label  Button's label
     */
    public function setAction(TAction $action, $label)
    {
        $this->action = $action;
        $this->label  = $label;
    }
    
    /**
     * Define the icon of the button
     * @param  $image  image path
     */
    public function setImage($image)
    {
        $this->image = $image;
    }
    
    /**
     * Define the label of the button
     * @param  $label button label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * Add a JavaScript function to be executed by the button
     * @param $function A piece of JavaScript code
     * @ignore-autocomplete on
     */
    public function addFunction($function)
    {
        if ($function)
        {
            $this->functions = $function.';';
        }
    }
    
    /**
     * Define a field property
     * @param $name  Property Name
     * @param $value Property Value
     */
    public function setProperty($name, $value, $replace = TRUE)
    {
        $this->properties[$name] = $value;
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
        $script->add( " try { document.{$form_name}.{$field}.removeAttribute('disabled'); } catch (e) { } " );
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
        $script->add( " try { document.{$form_name}.{$field}.setAttribute('disabled', true); } catch (e) { } " );
        $script->show();
    }
    
    /**
     * Show the widget at the screen
     */
    public function show()
    {
        if ($this->action)
        {
            if (empty($this->formName))
            {
                throw new Exception(TAdiantiCoreTranslator::translate('You must pass the ^1 (^2) as a parameter to ^3', __CLASS__, $this->label, 'TForm::setFields()') );
            }
            
            // get the action as URL
            $url = $this->action->serialize(FALSE);
            $wait_message = TAdiantiCoreTranslator::translate('Loading');
            // define the button's action (ajax post)
            $action = "__adianti_block_ui('$wait_message');";
            $action.= "{$this->functions}";
            $action.= "__adianti_post_data('{$this->formName}', '{$url}');";
            $action.= "return false;";
                        
            $button = new TElement('button');
            $button->{'class'} = 'btn btn-small';
            $button-> onclick   = $action;
            $button-> id   = 'tbutton_'.$this->name;
            $button-> name = $this->name;
            $action = '';
        }
        else
        {
            $action = $this->functions;
            // creates the button using a div
            $button = new TElement('div');
            $button-> id   = 'tbutton_'.$this->name;
            $button-> name = $this->name;
            $button->{'class'} = 'btn btn-small';
            $button-> onclick  = $action;
        }
        
        if ($this->properties)
        {
            foreach ($this->properties as $property => $value)
            {
                $button->$property = $value;
            }
        }

        $span = new TElement('span');
        if ($this->image)
        {
            if (file_exists('app/images/'.$this->image))
            {
                $image = new TImage('app/images/'.$this->image);
            }
            else
            {
                $image = new TImage('lib/adianti/images/'.$this->image);
            }
            $image->{'style'} = 'padding-right:4px';
            $span->add($image);
        }
        $span->add($this->label);
        $button->add($span);
        $button->show();
    }
}
?>