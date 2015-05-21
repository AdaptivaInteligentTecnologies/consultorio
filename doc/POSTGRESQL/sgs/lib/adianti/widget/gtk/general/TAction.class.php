<?php
/**
 * Structure to encapsulate an action
 *
 * @version    1.0
 * @package    widget_gtk
 * @subpackage general
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TAction
{
    protected $action;
    protected $param;
    
    /**
     * Class Constructor
     * @param $action Callback to be executed
     */
    public function __construct($action)
    {
        $this->action = $action;
        if (!is_callable($this->action))
        {
            $action_string = $this->toString();
            throw new Exception(TAdiantiCoreTranslator::translate('Method ^1 must receive a paremeter of type ^2', __METHOD__, 'Callback'). ' <br> '.
                                TAdiantiCoreTranslator::translate('Check if the action (^1) exists', $action_string));
        }
    }
    
    /**
     * Returns the action as a string
     */
    public function toString()
    {
        $action_string = '';
        if (is_string($this->action))
        {
            $action_string = $this->action;
        }
        else if (is_array($this->action))
        {
            if (is_object($this->action[0]))
            {
                $action_string = get_class($this->action[0]) . '::' . $this->action[1];
            }
            else
            {
                $action_string = $this->action[0] . '::' . $this->action[1];
            }
        }
        return $action_string;
    }
    
    /**
     * Adds a parameter to the action
     * @param  $param = parameter name
     * @param  $value = parameter value
     */
    public function setParameter($param, $value)
    {
        $this->param[$param] = $value;
    }
    
    /**
     * Set the parameters for the action
     * @param  $parameters = array of parameters
     */
    public function setParameters($parameters)
    {
        // does not override the action
        unset($parameters['class']);
        unset($parameters['method']);
        $this->param = $parameters;
    }
    
    /**
     * Returns a parameter
     * @param  $param = parameter name
     */
    public function getParameter($param)
    {
        return $this->param[$param];
    }
    
    /**
     * Return the Action Parameters
     */
    public function getParameters()
    {
        return $this->param;
    }
    
    /**
     * Return the Action Callback
     * @return  The Action Callback
     */
    public function getAction()
    {
        return $this->action;
    }
    
    /**
     * Returns if the action is static
     */
    public function isStatic()
    {
        if (is_callable($this->action) AND is_array($this->action))
        {
            $class  = is_string($this->action[0])? $this->action[0]: get_class($this->action[0]);
            $method = $this->action[1];
            
            $rm = new ReflectionMethod( $class, $method );
            return $rm->isStatic();
        }
        return FALSE;
    }
}
?>