<?php
/**
 * Base class to construct all the widgets
 *
 * @version    1.0
 * @package    widget_web
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
abstract class TField
{
    protected $name;
    protected $size;
    protected $value;
    protected $editable;
    protected $tag;
    protected $formName;
    private   $validations;
    
    /**
     * Class Constructor
     * @param  $name name of the field
     */
    public function __construct($name)
    {
        // define some default properties
        self::setEditable(TRUE);
        self::setName($name);
        self::setSize(200);
        
        // initialize validations array
        $this->validations = array();
        
        TPage::include_css('lib/adianti/include/tfield/tfield.css');
        
        // creates a <input> tag
        $this->tag = new TElement('input');
        $this->tag->{'class'} = 'tfield';   // classe CSS
    }
    
    /**
     * Intercepts whenever someones assign a new property's value
     * @param $name     Property Name
     * @param $value    Property Value
     */
    public function __set($name, $value)
    {
        // objects and arrays are not set as properties
        if (is_scalar($value))
        {              
            // store the property's value
            $this->setProperty($name, $value);
        }
    }
    
    /**
     * Returns a property value
     * @param $name     Property Name
     */
    public function __get($name)
    {
        return $this->getProperty($name);
    }
    
    /**
     * Clone the object
     */
    function __clone()
    {
        $this->tag = clone $this->tag;
    }
    
    /**
     * Define the field's name
     * @param $name   A string containing the field's name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the field's name
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Define the field's value
     * @param $value A string containing the field's value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }
    
    /**
     * Returns the field's value
     */
    public function getValue()
    {
        return $this->value;
    }
    
    /**
     * Define the name of the form to wich the button is attached
     * @param $name    A string containing the name of the form
     * @ignore-autocomplete on
     */
    public function setFormName($name)
    {
        $this->formName = $name;
    }
    
    /**
     * Define the field's tooltip
     * @param $name   A string containing the field's tooltip
     */
    public function setTip($tip)
    {
        $this->tag->{'title'} = $tip;
    }
    
    /**
     * Return the post data
     */
    public function getPostData()
    {
        if (isset($_POST[$this->name]))
        {
            return $_POST[$this->name];
        }
        else
        {
            return '';
        }
    }
    
    /**
     * Define if the field is editable
     * @param $editable A boolean
     */
    public function setEditable($editable)
    {
        $this->editable= $editable;
    }

    /**
     * Returns if the field is editable
     * @return A boolean
     */
    public function getEditable()
    {
        return $this->editable;
    }
    
    /**
     * Define a field property
     * @param $name  Property Name
     * @param $value Property Value
     */
    public function setProperty($name, $value, $replace = TRUE)
    {
        if ($replace)
        {
            // delegates the property assign to the composed object
            $this->tag->$name = $value;
        }
        else
        {
            if ($this->tag->$name)
            {
            
                // delegates the property assign to the composed object
                $this->tag->$name = $this->tag->$name . ';' . $value;
            }
            else
            {
                // delegates the property assign to the composed object
                $this->tag->$name = $value;
            }
        }
    }
    
    /**
     * Return a field property
     * @param $name  Property Name
     * @param $value Property Value
     */
    public function getProperty($name)
    {
        return $this->tag->$name;
    }
    
    /**
     * Define the Field's width
     * @param $width Field's width in pixels
     */
    public function setSize($width, $height = NULL)
    {
        $this->size = $width;
    }
    
    /**
     * Add a field validator
     * @param $label Field name
     * @param $validator TFieldValidator object
     * @param $parameters Aditional parameters
     */
    public function addValidation($label, TFieldValidator $validator, $parameters = NULL)
    {
        $this->validations[] = array($label, $validator, $parameters);
    }
    
    /**
     * Validate a field
     */
    public function validate()
    {
        if ($this->validations)
        {
            foreach ($this->validations as $validation)
            {
                $label      = $validation[0];
                $validator  = $validation[1];
                $parameters = $validation[2];
                
                $validator->validate($label, $this->getValue(), $parameters);
            }
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
        $script->add( " try { document.{$form_name}.{$field}.removeAttribute('readonly'); } catch (e) { } " );
        $script->add( " try { document.{$form_name}.{$field}.className = 'tfield'; } catch (e) { } " );
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
        $script->add( " try { document.{$form_name}.{$field}.setAttribute('readonly', true); } catch (e) { } " );
        $script->add( " try { document.{$form_name}.{$field}.className = 'tfield_disabled'; } catch (e) { } " );
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
        $script->add( " try { document.{$form_name}.{$field}.value='' } catch (e) { } " );
        $script->show();
    }
}
?>