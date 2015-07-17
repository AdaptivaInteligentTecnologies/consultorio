<?php
use Adianti\Control\TPage;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Container\TNotebook;
use Adianti\Widget\Container\TFrame;
use Adianti\Control\TWindow;
use Adianti\Widget\Form\TText;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Form\TButton;
use Adianti\Control\TAction;
use adianti\widget\dialog\TToast;
use Adianti\Widget\Container\THBox;

class ConsultaForm extends TPage
{
    protected $form;
    protected $notebook;
    protected $btnProcessaConsulta;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->form = new TForm('form_consulta_paciente');
        $this->form->class = 'tform';
        
        $this->btnProcessaConsulta = new TButton('iniciarConsulta');
        //$this->btnProcessaConsulta->setLabel('Iniciar Atendimento');
        $this->btnProcessaConsulta->setImage('bs:plus white');
        $this->btnProcessaConsulta->class = 'btn btn-success';
        $this->btnProcessaConsulta->popover = 'true';
        $this->btnProcessaConsulta->poptitle = 'Informação';
        $this->btnProcessaConsulta->popcontent = 'Inicia um novo atendimento de consulta';
        $this->btnProcessaConsulta->setAction(new TAction(array($this,'onProcessaConsulta')),'Iniciar Atendimento');
        
        //$btnIniciarConsulta->setAction(new TAction(array('ConsultaForm','onReload')),'Nova consulta');
        
        $divFotoPaciente = new TElement('div');
        $divFotoPaciente->class = 'foto-paciente';
        
        $divNomePaciente = new TElement('div');
        $divNomePaciente->class = 'nome-paciente';
        $divNomePaciente->add("Nome do Paciente");
        $divNomePaciente->add($this->paciente->pts_nome);
        
        $divIdadePaciente = new TElement('div');
        $divIdadePaciente->class = 'idade-paciente';
        $divIdadePaciente->add( TDate::getIdade($this->paciente->pts_data_nascimento));
        
        $divDataUltimaConsultaPaciente = new TElement('div');
        $divDataUltimaConsultaPaciente->class = 'data-ultima-consulta-paciente';
        $divDataUltimaConsultaPaciente->add("Data da última consulta: ".Consulta::getDataUltimaConsultaPorPaciente($pacienteId));
        
        $divNovoAtendimentoConsulta = new TElement('div');
        $divNovoAtendimentoConsulta->class = 'novo-atendimento-consulta-paciente';
        $divNovoAtendimentoConsulta->add($this->btnProcessaConsulta);
        
        $divFormConsultaCabecalhoPaciente = new TElement('div');
        $divFormConsultaCabecalhoPaciente->class = 'form-consulta-cabecalho-paciente';
        
        $divFormConsultaCabecalhoPaciente->add($divFotoPaciente);
        $divFormConsultaCabecalhoPaciente->add($divNomePaciente);
        $divFormConsultaCabecalhoPaciente->add($divIdadePaciente);
        $divFormConsultaCabecalhoPaciente->add($divDataUltimaConsultaPaciente);
        $divFormConsultaCabecalhoPaciente->add($divNovoAtendimentoConsulta);
        
        $pts_hpp                            = new TText('pts_hpp');
        $pts_hf                             = new TText('pts_hf');
        $pts_hpfs                           = new TText('pts_hpfs');
        $pts_isda                           = new TText('pts_isda');
        $cns_queixa_principal               = new TText('cns_queixa_principal');
        $cns_historico_doenca_atual         = new TText('cns_historico_doenca_atual');
        $cns_medimento_em_uso               = new TText('cns_medimento_em_uso');
        $cns_hipotese_diagnostica	        = new TText('cns_hipotese_diagnostica');
        
