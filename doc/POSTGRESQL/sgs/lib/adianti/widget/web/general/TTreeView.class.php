<?php
/**
 * TreeView
 * 
 * @version    1.0
 * @package    widget_web
 * @subpackage general
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TTreeView extends TElement
{
    private $itemIcon;
    private $itemAction;
    private $collapsed;
    
    /**
     * Class Constructor
     */
    public function __construct()
    {
        $this->id   = 'ttreeview_'.uniqid();
        $this->collapsed = FALSE;
        parent::__construct('ul');
    }
    
    /**
     * Set size
     * @param $size width
     */
    public function setSize($width)
    {
        $this->style = "width: {$width}px";
    }
    
    /**
     * Set item icon
     * @param $icon icon location
     */
    public function setItemIcon($icon)
    {
        $this->itemIcon = $icon;
    }
    
    /**
     * Set item action
     * @param $action icon action
     */
    public function setItemAction($action)
    {
        $this->itemAction = $action;
    } 
    
    /**
     * Collapse the Tree
     */
    public function collapse()
    {
        $this->collapsed = TRUE;
    }
    
    /**
     * Fill treeview from an multi-dimensional array
     * @param multi-dimensional array
     */
    public function fromArray($array)
    {
        if (is_array($array))
        {
            foreach ($array as $key => $option)
            {
                if (is_scalar($option))
                {
                    $element = new TElement('li');
                    $span = new TElement('span');
                    $span->{'class'} = 'file';
                    $span->add($option);
                    if ($this->itemIcon)
                    {
                        $element->{'style'} = "background-image:url(app/images/{$this->itemIcon})";
                    }
                    
                    if ($this->itemAction)
                    {
                        $this->itemAction->setParameter('key', $key);
                        $this->itemAction->setParameter('value', $option);
                        $string_action = $this->itemAction->serialize(FALSE);
                        $element->{'onClick'} = "ajaxExec('{$string_action}')";
                    }
                    
                    $element->add($span);
                    parent::add($element);
                }
                else if (is_array($option))
                {
                    $element = new TElement('li');
                    $span = new TElement('span');
                    $span->{'class'} = 'folder';
                    $span->add($key);
                    $element->add($span);
                    $element->add($this->fromOptions($option));
                    parent::add($element);
                }
            }
        }
    }
    
    /**
     * Fill one level of the treeview
     * @param $options array of options
     * @ignore-autocomplete on
     */
    private function fromOptions($options)
    {
        if (is_array($options))
        {
            $ul = new TElement('ul');
            foreach ($options as $key => $option)
            {
                if (is_scalar($option))
                {
                    $element = new TElement('li');
                    $span = new TElement('span');
                    $span->{'class'} = 'file';
                    $span->add($option);
                    if ($this->itemIcon)
                    {
                        $element->{'style'} = "background-image:url(app/images/{$this->itemIcon})";
                    }
                    
                    if ($this->itemAction)
                    {
                        $this->itemAction->setParameter('key', $key);
                        $this->itemAction->setParameter('value', $option);
                        $string_action = $this->itemAction->serialize(FALSE);
                        $element->{'onClick'} = "ajaxExec('{$string_action}')";
                        
                    }
                    $element->add($span);
                }
                else if (is_array($option))
                {
                    $element = new TElement('li');
                    $span = new TElement('span');
                    $span->{'class'} = 'folder';
                    $span->add($key);
                    $element->add($span);
                    $element->add($this->fromOptions($option));
                }
                $ul->add($element);
            }
            return $ul;
        }
    }
    
    /**
     * Shows the tag
     */
    public function show()
    {
        TPage::include_js('lib/adianti/include/ttreeview/jquery.treeview.js');
        TPage::include_css('lib/adianti/include/ttreeview/jquery.treeview.css');
        $collapsed = $this->collapsed ? 'true' : 'false';
        $script = new TElement('script');
        $script-> type = 'text/javascript';
        $script->add("
        	\$(document).ready(function(){
        	    \$('#{$this->id}').treeview({
        		persist: 'location',
        		animated: 'fast',
        		collapsed: {$collapsed}
        	}); });");
        parent::add($script);
        parent::show();
    }
}
?>