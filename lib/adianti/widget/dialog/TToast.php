<?php
namespace adianti\widget\dialog;

use Adianti\Widget\Base\TScript;

class TToast
{
    
   /**
    * Create a TToast
    *
    * @param string $message
    * @param string $type - (success, info, warning, error) - default: info
    * @param string $title_msg - Default: Informação
    * @param int    $timeOut - default 5000
    */
    function __construct($message='',$type = 'info', $title_msg = 'Informação',$timeOut = 5000)
    {
        
        $command = "toastr['{$type}'](\"{$message}<br />\", '{$title_msg}');";


//            "positionClass": "toast-top-right",
        
        $options = 'toastr.options = {
          "closeButton": true,
          "debug": false,
          "newestOnTop": true,
          "progressBar": true,
          "positionClass": "toast-top-center",
          "preventDuplicates": false,
          "showDuration": "300",
          "hideDuration": "1000",
          "timeOut": "'.$timeOut.'",
          "extendedTimeOut": "1000",
          "showEasing": "swing",
          "hideEasing": "linear",
          "showMethod": "fadeIn",
          "hideMethod": "fadeOut"
        }';
        
        TScript::create($options);
        TScript::create($command);
        
    }
}

?>