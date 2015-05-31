<?php
use Adianti\Control\TPage;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Container\TTable;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Util\TBreadCrumb;
use Adianti\Widget\Form\TEntry;
//namespace control\consultorio;

class ConsultorioForm extends TPage
{
    protected $form;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->form         = new TForm('form_consultorio');
        $this->form->class  = 'tform';
        $table              = new TTable();
        $table->style       = 'width:100%';
        $table->addRowSet(new TLabel('Cadastro de consultório'),'')->class = 'tformtitle';
        $this->form->add($table);
        
        
        $conId                  = new TEntry('con_id');
        $conNome                = new TEntry('con_nome');
        $conIniFuncionamento    = new TEntry('con_ini_funcionamento');
        $conFimFuncionamento    = new TEntry('con_fim_funcionamento');
        con_ini_almoco varchar(5),
        con_fim_almoco varchar(5),
        con_fun_segunda boolean,
        con_fun_terca boolean,
        con_fun_quarta boolean,
        con_fun_quinta boolean,
        con_fun_sexta boolean,
        con_fun_sabado boolean,
        con_fun_domingo boolean
        
        
        
        
        $container          = new TTable();
        $container->style   = 'width:80%';
        $container->addRow()->addCell(new TBreadCrumb('menu.xml','CadastroConsultorioList'));
        $container->addRow()->addCell($this->form);
        //$container->addRow()->addCell($script);
        
        parent::add($container);
        
    }
}

?>