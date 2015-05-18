<?php
/**
 * Basic structure to run a web application
 *
 * @version    1.0
 * @package    widget_web
 * @subpackage general
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TCoreApplication
{
    public static function run($debug = FALSE)
    {
        $class   = isset($_REQUEST['class'])    ? $_REQUEST['class']   : '';
        $static  = isset($_REQUEST['static'])   ? $_REQUEST['static']  : '';
        $method  = isset($_REQUEST['method'])   ? $_REQUEST['method']  : '';
        $content = '';
        set_error_handler(array('TCoreApplication', 'errorHandler'));
        
        if (class_exists($class))
        {
            if ($static)
            {
                $rf = new ReflectionMethod($class, $method);
                if ($rf->isStatic())
                {
                    call_user_func(array($class, $method),$_REQUEST);
                }
                else
                {
                    call_user_func(array(new $class($_GET), $method),$_REQUEST);
                }
            }
            else
            {
                try
                {
                    $page = new $class($_GET);
                    ob_start();
                    $page->show( $_GET );
	                $content = ob_get_contents();
	                ob_end_clean();
                }
                catch(Exception $e)
                {
                    ob_start();
                    if ($debug)
                    {
                        new TExceptionView($e);
                        $content = ob_get_contents();
                    }
                    else
                    {
                        new TMessage('error', $e->getMessage());
                        $content = ob_get_contents();
                    }
                    ob_end_clean();
                }
            }
        }
        else if (function_exists($method))
        {
            call_user_func($method, $_REQUEST);
        }
        else
        {
            new TMessage('error', "<b>Error</b>: class <b><i><u>{$class}</u></i></b> not found");
        }
        
        if (!$static)
        {
            echo TPage::getLoadedCSS();
        }
        echo TPage::getLoadedJS();
        
        echo $content;
    }
    
    /**
     * Execute a specific method of a class with parameters
     *
     * @param $class class name
     * @param $method method name
     * @param $parameters array of parameters
     */
    static public function executeMethod($class, $method = NULL, $parameters = NULL)
    {
        self::gotoPage($class, $method, $parameters);
    }
    
    /**
     * Goto a page
     *
     * @param $class class name
     * @param $method method name
     * @param $parameters array of parameters
     */
    static public function gotoPage($class, $method = NULL, $parameters = NULL)
    {
        $url = array();
        $url['class']  = $class;
        $url['method'] = $method;
        unset($parameters['class']);
        unset($parameters['method']);
        $url = array_merge($url, (array) $parameters);
        
        echo "<script language='JavaScript'> __adianti_goto_page('index.php?".http_build_query($url)."'); </script>";
    }
    
    /**
     * Load a page
     *
     * @param $class class name
     * @param $method method name
     * @param $parameters array of parameters
     */
    static public function loadPage($class, $method = NULL, $parameters = NULL)
    {
        $url = array();
        $url['class']  = $class;
        $url['method'] = $method;
        unset($parameters['class']);
        unset($parameters['method']);
        $url = array_merge($url, (array) $parameters);
        
        echo "<script language='JavaScript'> __adianti_load_page('index.php?".http_build_query($url)."'); </script>";
    }
    
    /**
     * Register URL
     *
     * @param $page URL to be registered
     */
    static public function registerPage($page)
    {
        echo "<script language='JavaScript'> __adianti_register_state('{$page}', 'user'); </script>";
    }
    
    /**
     * Handle Catchable Errors
     */
    static public function errorHandler($errno, $errstr, $errfile, $errline)
    {
    	if ( $errno === E_RECOVERABLE_ERROR ) { 
    		throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
    	}
     
    	return false;
    }
}
?>