<?php
use Adianti\Control\TPage;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Form\TDate;
use Adianti\Widget\Form\TButton;
use Adianti\Control\TAction;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Container\TNotebook;
use Adianti\Widget\Container\TScroll;
use Adianti\Widget\Form\THtmlEditor;
use Adianti\Widget\Form\TLabel;
//namespace control\consultas;

class FilaAtendimentoForm extends TPage
{

    private   $form;
    protected $paciente;
    protected $pacienteId;
    protected $divTimeLine;
    protected $btnNovoAtendimentoDeConsulta;    
    protected $tabPaneResumo;
    protected $iniciouAtendimento;
    
    protected $cns_queixa_principal;
    protected $cns_historico_doenca_atual;
    protected $cns_evolucao;
    
    function __construct()
    {
        
        parent::__construct();

        parent::include_css('lib/vertical-timeline/css/style.css');
        parent::include_js ('lib/vertical-timeline/js/main.js');
        parent::include_js ('lib/vertical-timeline/js/modernizr.js');
        
        $this->form = new TForm('form_consulta');
        $this->form->class = 'tform';
        
        if (!isset($this->pacienteId))
        {
            $this->pacienteId = $_REQUEST['key'];
        }
        
        $this->paciente = new Paciente($this->pacienteId);
        
        $this->btnNovoAtendimentoDeConsulta = new TButton('novoAtendimentoDeConsulta');
        $this->btnNovoAtendimentoDeConsulta->setImage('bs:plus white');
        $this->btnNovoAtendimentoDeConsulta->class = 'btn btn-success';
        $this->btnNovoAtendimentoDeConsulta->popover = 'true';
        $this->btnNovoAtendimentoDeConsulta->poptitle = 'Informação';
        $this->btnNovoAtendimentoDeConsulta->popcontent = 'Iniciar novo atendimento de consulta';
        $actBtnNovoAtencimentoDeConsulta = new TAction(array($this,'onIniciarAtendimento'));
        $actBtnNovoAtencimentoDeConsulta->setParameter('key', $this->pacienteId);
        $this->btnNovoAtendimentoDeConsulta->setAction($actBtnNovoAtencimentoDeConsulta,'Iniciar Atendimento');
        
        $btnDetalhesPaciente = new TButton('btnDetalhesDoPaciente');
        $btnDetalhesPaciente->setImage('bs:edit');
        $btnDetalhesPaciente->class = 'btn btn-xs btn-default btn-dados-cadastrais';
        $btnDetalhesPaciente->popover = 'true';
        $btnDetalhesPaciente->poptitle = 'Informação';
        $btnDetalhesPaciente->popcontent = 'Detalhes cadastrais do paciente';
        $btnDetalhesPaciente->setAction(new TAction(array('PacienteList','onReload')),'Detalhes');
        
        
        // Cabeçalho com os dados do paciente
        
        $divCabecalho = new TElement('div');
        $divCabecalho->class = 'fila-atendimento-form-cabecalho shadow1';
        $divCabecalho->id = 'divCabecalho';
        
        
        $divFotoPaciente = new TElement('div');
        $divFotoPaciente->class = 'foto-paciente';
        $divFotoPaciente->id = 'divFotoPaciente';
        
        
        $divDadosPaciente = new TElement('div');
        $divDadosPaciente->class = 'dados-paciente';
        $divDadosPaciente->id = 'divDadosPaciente';
        
        
        $divNomePaciente = new TElement('div');
        $divNomePaciente->class = 'nome-paciente';
        $divNomePaciente->add($this->paciente->pts_nome);
        
        $divIdadePaciente = new TElement('div');
        $divIdadePaciente->class = 'idade-paciente';
        $divIdadePaciente->add( TDate::getIdade($this->paciente->pts_data_nascimento));
        
        $divDataUltimaConsultaPaciente = new TElement('div');
        $divDataUltimaConsultaPaciente->class = 'data-ultima-consulta-paciente';
        $divDataUltimaConsultaPaciente->add(Consulta::getDataUltimaConsultaPorPaciente($this->pacienteId));
        
        $divNovoAtendimentoConsulta = new TElement('div');
        $divNovoAtendimentoConsulta->class = 'novo-atendimento-consulta-paciente';
        $divNovoAtendimentoConsulta->add($this->btnNovoAtendimentoDeConsulta);
        
        $divFormConsultaCabecalhoPaciente = new TElement('div');
        $divFormConsultaCabecalhoPaciente->class = 'form-consulta-cabecalho-paciente';
        $divCabecalho->add($divFotoPaciente);
        $divNomePaciente->add("  ");
        
        $divProgressTimer = new TElement('div');
        $divProgressTimer->class = 'progress-timer';
        $divProgressTimer->id = 'progressTimer';
        
        
        
        
        
        //Anamnese
        $this->cns_queixa_principal = new THtmlEditor('cns_queixa_principal'); // queixa principal
        $this->cns_queixa_principal->setSize(700);
        $this->cns_historico_doenca_atual = new THtmlEditor('cns_historico_doenca_atual'); // hda
        $this->cns_historico_doenca_atual->setSize(700);
        
        // Exame Físico
        // Hipótese Diagnóstica

        // Evolução
        $this->cns_evolucao = new THtmlEditor('cns_evolucao');
        $this->cns_evolucao->setSize(700);
        
        // Prescrição
        // Atestado
        


        //$divNomePaciente->add('<span class="label">Detalhes</span>');
        $divDetalhesPaciente = new TElement('div');
        $divDetalhesPaciente->add($btnDetalhesPaciente);
        $divDetalhesPaciente->class = 'btn-detalhes-paciente';

        $divDadosPaciente->add($divNomePaciente);
        $divDadosPaciente->add($divDetalhesPaciente);
        $divDadosPaciente->add($divIdadePaciente);
        $divDadosPaciente->add($divDataUltimaConsultaPaciente);
        $divDadosPaciente->add($divNovoAtendimentoConsulta);

        $divCabecalho->add($divDadosPaciente);
       
        $this->form->setFields(array(
            $this->btnNovoAtendimentoDeConsulta,
            $btnDetalhesPaciente,
        ));
        
        parent::add($divCabecalho);
        
        
    }
    
