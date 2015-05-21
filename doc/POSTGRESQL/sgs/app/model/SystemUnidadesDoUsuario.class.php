<?php
/**
 * SystemProgramasDoUsuario Active Record
 * @author  <your-name-here>
 */
class SystemUnidadesDoUsuario extends TRecord
{
    const TABLENAME = 'unidades';
    const PRIMARYKEY= 'id';
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