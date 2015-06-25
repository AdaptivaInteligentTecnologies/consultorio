<?php
//namespace model\profissionais;

use Adianti\Database\TRecord;
class Conselho extends TRecord
{
    const TABLENAME  = 'conselhos';
    const PRIMARYKEY = 'css_id';
    const IDPOLICY   = 'max';
    
    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('css_id');
        parent::addAttribute('css_descricao');
    
    }    
}

?>