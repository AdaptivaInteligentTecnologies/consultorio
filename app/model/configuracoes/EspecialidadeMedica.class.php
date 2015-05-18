<?php

//namespace model\medico;

use Adianti\Database\TRecord;
class EspecialidadeMedica extends TRecord {
	
	const TABLENAME = 'especialidades_medicas';
	const PRIMARYKEY= 'ems_id';
	const IDPOLICY =  'serial'; // {max, serial}
	
}

?>