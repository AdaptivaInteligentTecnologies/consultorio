<?php
use Adianti\Control\TPage;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Form\TDate;
use Adianti\Widget\Form\TButton;
use Adianti\Control\TAction;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Container\TNotebook;
// use Adianti\Widget\Container\TScroll;
use Adianti\Widget\Form\THtmlEditor;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Container\TTable;
use Adianti\Registry\TSession;
use Adianti\Widget\Util\TImage;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Database\TTransaction;
use adianti\widget\dialog\TToast;
use Adianti\Database\TCriteria;
use Adianti\Database\TFilter;
// namespace control\consultas;
class FilaAtendimentoForm extends TPage
{

    private $form;

    private $loaded;

    protected $cns_data_hora_ini_consulta;

    protected $cns_data_hora_fim_consulta;

    protected $consultaId;

    protected $consulta;

    protected $paciente;

    protected $pacienteId;

    protected $divTimeLine;

    protected $btnNovoAtendimentoDeConsulta;

    protected $btnSalvarAnamnese;

    protected $actBtnSalvarAnamnese;

    protected $tabPaneResumo;

    protected $iniciouAtendimento;

    protected $cns_queixa_principal;

    protected $cns_historico_doenca_atual;

    protected $perfil;

    protected $cns_hpp_hmp;

    protected $cns_historico_familiar;

    protected $cns_pessoal_social;

    protected $cns_evolucao;

    function __construct()
    {
        parent::__construct();
        
        parent::include_css('lib/vertical-timeline/css/style.css');
        parent::include_js('lib/vertical-timeline/js/main.js');
        parent::include_js('lib/vertical-timeline/js/modernizr.js');
        
        $this->form = new TForm('form_consulta');
        $this->form->class = 'tform';
        
        // var_dump(TSession::getValue('session_atendimento_'))
        
        $this->consulta = new Consulta(TSession::getValue('session_atendimento_consulta_id'));
        $this->consultaId = $this->consulta->cns_id;
        
        $this->pacienteId = $this->consulta->cns_pts_id;
        
        $this->paciente = new Paciente($this->pacienteId);
        
        $this->btnNovoAtendimentoDeConsulta = new TButton('novoAtendimentoDeConsulta');
        $this->btnNovoAtendimentoDeConsulta->setImage('bs:play-circle white');
        $this->btnNovoAtendimentoDeConsulta->class = 'btn btn-xs btn-success';
        $this->btnNovoAtendimentoDeConsulta->popover = 'true';
        $this->btnNovoAtendimentoDeConsulta->poptitle = 'Informação';
        $this->btnNovoAtendimentoDeConsulta->popcontent = 'Iniciar novo atendimento de consulta';
        $actBtnNovoAtencimentoDeConsulta = new TAction(array(
            $this,
            'onIniciarAtendimento'
        ));
        
        // $actBtnNovoAtencimentoDeConsulta->setParameter('key', $this->pacienteId);
        $this->btnNovoAtendimentoDeConsulta->setAction($actBtnNovoAtencimentoDeConsulta, 'Iniciar Atendimento');
        
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
        $divIdadePaciente->add(TDate::getIdade($this->paciente->pts_data_nascimento));
        
        $divDataUltimaConsultaPaciente = new TElement('div');
        $divDataUltimaConsultaPaciente->class = 'data-ultima-consulta-paciente';
        $divDataUltimaConsultaPaciente->add(Consulta::getDataUltimaConsultaPorPaciente($this->pacienteId));
        
        $divNovoAtendimentoConsulta = new TElement('div');
        $divNovoAtendimentoConsulta->class = 'novo-atendimento-consulta-paciente';
        $divNovoAtendimentoConsulta->add($this->btnNovoAtendimentoDeConsulta);
        
        $divTimer = new TElement('div');
        $divTimer->class = 'timer-atendimento-consulta';
        $divTimer->id = 'divTimerAtendimentoConsulta';
        
        $divCounterTime = new TElement('div');
        $divCounterTime->class = 'counter-time';
        $divCounterTime->add('<span class="glyphicon glyphicon-time icon-counter-time"></span><span class="counterTime">00:00:00</span>');
        
        $divFormConsultaCabecalhoPaciente = new TElement('div');
        $divFormConsultaCabecalhoPaciente->class = 'form-consulta-cabecalho-paciente';
        $divCabecalho->add($divFotoPaciente);
        
        // Conduta
        $this->cns_conduta = new THtmlEditor('cns_conduta');
        $this->cns_conduta->setSize(700);
        
        // Evolução
        $this->cns_evolucao = new THtmlEditor('cns_evolucao');
        $this->cns_evolucao->setSize(700);
        // Prescrição
        // Atestado
        
        // $divNomePaciente->add('<span class="label">Detalhes</span>');
        /*
         * $divDetalhesPaciente = new TElement('div');
         * $divDetalhesPaciente->add($btnDetalhesPaciente);
         * $divDetalhesPaciente->class = 'btn-detalhes-paciente';
         */
        
        $divDadosPaciente->add($divNomePaciente);
        // $divDadosPaciente->add($divDetalhesPaciente);
        $divDadosPaciente->add($divIdadePaciente);
        $divDadosPaciente->add($divDataUltimaConsultaPaciente);
        $divDadosPaciente->add($divNovoAtendimentoConsulta);
        $divDadosPaciente->add($divCounterTime);
        
        $divCabecalho->add($divDadosPaciente);
        
        $this->form->setFields(array(
            $this->btnNovoAtendimentoDeConsulta
        ));
        // $this->btnSalvar
        
        // $btnDetalhesPaciente,
        
        parent::add($divCabecalho);
    }

