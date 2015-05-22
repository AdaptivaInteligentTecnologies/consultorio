<?php
/**
 * Window container
 *
 * @version    1.0
 * @package    widget_gtk
 * @subpackage container
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TWindow extends GtkWindow
{
    /**
     * Class Constructor
     * @param $title Window's title
     */
    public function __construct($title='')
    {
        parent::__construct();
        parent::set_position(Gtk::WIN_POS_CENTER);
        parent::set_border_width(4);
        parent::connect('key_press_event', array($this, 'onKeyTest'));
        parent::connect_simple('delete-event', array($this, 'onClose'));
        parent::set_title($title);
    }
    
    /**
     * Define the window's title
     * @param  $title  Window's title
     */
    public function setTitle($title)
    {
        parent::set_title($title);
    }
    
    /**
     * Define the window's size
     * @param  $width  Window's width
     * @param  $height Window's height
     */
    public function setSize($width, $height)
    {
        parent::set_size_request($width, $height);
    }
    
    /**
     * Define the top corner positions
     * @param $x left coordinate
     * @param $y top  coordinate
     */
    public function setPosition($x, $y)
    {
        parent::set_uposition($x, $y);
    }
    
    /**
     * Test the pressed key
     * @param  $object  Source object
     * @param  $event   Event
     * @ignore-autocomplete on
     */
    public function onKeyTest($object, $event)
    {
        if ($event-> keyval == Gdk::KEY_Escape)
        {
            parent::hide();
        }
    }
    
    /**
     * Executed when the user closes the window
     */
    public function onClose()
    {
        parent::hide();
        return TRUE;
    }
    
    /**
     * Close Window
     */
    public function closeWindow()
    {
        parent::hide();
    }
    
    /**
     * Show the window
     */
    public function show()
    {
        if (parent::get_child())
        {
            parent::get_child()->show();
        }
        parent::show_all();
    }
}
?>