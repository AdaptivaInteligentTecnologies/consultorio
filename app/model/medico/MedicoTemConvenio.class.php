<?php
use Adianti\Database\TRecord;
//namespace model\medico;

class MedicoTemConvenio extends TRecord
{
	const TABLENAME = 'medicos_tem_convenios';
	const PRIMARYKEY= 'mtc_id';
	const IDPOLICY =  'max'; // {max, serial}
	
	public function __construct($id = NULL)
	{
	    parent::__construct($id);
	    parent::addAttribute('mtc_med_id');
	    parent::addAttribute('mtc_cms_id');
	     
	}
}

?>