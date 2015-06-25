<?php
use Adianti\Database\TRecord;
use Adianti\Database\TRepository;
use Adianti\Database\TTransaction;
//namespace model\Profissional;

class ProfissionalTemConvenio extends TRecord
{
	const TABLENAME = 'profissionais_tem_convenios';
	const PRIMARYKEY= 'ptc_id';
	const IDPOLICY =  'max'; // {max, serial}
	
	public function __construct($id = NULL)
	{
	    parent::__construct($id);
	    parent::addAttribute('ptc_pfs_id');
	    parent::addAttribute('ptc_cps_id');
	     
	}
}

?>