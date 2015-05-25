<?php

//namespace model\medico;

use Adianti\Database\TRecord;
class ConvenioMedico extends TRecord {
	
	const TABLENAME = 'convenios_medicos';
	const PRIMARYKEY= 'cms_id';
	const IDPOLICY =  'max'; // {max, serial}
	
}

?>