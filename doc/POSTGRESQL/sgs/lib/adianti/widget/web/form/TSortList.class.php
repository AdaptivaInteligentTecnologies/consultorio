<?php
/**
 * A Sortable list
 *
 * @version    1.0
 * @package    widget_web
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TSortList extends TField implements IWidget
{
    private $id;
    private $initialItems;
    private $items;
    private $connectedTo;
    private $itemIcon;
    private static $counter;
    
    /**
     * Class Constructor
     * @param  $name widget's name
     */
    public function __construct($name)
    {
        // executes the parent class constructor
        parent::__construct($name);
        self::$counter ++;
        $this->id   = 'tsortlist_'.uniqid();
        
        $this->initialItems = array();
        $this->items = array();
        TPage::include_css('lib/adianti/include/tsortlist/tsortlist.css');
        
        // creates a <ul> tag
        $this->tag = new TElement('ul');
        $this->tag->{'class'} = 'tsortlist';
        $this->tag->{'id'} = $this->id;
        $this->tag->{'itemname'} = $name;
    }
    
    /**
     * Define the item icon
     * @param $image Item icon
     */
    public function setItemIcon(TImage $icon)
    {
        $this->itemIcon = $icon;
    }
    
    /**
     * Define the list size
     */
    public function setSize($width, $height = NULL)
    {
        $this->tag->{'style'} = "width:{$width}px;height:{$height}px";
    }
    
    /**
     * Define the field's value
     * @param $value An array the field's values
     */
    public function setValue($value)
    {
        $items = $this->initialItems;
        if (is_array($value))
        {
            $this->items = array();
            foreach ($value as $index)
            {
                if (isset($items[$index]))
                {
                    $this->items[$index] = $items[$index];
                }
                else if (isset($this->connectedTo) AND is_array($this->connectedTo))
                {
                    foreach ($this->connectedTo as $connectedList)
                    {
                        if (isset($connectedList->initialItems[$index] ) )
                        {
                            $this->items[$index] = $connectedList->initialItems[$index];
                        }
                    }
                }
            }
        }
    }
    
    /**
     * Connect to another list
     * @param $list Another TSortList
     */
    public function connectTo(TSortList $list)
    {
        $this->connectedTo[] = $list;
    }
    
    /**
     * Add items to the sort list
     * @param $items An indexed array containing the options
     */
    public function addItems($items)
    {
        if (is_array($items))
        {
            $this->initialItems += $items;
            $this->items += $items;
        }
    }
    
    /**
     * Return the sort items
     */
    public function getItems()
    {
        return $this->initialItems;
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
            return array();
        }
    }
    
    /**
     * Enable the field
     */
    public static function enableField($form_name, $field)
    {
        $script = new TElement('script');
        $script->{'language'} = 'JavaScript';
        $script->setUseSingleQuotes(TRUE);
        $script->setUseLineBreaks(FALSE);
        $script->add( " setTimeout(function(){ \$('[itemname={$field}]').sortable('enable') },1); ");
        $script->show();
    }
    
    /**
     * Disable the field
     */
    public static function disableField($form_name, $field)
    {
        $script = new TElement('script');
        $script->{'language'} = 'JavaScript';
        $script->setUseSingleQuotes(TRUE);
        $script->setUseLineBreaks(FALSE);
        $script->add( " setTimeout(function(){ \$('[itemname={$field}]').sortable('disable') },1); ");

        $script->show();
    }
    
    /**
     * Clear the field
     */
    public static function clearField($form_name, $field)
    {
        $script = new TElement('script');
        $script->{'language'} = 'JavaScript';
        $script->setUseSingleQuotes(TRUE);
        $script->setUseLineBreaks(FALSE);
        $script->add( " setTimeout(function(){ \$('[itemname={$field}]').empty() },1); ");

        $script->show();
    }
    
    /**
     * Shows the widget at the screen
     */
    public function show()
    {
        if ($this->items)
        {
            $i = 1;
            // iterate the checkgroup options
            foreach ($this->items as $index => $label)
            {
                // instantiates a new Item
                $item = new TElement('li');
                
                if ($this->itemIcon)
                {
                    $item->add($this->itemIcon);
                }
                
                $item->add(new TLabel($label));
                $item->{'class'} = 'tsortlist_item btn';
                $item->{'style'} = 'display:block;';
                $item->{'id'} = "tsortlist_{$this->name}_item_{$i}_li";
                $item->{'title'} = $this->tag->title;
                
                $input = new TElement('input');
                $input->{'id'}   = "tsortlist_{$this->name}_item_{$i}_li_input";
                $input->{'type'} = 'hidden';
                $input->{'name'} = $this->name . '[]';
                $input->{'value'} = $index;
                $item->add($input);
                
                $this->tag->add($item);
                $i ++;
            }
        }
        
        if (parent::getEditable())
        {
            $connect = '';
            if ($this->connectedTo AND is_array($this->connectedTo))
            {
                foreach ($this->connectedTo as $connectedList)
                {
                    $connectIds[] =  "#{$connectedList->{'id'}}";
                }
                $connect = 'connectWith: "' . implode(', ', $connectIds) . '", ';
            }
            $script = new TElement('script');
            $script-> type = 'text/javascript';
            $script->add("
            	\$(function() {
                    \$( \"#{$this->id}\" ).sortable({
                        {$connect}
                        start: function(event,ui){
                            ui.item.data('index',ui.item.index());
                            ui.item.data('parenta',this.id);
                        },
                        receive: function(event, ui) {
                            var sourceList = ui.sender;
                            var targetList = $(this);
                            targetListName = this.getAttribute('itemname');
                            document.getElementById(ui.item.attr('id') + '_input').name = targetListName + '[]';
                        }
                    }).disableSelection();
            });");
            $script->show();
        }
        $this->tag->show();
    }
}
?>