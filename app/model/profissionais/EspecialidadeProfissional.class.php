<?php

//namespace model\medico;

use Adianti\Database\TRecord;
class EspecialidadeProfissional extends TRecord {
	
	const TABLENAME = 'especialidades_profissionais';
	const PRIMARYKEY= 'ems_id';
	const IDPOLICY =  'serial'; // {max, serial}
	
	
	public function __construct($id = NULL){
	    parent::__construct($id);

	    parent::addAttribute('ems_id');
	    parent::addAttribute('ems_descricao');
	     
	}
	
}

?>