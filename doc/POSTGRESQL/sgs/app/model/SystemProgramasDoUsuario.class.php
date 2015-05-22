<?php
/**
 * SystemProgramasDoUsuario Active Record
 * @author  <your-name-here>
 */
class SystemProgramasDoUsuario extends TRecord
{
    const TABLENAME = 'programas_do_usuario';
    const PRIMARYKEY= 'usu_id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL)
    {
        parent::__construct($id);
    }
}
?>