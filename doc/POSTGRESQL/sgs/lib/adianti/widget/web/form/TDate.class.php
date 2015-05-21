<?php
/**
 * DataPicker Widget
 *
 * @version    1.0
 * @package    widget_web
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TDate extends TEntry implements IWidget
{
    private $mask;
    public $id;
    private static $counter;
    
    /**
     * Class Constructor
     * @param $name Name of the widget
     */
    public function __construct($name)
    {
        parent::__construct($name);
        self::$counter ++;
        $this->id   = 'tdate_'.uniqid();
        $this->mask = 'yyyy-mm-dd';
        
        TPage::include_css('lib/adianti/include/tdate/tdate.css');
        
        $newmask = $this->mask;
        $newmask = str_replace('dd',   '99',   $newmask);
        $newmask = str_replace('mm',   '99',   $newmask);
        $newmask = str_replace('yyyy', '9999', $newmask);
        parent::setMask($newmask);
    }
    
    /**
     * Define the field's mask
     * @param $mask  Mask for the field (dd-mm-yyyy)
     */
    public function setMask($mask)
    {
        $this->mask = $mask;
        $newmask = $this->mask;
        $newmask = str_replace('dd',   '99',   $newmask);
        $newmask = str_replace('mm',   '99',   $newmask);
        $newmask = str_replace('yyyy', '9999', $newmask);
        parent::setMask($newmask);
    }
    
    /**
     * Convert a date to format yyyy-mm-dd
     * @param $date = date in format dd/mm/yyyy
     */
    public static function date2us($date)
    {
        if ($date)
        {
            // get the date parts
            $day  = substr($date,0,2);
            $mon  = substr($date,3,2);
            $year = substr($date,6,4);
            return "{$year}-{$mon}-{$day}";
        }
    }
    
    /**
     * Convert a date to format dd/mm/yyyy
     * @param $date = date in format yyyy-mm-dd
     */
    public static function date2br($date)
    {
        if ($date)
        {
            // get the date parts
            $year = substr($date,0,4);
            $mon  = substr($date,5,2);
            $day  = substr($date,8,4);
            return "{$day}/{$mon}/{$year}";
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
        $script->add( " try { document.{$form_name}.{$field}.disabled = false; } catch (e) { } " );
        $script->add( " try { document.{$form_name}.{$field}.className = 'tfield'; } catch (e) { } " );
        $script->add( " setTimeout(function(){ \$('[name={$field}]').next().show() },1);");
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
        $script->add( " try { document.{$form_name}.{$field}.disabled = true; } catch (e) { } " );
        $script->add( " try { document.{$form_name}.{$field}.className = 'tfield_disabled'; } catch (e) { } " );
        $script->add( " setTimeout(function(){ \$('[name={$field}]').next().hide() },1);");
        $script->show();
    }
    
    /**
     * Shows the widget at the screen
     */
    public function show()
    {
        $js_mask = str_replace('yyyy', 'yy', $this->mask);
        
        if (parent::getEditable())
        {
            $icon = file_exists('app/images/tdate.png') ? 'app/images/tdate.png' : 'lib/adianti/images/tdate.png';
            $script = new TElement('script');
            $script-> type = 'text/javascript';
            $script->add("
            	$(function() {
                $(\"#{$this->id}\").datepicker({
                    showOn: 'button',
                    buttonImage: '$icon',
                    buttonImageOnly: false,    
            		changeMonth: true,
            		changeYear: true,
            		dateFormat: '{$js_mask}',
            		showButtonPanel: true
            	});
            });");
            $script->show();
        }
		
        parent::show();
    }
}
?>