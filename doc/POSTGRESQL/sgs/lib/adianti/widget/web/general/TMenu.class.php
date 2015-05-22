<?php
/**
 * Menu Widget
 *
 * @version    1.0
 * @package    widget_web
 * @subpackage general
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TMenu extends TElement
{
    private $items;
    
    /**
     * Class Constructor
     * @param $xml SimpleXMLElement parsed from XML Menu
     */
    public function __construct($xml, $permission_callback = NULL)
    {
        parent::__construct('ul');
        $this->{'class'} = 'dropdown-menu';
        $this->items = array();
        
        if ($xml instanceof SimpleXMLElement)
        {
            $this->parse($xml, $permission_callback);
        }
    }
    
    /**
     * Add a MenuItem
     * @param $menuitem A TMenuItem Object
     */
    public function addMenuItem(TMenuItem $menuitem)
    {
        $this->items[] = $menuitem;
    }
    
    /**
     * Return the menu items
     */
    public function getMenuItems()
    {
        return $this->items;
    }
    
    /**
     * Parse a XMLElement reading menu entries
     * @param $xml A SimpleXMLElement Object
     * @param $permission_callback check permission callback
     */
    public function parse($xml, $permission_callback = NULL)
    {
        $i = 0;
        foreach ($xml as $xmlElement)
        {
            $atts   = $xmlElement->attributes();
            $label  = (string) $atts['label'];
            $action = (string) $xmlElement-> action;
            $icon   = (string) $xmlElement-> icon;
            
            $menuItem = new TMenuItem($label, $action, $icon);
            
            if ($xmlElement->menu)
            {
                $menu = new TMenu($xmlElement-> menu-> menuitem, $permission_callback);
                $menuItem->setMenu($menu);
            }
            
            // just child nodes have actions
            if ($action)
            {
                if ( $permission_callback )
                {
                    // check permission
                    $parts = explode('#', $action);
                    $className = $parts[0];
                    if (call_user_func($permission_callback, $className))
                    {
                        $this->addMenuItem($menuItem);
                    }
                }
                else
                {
                    // menus without permission check
                    $this->addMenuItem($menuItem);
                }
            }
            // parent nodes are shown just when they have valid children (with permission)
            else if ( count($menu->getMenuItems()) > 0)
            {
                $this->addMenuItem($menuItem);
            }
            
            $i ++;
        }
    }
    
    /**
     * Shows the widget at the screen
     */
    public function show()
    {    
        if ($this->items)
        {
            foreach ($this->items as $item)
            {
                parent::add($item);
            }
        }
        parent::show();
    }
}
?>