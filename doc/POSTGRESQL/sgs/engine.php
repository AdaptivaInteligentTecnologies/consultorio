<?php
// define the autoloader
include_once 'lib/adianti/util/TAdiantiLoader.class.php';
spl_autoload_register(array('TAdiantiLoader', 'autoload_web'));

// read configurations
$ini  = parse_ini_file('application.ini');
date_default_timezone_set($ini['timezone']);
TAdiantiCoreTranslator::setLanguage( $ini['language'] );
TApplicationTranslator::setLanguage( $ini['language'] );

// define constants
define('APPLICATION_NAME', $ini['application']);
define('OS', strtoupper(substr(PHP_OS, 0, 3)));
define('PATH', dirname(__FILE__));

class TApplication extends TCoreApplication
{
    static public function run($debug = FALSE)
    {
        new TSession;
        if ($_REQUEST)
        {
            $class = isset($_REQUEST['class']) ? $_REQUEST['class']   : '';
            
            if( ! TSession::getValue('logged') AND $class !== 'LoginForm' )
            {
                new TMessage('error', _t('Permission denied'), new TAction(array('LoginForm','onLogout')) );
            }
            else
            {
                if( $class AND $class !== 'LoginForm' )
                {
                    $programs = (array) TSession::getValue('programs');
                    $default_programs = array('TStandardSeek' => TRUE,
                                              'TFileUploader' => TRUE,
//                    						  'SystemMenu' => TRUE, // inserido por carnegie
                                              'EmptyPage' => TRUE );
                    $programs = array_merge($programs,$default_programs);
                    if( ! isset($programs[$class]) )
                    {
                        //new TMessage('info', var_dump($programs));
                    	new TMessage('error', _t('Permission denied'));
                        return false;                    
                    }
                }
                parent::run($debug);
            }
        }
    }
}

TApplication::run(TRUE);
?>