<?php
use Adianti\Database\TRecord;
use Adianti\Widget\Form\TDate;
//namespace model\pacientes;

class Paciente extends TRecord
{
   const TABLENAME     = 'pacientes';
   const PRIMARYKEY    = 'pts_id';
   const IDPOLICY      = 'max';
   const CACHECONTROL  = 'TAPCache';
   
   public function __construct($id = NULL)
   {
       parent::__construct($id);
   }
   
    
    
}

?>