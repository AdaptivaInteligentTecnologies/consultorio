<?php

//namespace model\medico;

use Adianti\Database\TRecord;
class EspecialidadeMedica extends TRecord {
	
	const TABLENAME = 'especialidades_medicas';
	const PRIMARYKEY= 'ems_id';
	const IDPOLICY =  'serial'; // {max, serial}
	
	
	public function __construct($id = NULL){
	    parent::__construct($id);

	    parent::addAttribute('ems_id');
	    parent::addAttribute('ems_descricao');
	     
	}
	
}

?>