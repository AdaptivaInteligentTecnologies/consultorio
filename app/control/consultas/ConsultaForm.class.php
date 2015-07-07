<?php
use Adianti\Control\TPage;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Base\TElement;
//namespace control\consultas;

class ConsultaForm extends TPage
{

    private $form;
    
    function __construct()
    {
        parent::__construct();
        
        $this->form = new TForm('form_consulta');
        $this->form->class = 'tform';
        
        
        
        $divFotoPaciente = new TElement('div');
        $divFotoPaciente->class = 'foto-paciente';
        
        $divNomePaciente = new TElement('div');
        $divNomePaciente->class = 'nome-paciente';
        
        $divIdadePaciente = new TElement('div');
        $divIdadePaciente->class = 'idade-paciente';
        
        $divDataUltimaConsultaPaciente = new TElement('div');
        $divDataUltimaConsultaPaciente->class = 'data-ultima-consulta-paciente';
        
        $divFormConsultaCabecalhoPaciente = new TElement('div');
        $divFormConsultaCabecalhoPaciente->class = 'form-consulta-cabecalho-paciente';
        
        $divFormConsultaCabecalhoPaciente->add($divFotoPaciente);
        $divFormConsultaCabecalhoPaciente->add($divNomePaciente);
        $divFormConsultaCabecalhoPaciente->add($divIdadePaciente);
        $divFormConsultaCabecalhoPaciente->add($divDataUltimaConsultaPaciente);
        
        parent::add($divFormConsultaCabecalhoPaciente);
        
        
        
        /*
        <div class='form-consulta-cabecalho-paciente'>
        <div class='foto-paciente'></div>
        <div class='nome-paciente'><span id="#nome-paciente"></span></div>
        <div class="idade-paciente"><span id="#idade-paciente"></span></div>
        <div class="data-ultima-consulta-paciente"><span id="#data-ultima-consulta-paciente"></span></div>
        </div>
        </div>
        */
        
    }
    
    public function onReload($param)
    {
        var_dump($param);
    }
}

?>