    public function onReload($param)
    {
        
        // TAB
        $tabbable = new TElement('div');
        $tabbable->class = 'tabbable';
        $tabbable->style .= '; background-color:white; height:100%; min-height:300px';
        $tabbable->id = 'tabs';
        
        $tabSessions = new TElement('ul');
        $tabSessions->class = 'nav nav-tabs';
        
        // cabeçalho dos tabs
        $tabSessionResumo = new TElement('li');
        $tabSessionResumo->class = 'active';
        $tabSessionResumo->add('<a href="#tabResumo" data-toggle="tab" aria-expanded="true">Resumo</a>');
        
        $tabSessionAnamnese = new TElement('li');
        $tabSessionAnamnese->class = '';
        $tabSessionAnamnese->add('<a href="#tabAnamnese" data-toggle="tab">Anamnese</a>');
        
        $tabSessionExameFisico = new TElement('li');
        $tabSessionExameFisico->class = '';
        $tabSessionExameFisico->add('<a href="#tabExameFisico" data-toggle="tab">Exame Físico</a>');
        
        $tabSessionConduta = new TElement('li');
        $tabSessionConduta->class = '';
        $tabSessionConduta->add('<a href="#tabConduta" data-toggle="tab">Conduta</a>');
        
        $tabSessionEvolucao = new TElement('li');
        $tabSessionEvolucao->class = '';
        $tabSessionEvolucao->add('<a href="#tabEvolucao" data-toggle="tab">Evolução</a>');
        
        $tabSessionPrescricao = new TElement('li');
        $tabSessionPrescricao->class = '';
        $tabSessionPrescricao->add('<a href="#tabPrescricao" data-toggle="tab">Prescição</a>');
        
        $tabSessionAtestado = new TElement('li');
        $tabSessionAtestado->class = '';
        $tabSessionAtestado->add('<a href="#tabAtestado" data-toggle="tab">Atestado</a>');
        
        $tabSessions->add($tabSessionResumo);
        
        if ($this->iniciouAtendimento == TRUE) {
            $tabSessions->add($tabSessionAnamnese);
            $tabSessions->add($tabSessionExameFisico);
            // $tabSessions->add($tabSessionHipoteseDiagnostica);
            $tabSessions->add($tabSessionConduta);
            $tabSessions->add($tabSessionEvolucao);
            $tabSessions->add($tabSessionPrescricao);
            $tabSessions->add($tabSessionAtestado);
        }
        $tabbable->add($tabSessions);
        
        // corpo dos tabs
        $tabContent = new TElement('div');
        $tabContent->class = 'tab-content';
        
        $this->tabPaneResumo = new TElement('div');
        $this->tabPaneResumo->class = 'tab-pane active';
        $this->tabPaneResumo->id = 'tabResumo';
        $this->tabPaneResumo->add(print_r($this->divTimeLine, true));
        
        // corpo tab anamnese
        $tabPaneAnamnese = new TElement('div');
        $tabPaneAnamnese->class = 'tab-pane';
        $tabPaneAnamnese->id = 'tabAnamnese';
        $objAnamnese = new AnamneseForm();
        $tabPaneAnamnese->add($objAnamnese);
        
        // corpo tab exame físico
        $tabPaneExameFisico = new TElement('div');
        $tabPaneExameFisico->class = 'tab-pane';
        $tabPaneExameFisico->id = 'tabExameFisico';
        $objExameFisico = new ExameFisicoForm();
        $tabPaneExameFisico->add($objExameFisico);
        
        // corpo tab conduta
        $tabPaneConduta = new TElement('div');
        $tabPaneConduta->class = 'tab-pane';
        $tabPaneConduta->id = 'tabConduta';
        $objConduta = new CondutaForm();
        $tabPaneConduta->add($objConduta);
        
        // corpo tab evolução
        $tabPaneEvolucao = new TElement('div');
        $tabPaneEvolucao->class = 'tab-pane';
        $tabPaneEvolucao->id = 'tabEvolucao';
        $h2 = new TElement('h2');
        $h2->add("Evolução Médica");
        $objEvolucao = new EvolucaoForm();
        $tabPaneEvolucao->add($objEvolucao);
        
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
        
        if ($this->iniciouAtendimento == TRUE) {
            $tabContent->add($tabPaneAnamnese);
            $tabContent->add($tabPaneExameFisico);
            $tabContent->add($tabPaneHipoteseDiagnostica);
            $tabContent->add($tabPaneConduta);
            $tabContent->add($tabPaneEvolucao);
            $tabContent->add($tabPanePrescricao);
            $tabContent->add($tabPaneAtestado);
        }
        
        $tabbable->add($tabContent);
        
        /*
         * <svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="60px" height="60px" viewBox="0 0 200 200">
         * <circle cx="100" cy="100" r="90" stroke="#42A7D9" stroke-width="20" fill="none" />
         * <text x="40" y="128" fill="#42A7D9" style="font-size:100px; font-family:Georgia, "Times New Roman", Times, serif;">10</text>
         * </svg>
         *
         */
        
        $this->paciente = new Paciente($this->pacienteId);
        
        try {
            TTransaction::open('consultorio');
            $repository = new TRepository('Consulta');
            $criteria = new TCriteria();
            $criteria->add(new TFilter('cns_pts_id', '=', $this->pacienteId));
            $criteria->setProperty('order', 'cns_data_consulta');
            $criteria->setProperty('direction', 'desc');
            // $consultas = $repository->where('cns_pts_id', '=', $this->pacienteId)->load($criteria);
            $consultas = $repository->load($criteria);
            TTransaction::close();
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
        
        // var_dump($this->paciente);
        
        $sectionTimeLine = new TElement('section');
        $sectionTimeLine->id = 'cd-timeline';
        $sectionTimeLine->class = 'cd-container';
        
        foreach ($consultas as $consulta) { // LOOP
            
            $divTimeLineBlock = new TElement('div');
            $divTimeLineBlock->class = 'cd-timeline-block';
            
            $divTimeLineImg = new TElement('div');
            $divTimeLineImg->class = 'cd-timeline-img cd-picture';
            
            $imgTimeLine = new TImage('app/images/cd-icon-calendar.svg');
            $imgTimeLine->alt = 'Calendário';
            
            $divTimeLineImg->add($imgTimeLine);
            $divTimeLineBlock->add($divTimeLineImg);
            
            $divTimeLineContent = new TElement('div');
            $divTimeLineContent->class = 'cd-timeline-content';
            
            if (! empty($consulta->cns_hpp_hmp)) {
                $h2 = new TElement('h2');
                $h2->add('História médica pregressa / História patológica pregressa');
                $p = new TElement('p');
                $p->add($consulta->cns_hpp_hmp);
                $p->add('<br />');
                $divTimeLineContent->add($h2);
                $divTimeLineContent->add($p);
            }
            if (! empty($consulta->cns_hf)) {
                $h2 = new TElement('h2');
                $h2->add('Histórico familiar');
                $p = new TElement('p');
                $p->add($consulta->cns_hf);
                $p->add('<br />');
                $divTimeLineContent->add($h2);
                $divTimeLineContent->add($p);
            }
            if (! empty($consulta->cns_queixa_principal)) {
                $h2 = new TElement('h2');
                $h2->add('Queixa Principal');
                $p = new TElement('p');
                $p->add($consulta->cns_queixa_principal);
                $p->add('<br />');
                $divTimeLineContent->add($h2);
                $divTimeLineContent->add($p);
            }
            /*
             * $a = new TElement('a');
             * $a->href = '#0';
             * $a->class = 'cd-read-more';
             * $a->add('Leia mais...');
             */
            $span = new TElement('span');
            $span->class = 'cd-date';
            $span->add(TDate::format(TDate::parseDate(TDate::parseDate($consulta->cns_data_consulta)), 'd/m/Y'));
            
            $divTimeLineContent->add($a);
            $divTimeLineContent->add($span);
            $divTimeLineBlock->add($divTimeLineContent);
            $sectionTimeLine->add($divTimeLineBlock);
        }
        // END LOOP
        
        $this->tabPaneResumo->add($sectionTimeLine);
        
        parent::add($tabbable);
        // check if the datagrid is already loaded
        $this->loaded = TRUE;
    }

    public function onIniciarAtendimento($param)
    {
        TScript::create("$('.counterTime').timer({ format: '%H:%M:%S'});");
        
        TScript::create('$(".sidebar-nav").toggleClass("hide");');
        
        $this->cns_data_hora_ini_consulta = date('Y-m-d H:i');
        
        TSession::setValue(TSession::getValue('username') . 'cns_data_hora_ini_consulta', date('Y-m-d H:i'));
        // new TMessage('info',$this->cns_data_hora_ini_consulta);
        
        $this->iniciouAtendimento = TRUE;
        
        $this->btnNovoAtendimentoDeConsulta->setImage('bs:stop white');
        $this->btnNovoAtendimentoDeConsulta->class = 'btn btn-xs btn-danger';
        $this->btnNovoAtendimentoDeConsulta->popover = 'true';
        $this->btnNovoAtendimentoDeConsulta->poptitle = 'Informação';
        $this->btnNovoAtendimentoDeConsulta->popcontent = 'Encerrar esta consulta';
        
        $actBtnNovoAtendimentoDeConsulta = new TAction(array(
            $this,
            'onEncerrarConsulta'
        ));
        
        $actBtnNovoAtendimentoDeConsulta->setParameter('key', $param['key']);
        $this->btnNovoAtendimentoDeConsulta->setAction($actBtnNovoAtendimentoDeConsulta, 'Encerrar consulta');
        
        $this->onReload(func_get_arg(0));
    }

    public function onEncerrarConsulta($param)
    {
        try {
            
            TTransaction::open('consultorio');
            
            // new TMessage('info', $this->cns_data_hora_ini_consulta);
            
            $this->consulta->cns_data_hora_ini_consulta = TSession::getValue(TSession::getValue('username') . 'cns_data_hora_ini_consulta');
            $this->consulta->cns_data_hora_fim_consulta = date('Y-m-d H:i');
            $this->consulta->cns_encerrada = 'S';
            $this->consulta->store();
            
            TTransaction::close();
            
            new TToast('Consulta encerrada com sucesso!');
            
            $this->iniciouAtendimento = FALSE;
            TScript::create('$(".sidebar-nav").toggleClass("hide");');
            $this->onReload(func_get_arg(0));
        } catch (Exception $e) {
            new TMessage('info', 'Erro inesperado ao encerrar a consulta. ERRO: ' . $e->getMessage());
        }
    }

    public function onShow()
    {
        // check if the datagrid is already loaded
        if (! $this->loaded) {
            $this->onReload(func_get_arg(0));
        }
        parent::show();
    }
}

?>