        $pts_hpp->style                     .= "width:100% !important;";
        $pts_hf->style                      .= "width:100% !important;";
        $pts_hpfs->style                    .= "width:100% !important;";
        $pts_isda->style                    .= "width:100% !important;";
        $cns_historico_doenca_atual->style  .= "width:100% !important;";
        $cns_hipotese_diagnostica->style    .= "width:100% !important;";
        $cns_medimento_em_uso->style        .= "width:100% !important;";
        $cns_queixa_principal->style        .= "width:100% !important;";
        
        $frameHistoricoPatologicoPregressa  = new TFrame();
        $frameHistoricoPatologicoPregressa->setLegend('Histórico patológico pregressa');
        
        $frameQueixaPrinciapal = new TFrame();
        $frameQueixaPrinciapal->setLegend('Queixa Principal');
        $frameQueixaPrinciapal->add($cns_queixa_principal);
        
        $frameHistoricoDoencaAtual = new TFrame();
        $frameHistoricoDoencaAtual->setLegend('Histórico da doença atual');
        $frameHistoricoDoencaAtual->add($cns_historico_doenca_atual);
        
        $frameMedicamentoEmUso = new TFrame();
        $frameMedicamentoEmUso->setLegend('Medicamentos em uso');
        $frameMedicamentoEmUso->add($cns_medimento_em_uso);
        
        $frameHipoteseDiagnostica = new TFrame();
        $frameHipoteseDiagnostica->setLegend('Hipotese Diagnóśtica');
        $frameHipoteseDiagnostica->add($cns_hipotese_diagnostica);
        
        
        
        
        
        $vboxText = new TVBox('vBoxText');
        
        $vboxText->style = 'width:100%; float:left;';
        $vboxText->add($frameQueixaPrinciapal);
        $vboxText->add($frameHistoricoDoencaAtual);
        $vboxText->add($frameMedicamentoEmUso);
        $vboxText->add($frameHipoteseDiagnostica);
        
        $vboxButtons = new TVBox;
        $vboxButtons->style = 'width:100%; float:left;';
        
        $btnDadosComplementares = new TButton('dadosComplementares');
        $btnDadosComplementares->setLabel('Dados Complementares');
        
        $btnAnamnese = new TButton('anamnese');
        $btnAnamnese->setLabel('Anamnese');
        
        $btnExamesFisicos = new TButton('examesFisicos');
        $btnExamesFisicos->setLabel('Exames Físicos');

        $vboxButtons->add($btnDadosComplementares);
        $vboxButtons->add($btnAnamnese);
        $vboxButtons->add($btnExamesFisicos);
        
        $hBoxFrame = new THBox;
            
        
        $hBoxFrame->style = 'width:100%';
        $hBoxFrame->add($vboxButtons)->style='float:left';
        $hBoxFrame->add($vboxText);
        
        
        
        
        
        
        $this->form->setFields(
            
            
            array(
                $this->btnProcessaConsulta,
                
            ));
        
        $this->form->add($hBoxFrame);

//        var_dump($this->form);
        
/*
 *  $bt5a->addFunction("alert('action 1');");
        $bt5b->addFunction("alert('going to another page');__adianti_load_page('index.php?class=FormQuickView');");
        $bt5c->addFunction("if (confirm('Want to go?') == true) { __adianti_load_page('index.php?class=ContainerWindowView'); }");
        
 */        
        
        
        
        parent::add($divFormConsultaCabecalhoPaciente);
        parent::add($this->form);
        
        
        
        
        
        
    }
    
    public function onReload($param)
    {
        //
    }
    
    public function onEncerrarConsulta()
    {
        //
    }
    
    public function onProcessaConsulta()
    {
        $this->btnProcessaConsulta->setImage('bs:square white');
        $this->btnProcessaConsulta->class = 'btn btn-danger';
        $this->btnProcessaConsulta->popover = 'true';
        $this->btnProcessaConsulta->poptitle = 'Informação';
        $this->btnProcessaConsulta->popcontent = 'Iniciar consulta';
        $this->btnProcessaConsulta->setAction(new TAction(array($this,'onEncerrarConsulta')),'Encerrar consulta');
    }
} 