    public function onReload($param)
    {
        
        
        // TAB
        
        $tabbable = new TElement('div');
        $tabbable->class = 'tabbable';
        $tabbable->style .='; background-color:white; height:100%; min-height:300px';
        
        $tabSessions = new TElement('ul');
        $tabSessions->class='nav nav-tabs';
        
        // cabeçalho dos tabs
        $tabSessionResumo = new TElement('li');
        $tabSessionResumo->class='active';
        $tabSessionResumo->add('<a href="#tabResumo" data-toggle="tab" aria-expanded="true">Resumo</a>');
        
        $tabSessionAnamnese = new TElement('li');
        $tabSessionAnamnese->class='';
        $tabSessionAnamnese->add('<a href="#tabAnamnese" data-toggle="tab">Anamnese</a>');
        
        
        $tabSessionExameFisico = new TElement('li');
        $tabSessionExameFisico->class='';
        $tabSessionExameFisico->add('<a href="#tabExameFisico" data-toggle="tab">Exame Físico</a>');
        
        $tabSessionHipoteseDiagnostica = new TElement('li');
        $tabSessionHipoteseDiagnostica->class='';
        $tabSessionHipoteseDiagnostica->add('<a href="#tabHipoteseDiagnostica" data-toggle="tab">Hipótese diagnóstica</a>');
        
        $tabSessionEvolucao = new TElement('li');
        $tabSessionEvolucao->class='';
        $tabSessionEvolucao->add('<a href="#tabEvolucao" data-toggle="tab">Evolução</a>');
        
        $tabSessionPrescricao = new TElement('li');
        $tabSessionPrescricao->class='';
        $tabSessionPrescricao->add('<a href="#tabPrescricao" data-toggle="tab">Prescição</a>');
        
        $tabSessionAtestado = new TElement('li');
        $tabSessionAtestado->class='';
        $tabSessionAtestado->add('<a href="#tabAtestado" data-toggle="tab">Atestado</a>');
        
        
        $tabSessions->add($tabSessionResumo);
        
        if ($this->iniciouAtendimento == TRUE){
            $tabSessions->add($tabSessionAnamnese);
            $tabSessions->add($tabSessionExameFisico);
            $tabSessions->add($tabSessionHipoteseDiagnostica);
            $tabSessions->add($tabSessionEvolucao);
            $tabSessions->add($tabSessionPrescricao);
            $tabSessions->add($tabSessionAtestado);
        }
        $tabbable->add($tabSessions);
        
        
        // corpo dos tabs
        $tabContent = new TElement('div');
        $tabContent->class='tab-content';
        
        $this->tabPaneResumo = new TElement('div');
        $this->tabPaneResumo->class = 'tab-pane active';
        $this->tabPaneResumo->id = 'tabResumo';
        $this->tabPaneResumo->add(print_r($this->divTimeLine,true));
        
        $scrollAnamnese = new TScroll;
        $scrollAnamnese->setSize('100%',350);
        
        //$scrollAnamnese->style = '; width:100%; height:100%;';
        
        $h2 = new TElement('h2');
        $h2->add("Queixa Principal");
        $scrollAnamnese->add($h2);
        $scrollAnamnese->add($this->cns_queixa_principal);
        
        
        // corpo tab anamnese
        $tabPaneAnamnese = new TElement('div');
        $tabPaneAnamnese->class = 'tab-pane';
        $tabPaneAnamnese->id = 'tabAnamnese';
        
        $h2 = new TElement('h2');
        $h2->add("Histórico de doença atual");
        $scrollAnamnese->add($h2);
        $scrollAnamnese->add($this->cns_historico_doenca_atual);
        
        $tabPaneAnamnese->add($scrollAnamnese);
        
        // corpo tab exame físico
        $tabPaneExameFisico = new TElement('div');
        $tabPaneExameFisico->class = 'tab-pane';
        $tabPaneExameFisico->id = 'tabExameFisico';
        
        // corpo tab hipotese diagóstica
        $tabPaneHipoteseDiagnostica = new TElement('div');
        $tabPaneHipoteseDiagnostica->class = 'tab-pane';
        $tabPaneHipoteseDiagnostica->id = 'tabHipoteseDiagnostica';
        $tabPaneHipoteseDiagnostica->add('<p>Estou na Seção Hipótese diagnóstica</p>');
        
        // corpo tab evolução
        $tabPaneEvolucao = new TElement('div');
        $tabPaneEvolucao->class = 'tab-pane';
        $tabPaneEvolucao->id = 'tabEvolucao';
        $h2 = new TElement('h2');
        $h2->add("Evolução Médica");
        $tabPaneEvolucao->add($h2);
        $tabPaneEvolucao->add($this->cns_evolucao);
        
        
        
        
        // corpo tab prescrição
        $tabPanePrescricao = new TElement('div');
        $tabPanePrescricao->class = 'tab-pane';
        $tabPanePrescricao->id = 'tabPrescricao';
        $tabPanePrescricao->add('<p>Estou na Seção Prescrição</p>');
        
        // corpo tab atestado
        $tabPaneAtestado = new TElement('div');
        $tabPaneAtestado->class = 'tab-pane';
        $tabPaneAtestado->id = 'tabAtestado';
        $tabPaneAtestado->add('<p>Estou na Seção Atestado</p>');
        
        $tabContent->add($this->tabPaneResumo);

        if ($this->iniciouAtendimento == TRUE){
        $tabContent->add($tabPaneAnamnese);
            $tabContent->add($tabPaneExameFisico);
            $tabContent->add($tabPaneHipoteseDiagnostica);
            $tabContent->add($tabPaneEvolucao);
            $tabContent->add($tabPanePrescricao);
            $tabContent->add($tabPaneAtestado);
        }
        
        
        $tabbable->add($tabContent);
        
/*
 *     <svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="60px" height="60px" viewBox="0 0 200 200">
        <circle cx="100" cy="100" r="90" stroke="#42A7D9" stroke-width="20" fill="none" />
        <text  x="40" y="128" fill="#42A7D9" style="font-size:100px; font-family:Georgia, "Times New Roman", Times, serif;">10</text>
    </svg>

 */
        
        //if (!isset($this->pacienteId))
        
        $this->paciente = new Paciente($param['key']);

        //var_dump($this->paciente);
        
        $todo =  '
	<section id="cd-timeline" class="cd-container">
		<div class="cd-timeline-block">
			<div class="cd-timeline-img cd-picture">
				<img src="app/images/cd-icon-calendar.svg" alt="Picture">
			</div> <!-- cd-timeline-img -->

			<div class="cd-timeline-content">
				<h2>Anamnese</h2>
				<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Iusto, optio, dolorum provident rerum aut hic quasi placeat iure tempora laudantium ipsa ad debitis unde? Iste voluptatibus minus veritatis qui ut.</p>
                <p><br /></p>
				<h2>Queixa Principal</h2>
				<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Iusto, optio, dolorum provident rerum aut hic quasi placeat iure tempora laudantium ipsa ad debitis unde? Iste voluptatibus minus veritatis qui ut.</p>
				<a href="#0" class="cd-read-more">Read more</a>
            <span class="cd-date">Jan 14</span>
			</div> <!-- cd-timeline-content -->
		</div> <!-- cd-timeline-block -->

		<div class="cd-timeline-block">
			<div class="cd-timeline-img cd-movie">
				<img src="app/images/cd-icon-calendar.svg" alt="Calendário">
			</div> <!-- cd-timeline-img -->

			<div class="cd-timeline-content">
				<h2>Title of section 2</h2>
				<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Iusto, optio, dolorum provident rerum aut hic quasi placeat iure tempora laudantium ipsa ad debitis unde?</p>
				<a href="#0" class="cd-read-more">Read more</a>
				<span class="cd-date">Jan 18</span>
			</div> <!-- cd-timeline-content -->
		</div> <!-- cd-timeline-block -->

		<div class="cd-timeline-block">
			<div class="cd-timeline-img cd-picture">
				<img src="app/images/cd-icon-calendar.svg" alt="Calendário">
			</div> <!-- cd-timeline-img -->

			<div class="cd-timeline-content">
				<h2>Title of section 3</h2>
				<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Excepturi, obcaecati, quisquam id molestias eaque asperiores voluptatibus cupiditate error assumenda delectus odit similique earum voluptatem doloremque dolorem ipsam quae rerum quis. Odit, itaque, deserunt corporis vero ipsum nisi eius odio natus ullam provident pariatur temporibus quia eos repellat consequuntur perferendis enim amet quae quasi repudiandae sed quod veniam dolore possimus rem voluptatum eveniet eligendi quis fugiat aliquam sunt similique aut adipisci.</p>
				<a href="#0" class="cd-read-more">Read more</a>
				<span class="cd-date">Jan 24</span>
			</div> <!-- cd-timeline-content -->
		</div> <!-- cd-timeline-block -->

		<div class="cd-timeline-block">
			<div class="cd-timeline-img cd-location">
				<img src="app/images/cd-icon-calendar.svg" alt="Calendário">
			</div> <!-- cd-timeline-img -->

			<div class="cd-timeline-content">
				<h2>Title of section 4</h2>
				<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Iusto, optio, dolorum provident rerum aut hic quasi placeat iure tempora laudantium ipsa ad debitis unde? Iste voluptatibus minus veritatis qui ut.</p>
				<a href="#0" class="cd-read-more">Read more</a>
				<span class="cd-date">Feb 14</span>
			</div> <!-- cd-timeline-content -->
		</div> <!-- cd-timeline-block -->

		<div class="cd-timeline-block">
			<div class="cd-timeline-img cd-location">
				<img src="app/images/cd-icon-calendar.svg" alt="Calendário">
			</div> <!-- cd-timeline-img -->

			<div class="cd-timeline-content">
				<h2>Title of section 5</h2>
				<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Iusto, optio, dolorum provident rerum.</p>
				<a href="#0" class="cd-read-more">Read more</a>
				<span class="cd-date">Feb 18</span>
			</div> <!-- cd-timeline-content -->
		</div> <!-- cd-timeline-block -->

		<div class="cd-timeline-block">
			<div class="cd-timeline-img cd-movie">
				<img src="app/images/cd-icon-calendar.svg" alt="Calendário">
			</div> <!-- cd-timeline-img -->

			<div class="cd-timeline-content">
				<h2>Final Section</h2>
				<p>This is the content of the last section</p>
				<span class="cd-date">Feb 26</span>
			</div> <!-- cd-timeline-content -->
		</div> <!-- cd-timeline-block -->
	</section> <!-- cd-timeline -->
           ';
            //$this->divTimeLine->add($todo);
            //$scroll = new TScroll;
            //$scroll->setSize('100%',500 );
            //$scroll->add($todo);
            
            //$this->tabPaneResumo->add($scroll);
            $this->tabPaneResumo->add($todo);
            parent::add($tabbable);
            
        }
        
        
public function onIniciarAtendimento($param)
{
    
    $this->iniciouAtendimento = TRUE;
    
    $this->paciente = new Paciente($param['key']);
    
    $this->btnNovoAtendimentoDeConsulta->setImage('bs:stop white');
    $this->btnNovoAtendimentoDeConsulta->class = 'btn btn-danger';
    $this->btnNovoAtendimentoDeConsulta->popover = 'true';
    $this->btnNovoAtendimentoDeConsulta->poptitle = 'Informação';
    $this->btnNovoAtendimentoDeConsulta->popcontent = 'Encerrar esta consulta';
    $actBtnNovoAtendimentoDeConsulta = new TAction(array($this,'onEncerrarConsulta'));
    $actBtnNovoAtendimentoDeConsulta->setParameter('key', $param['key']);
    $this->btnNovoAtendimentoDeConsulta->setAction($actBtnNovoAtendimentoDeConsulta,'Encerrar consulta');
    $this->onReload($param);
}

public function onEncerrarConsulta($param)
{
    $this->iniciouAtendimento = FALSE;
    $this->onReload($param);
    
}
        
        
        
}


?>