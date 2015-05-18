<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PessoasJuridicasModel
 *
 * @author hugo
 */
class PessoasJuridicasModel extends TRecord {
    //put your code here
    const TABLENAME = 'sch_api_sgs_data.pessoas_juridicas';
    const PRIMARYKEY= 'id';
    //const IDPOLICY =  'max'; // {max, serial}
    const IDPOLICY =  'serial'; // {max, serial}  
    
    public function __construct($id = NULL)
    {
        parent::__construct($id);
       
      
    }
    
    
}