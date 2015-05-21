<?php
/**
 * Html Editor
 *
 * @version    1.0
 * @package    widget_web
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class THtmlEditor extends TField implements IWidget
{
    private $widgetId;
    private static $counter;
    protected $size;
    private   $height;
    
    /**
     * Class Constructor
     * @param $name Widet's name
     */
    public function __construct($name)
    {
        parent::__construct($name);
        self::$counter ++;
        $this->widgetId = 'THtmlEditor_'.self::$counter;
        
        // creates a tag
        $this->tag = new TElement('div');
    }
    
    /**
     * Define the widget's size
     * @param  $width   Widget's width
     * @param  $height  Widget's height
     */
    public function setSize($width, $height = NULL)
    {
        $this->size   = $width;
        if ($height)
        {
            $this->height = $height;
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
        $script->add( " setTimeout(function(){ \$('[name={$field}]').cleditor()[0].disable(false).refresh(); },1);");
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
        $script->add( " setTimeout(function(){ \$('[name={$field}]').cleditor()[0].disable(true); },1);");
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
        $script->add( " setTimeout(function(){ \$('[name={$field}]').cleditor()[0].clear(); },1);");
        $script->show();
    }
    
    /**
     * Show the widget
     */
    public function show()
    {
        // check if the field is not editable
        if (parent::getEditable())
        {
            $tag = new TElement('textarea');
            $tag->{'id'} = $this->widgetId;
            $tag->{'class'} = 'thtmleditor';       // CSS
            $tag-> name  = $this->name;   // tag name
            $this->setProperty('style', "width:{$this->size}px", FALSE); //aggregate style info
            $this->tag->add($tag);
            if ($this->height)
            {
                $tag-> style .=  "height:{$this->height}px";
            }
            
            // add the content to the textarea
            $tag->add(htmlspecialchars($this->value));
            
            $script = new TElement('script');
            $script-> type = 'text/javascript';
            $script->add('
                $("#'.$tag->{'id'}.'").cleditor({width:"'.$this->size.'px", height:"'.$this->height.'px"})
            ');
    		$script->show();
        }
        else
        {
            $this->tag-> style = "width:{$this->size}px;";
            $this->tag-> style.= "height:{$this->height}px;";
            $this->tag-> style.= "background-color:#FFFFFF;";
            $this->tag-> style.= "border: 1px solid #000000;";
            $this->tag-> style.= "padding: 5px;";
            $this->tag-> style.= "overflow: auto;";
            
            // add the content to the textarea
            $this->tag->add($this->value);
        }
        // show the tag
        $this->tag->show();
    }
}
?>