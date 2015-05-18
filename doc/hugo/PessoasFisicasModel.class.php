<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PessoasFisicas
 *
 * @author hugo
 */
class PessoasFisicasModel extends TRecord {
    //put your code here
    const TABLENAME = 'sch_api_sgs_data.pessoas_fisicas';
    const PRIMARYKEY= 'id';
    //const IDPOLICY =  'max'; // {max, serial}
    const IDPOLICY =  'serial'; // {max, serial}    
}