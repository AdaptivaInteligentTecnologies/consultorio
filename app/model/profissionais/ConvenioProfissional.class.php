<?php

//namespace model\medico;

use Adianti\Database\TRecord;
class ConvenioProfissional extends TRecord {
	
	const TABLENAME = 'convenios_profissionais';
	const PRIMARYKEY= 'cps_id';
	const IDPOLICY =  'max'; // {max, serial}
	
}

?>