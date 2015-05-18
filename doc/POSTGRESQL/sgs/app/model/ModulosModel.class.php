<?php
/**
 * SystemModulos Active Record
 * @author  <your-name-here>
 */
class ModulosModel extends TRecord
{
    const TABLENAME = 'modulos';
    const PRIMARYKEY= 'id';
    //const IDPOLICY =  'max'; // {max, serial}
    const IDPOLICY =  'serial'; // {max, serial}
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('mod_modulos_id');
        parent::addAttribute('mod_nome');
       	parent::addAttribute('mod_descricao');
       	parent::addAttribute('mod_controller');
       	parent::addAttribute('mod_imagem');
       	parent::addAttribute('mod_item_de_menu');
       	
    }
}

?>