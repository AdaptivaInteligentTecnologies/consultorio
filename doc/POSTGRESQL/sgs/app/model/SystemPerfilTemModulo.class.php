<?php
/**
 * SystemPerfilTemModulo Active Record
 * @author  <your-name-here>
 */
class SystemPerfilTemModulo extends TRecord
{
    const TABLENAME = 'perfis_tem_modulos';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('prf_mod_modulos_id');
        parent::addAttribute('prf_mod_perfis_id');
        parent::addAttribute('prf_mod_incluir');
        parent::addAttribute('prf_mod_excluir');
        parent::addAttribute('prf_mod_editar');
    }
}
?>