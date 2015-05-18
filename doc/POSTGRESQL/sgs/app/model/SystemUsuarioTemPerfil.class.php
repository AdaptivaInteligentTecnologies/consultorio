<?php
/**
 * System_user_group Active Record
 * @author  <your-name-here>
 */
class SystemUsuarioTemPerfil extends TRecord
{
    const TABLENAME = 'usuarios_tem_perfis';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('usu_prl_usuarios_id');
        parent::addAttribute('usu_prl_perfis_id');
    }
}
?>