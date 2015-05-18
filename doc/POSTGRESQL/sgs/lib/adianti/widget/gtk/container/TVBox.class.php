<?php
/**
 * Vertical Box
 *
 * @version    1.0
 * @package    widget_web
 * @subpackage container
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TVBox extends GtkVBox
{
    /**
     * Shows the VBox
     */
    public function show()
    {
        $children = parent::get_children();
        if ($children)
        {
            foreach ($children as $child)
            {
                // show child object
                $child->show();
            }
        }
        parent::show_all();
    }
}
?>