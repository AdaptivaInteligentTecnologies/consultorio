<?php
//namespace control\agendas;


/*
 * .fc-content:before {
  content: "\ C ";
  text-indent: -10px;
  background-color: red;
}
 */

use Adianti\Control\TPage;
use Adianti\Widget\Container\TTable;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Base\TStyle;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Container\THBox;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TCombo;
use Adianti\Widget\Util\TImage;
use Adianti\Widget\Util\TCalendar;
use Adianti\Widget\Wrapper\TQuickForm;
use Adianti\Widget\Wrapper\TDBCombo;
use Adianti\Widget\Wrapper\TDBSeekButton;
use Adianti\Widget\Form\TButton;
use Adianti\Control\TAction;
use Adianti\Control\TWindow;
use Adianti\Widget\Dialog\TInputDialog;
use Adianti\Widget\Form\TSeekButton;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Form\TDate;
use Adianti\Widget\Wrapper\TDBCheckGroup;
use Adianti\Database\TTransaction;
use adianti\widget\dialog\TToast;
use Adianti\Widget\Wrapper\TDBEntry;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Dialog\TQuestion;

class AgendaPacienteForm extends TPage
{
    
    private $form;
    static public $aps_pts_id;
    static public $aps_nome_paciente;
    protected $itemsStatus = array("A"=>"Agendado","C"=>"Cancelado", "E"=>"Encerrada");
    protected $new_button;
    protected $incluirFilaAtendimento_button;
    protected $actCadastrarPaciente;    
    
    public function __construct()
    {
        parent::__construct();
       

        //TPage::include_css('lib/jquery/fullcalendar/fullcalendar.print.css');
        TPage::include_css('lib/jquery/fullcalendar/fullcalendar.css');
        
        TPage::include_css('lib/jquery/jquery.qtip/jquery.qtip.min.css');
        
        TPage::include_js('lib/jquery/fullcalendar/lib/moment.min.js');
        TPage::include_js('lib/jquery/jquery.qtip/jquery.qtip.min.js');
        TPage::include_js('lib/jquery/fullcalendar/fullcalendar.min.js');
        TPage::include_js('lib/jquery/fullcalendar/lang-all.js');
        
        $container = new TVBox() ;
        $container->style = 'width:100%;height:100%';
        
        // fullcalendar
       
        $divLoading = new TElement('div');
        $divLoading->id = 'loading';
        $divLoading->add('loading...');
        $divLoading->style="display: none;position: absolute;top: 10px;right: 10px;";
        
        
        $divQtip = new TElement('div');
        $divQtip->class='qtip qtip-stylename';
        $divQtip->add("
        <div class='qtip-tip' rel='cornerValue'></div>
        <div class='qtip-wrapper'>
        <div class='qtip-borderTop'></div>
        <div class='qtip-contentWrapper'>
        <div class='qtip-title'>
        <div class='qtip-button'></div>
        </div>
        <div class='qtip-content'></div>
        </div>
        <div class='qtip-borderBottom'></div>
            ");
        
        /*
        <div class="qtip qtip-stylename">
        <div class="qtip-tip" rel="cornerValue"></div>
        <div class="qtip-wrapper">
        <div class="qtip-borderTop"></div> // Only present when using rounded corners
        <div class="qtip-contentWrapper">
        <div class="qtip-title"> // All CSS styles...
        <div class="qtip-button"></div> // ...are usually applied...
        </div>
        <div class="qtip-content"></div> // ...to these three elements!
        </div>
        <div class="qtip-borderBottom"></div> // Only present when using rounded corners
        </div>
        </div>
        */
        
        $divWarning = new TElement('div');
        $divWarning->id = 'script-warning';
        $divWarning->style='
                                                    /*display: none;*/	
                                                    background: #eee;		
                                                    border-bottom: 1px solid #ddd;		
                                                    padding: 0 10px;		
                                                    line-height: 40px;		
                                                    text-align: center;		
                                                    font-weight: bold;
		                                            font-size: 12px;
		                                            color: red;
            ';
        
        $calendario = new TElement('div');
        $calendario->id = 'calendar';
        $calendario->class='fc fc-ltr fc-themed';
        
        $eventContent = new TElement('div');
        $eventContent->id = 'eventContent';
        $eventContent->title = 'Detalhes do evento';
        $eventContent->style = 'display:none;';

        $eventContent->add('
        Data/Hora INÍCIO: <span id="startTime"></span><br>
        Data/Hora FIM: <span id="endTime"></span><br><br>
        <p id="eventInfo"></p>
        <p><strong><a id="eventLink" href="" target="_blank">Saber Mais</a></strong></p>            
            
            ');
        
        $script = new TElement('script');
        $script->type = 'text/javascript';
        $script->add("

            InitializeCalendar();

            var btnLixeira       =  '<div id=\"lixeira\"  class=\"ui-button ui-state-default ui-corner-left ui-corner-right\" style=\"text-align:center; width:30px; height:29px;\">'+
                                    '  <img src=\"app/images/trash-icon2.png\"  border=0 width=24px height=24px popover=\"true\" poptitle=\"Excluir agendamento\" popcontent=\"Clique, segure e arraste para cima desta lixeira para excluir o agendamento desejado\"/></div>';

            var btnSearch        =  '<div '+
                                    ' class=\"ui-button ui-state-default ui-corner-left ui-corner-right\" '+
                                    ' style=\"text-align:center; width:30px; height:29px;\"> '+
                                    ' <img src=\"app/images/lupa4.png\" id=\"localizarAgendamento\" popover=\"true\" poptitle=\"Localizar Paciente Agendado\" '+
                                    ' popcontent=\"Abre janela para localização de paciente agenda\" '+
                                    ' onclick=\"Adianti.waitMessage = \'Carregando\';__adianti_post_data(\'form_agendar_paciente\', \'class=LocalizarEvento&method=onSearch\');return false;\"/>' +            
                                    '</div>';
            
            
            var btnNewScheduler  =  '<div style=\"display:inline-block;\"><button class=\"btn btn-success btn-sm\" onclick=\"Adianti.waitMessage = \'Carregando\';__adianti_post_data(\'form_agendar_paciente\', \'class=AgendaPacienteForm&amp;method=onFormAgenda\');return false;\" id=\"tbutton_btnAgendar\" name=\"btnAgendar\" popover=\"true\" poptitle=\"Agendamento\" '+
                                                                                'popcontent=\"Clique aqui para agendar um paciente\" data-original-title=\"\" title=\"\"><span>'+
                                                                                '<i class=\"glyphicon glyphicon-plus white\" style=\"padding-right:4px\"></i>Agendar</span></button></div>';
            
            
            var existeLixeira = $('#lixeira').length;
            
            if (!existeLixeira)
            {
                    $('.fc-toolbar .fc-left').append($('.fc-toolbar .fc-left'),btnLixeira,btnNewScheduler, btnSearch);
            }

            $(function(){;
                   $('#date_picker').datepicker({
                        inline:true,
                        autoSize:true,
                        language:'pt-BR',
                        changeMonth: true,
                        changeYear: true,
                        onSelect: function(dateStr) {
                                var date = $(this).datepicker('getDate');
                                var today = new Date();
                                var days = (date.getTime() - today.getTime()) / (24 * 60 * 60 * 1000);
                                alert(today);
                                return;
                        }
                    });
            });            
            
            function InitializeCalendar(){
        
                    var date = new Date();
                    var d = date.getDate();
                    var m = date.getMonth();
                    var y = date.getFullYear();
                    
                    toastr.options = {
                                              'closeButton': true,
                                              'debug': false,
                                              'newestOnTop': true,
                                              'progressBar': true,
                                              'positionClass': 'toast-top-center',
                                              'preventDuplicates': false,
                                              'showDuration': '300',
                                              'hideDuration': '1000',
                                              'timeOut': '2000',
                                              'extendedTimeOut': '1000',
                                              'showEasing': 'swing',
                                              'hideEasing': 'linear',
                                              'showMethod': 'fadeIn',
                                              'hideMethod': 'fadeOut'
                                    }
            
                    $('#calendar').fullCalendar({
                            
                            header: {
                                left: 'prev,next today',
                                center: 'title',
                                right: 'month,agendaWeek,agendaDay'
                            },

                            theme: true,
                            height: 500,
                            
        
                            allDay: false,
                            allDaySlot: false,
                            aspectRatio: 1.5,
                		    axisFormat: 'HH:mm',
        
                            buttonIcons: true,
                            businessHours: {
                                start: '06:00',
                                end: '19:00',
                                dow: [ 1, 2, 3, 4, 5 ],
                            },
                            buttonText: {
                        			prev: ' ◄ ',
                        			next: ' ► ',
                        			prevYear: ' << ',
                        			nextYear: ' >> ',
                        			today: 'hoje',
                        			month: 'mês',
                        			week: 'semana',
                        			day: 'dia'
                		     },		
            
            
                            defaultView: 'agendaDay',
                            dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
                            dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb'],
                            defaultDate: '".date("Y-m-d")."',
                            displayEventEnd:true,
                            
                            dropable: true,
                            draggable: false,
                    
                            editable: true,
                            eventLimitText:'...',
                			editable: true,
                			eventLimit: true,
        
                            lang: 'pt-br',
                            
                            minutes: '30',
                            minTime: '06:00:00',
                            maxTime: '23:00:00',
                    
                            monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
                		    monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
                            
                            selectHelper : true,
                            selectable : true,
                            slotDuration: '00:30:01',
                            snapDuration: '00:30:01',
            

                            eventDragStop: function( event, jsEvent) {
                
                                        var trashEl = jQuery('#lixeira');
                                        var ofs = trashEl.offset();
                                        var x1 = ofs.left;
                                        var x2 = ofs.left + trashEl.outerWidth(true);
                                        var y1 = ofs.top;
                                        var y2 = ofs.top + trashEl.outerHeight(true);
                                        if (jsEvent.pageX >= x1 && jsEvent.pageX<= x2 && jsEvent.pageY>= y1 && jsEvent.pageY <= y2) 
                                        {
                                         $.ajax({
                                                 url: 'Agenda.php',
                                                data: {
                                                    type: 'removeEvent',
                                                    eventId: event.id,
                                                 },            
                                                 type: 'POST',
                                                 dataType: 'json',
                                                 success: function(response){
                                                   if(response.status == 'success'){
                                                    jQuery('#calendar').fullCalendar('removeEvents', event.id);
                                                        toastr['success']('Evento excluído<br />', 'Sucesso');
                                        
                                                   }
                                                 },
                                                 error: function(e){
                                                    alert('Erro ao processar requisição: '+e.responseText+' ERRO:'+e.error);
                                                 }
                                               });            
                                        }
                            },
            
            
                            eventRender: function(calEvent, element, view) {
                                    
                                    
            
                                   element.click(function() {
                
                                                /*
                                                    $('#startTime').html(moment(event.start).format('MMM Do h:mm A'));
                                                    $('#endTime').html(moment(event.end).format('MMM Do h:mm A'));
                                                    $('#eventInfo').html(event.description);
                                                    $('#eventLink').attr('href', event.url);
                                                    $('#eventContent').dialog({ modal: true, title: event.title, width:350});
                                                */

                                                Adianti.waitMessage = 'Carregando';
                                                __adianti_post_data('form_agendar_paciente', 'class=AgendaPacienteForm&method=onFormAgenda&key='+calEvent.id);
                                                return false; 
                            
                                   });
            
                                                            
            
                                   if (calEvent.id == 1) {
                                        /*
                                        for (propertie in calEvent)
                                        {
                                            console.log( propertie + \": \" + element[propertie] );
                                            //calEvent.className = \".fc-time\";
                                        }
                                        */
                                        //return $(\"<div style='background:green'>\").text(calEvent.title);
				                   }
            
                            
                            
                            },

                            eventDrop: function(event, delta, revertFunc) {
                                       var title = event.title;
                                       var start = event.start.format();
                                       var end = (event.end == null) ? start : event.end.format();
                                       $.ajax({
                                         url: 'Agenda.php',
                                                data: {
                                                    type: 'alterEvent',
                                                    eventId: event.id,
                                                    title: title,
                                                    start: start,
                                                    end: end,
                                                 },               
                                         type: 'POST',
                                         dataType: 'json',
                                         success: function(response){
                                           if(response.status != 'success'){
                                             revertFunc();
                                             toastr['error']('Erro ao remanejar evento!<br />', 'Erro');
                                            }
                                            $('.fc-content:before').css('content: \" C \";  text-indent: -10px;  background-color: red;');
                                            $('.fc-content').css('background-color:red');
                            
                                           
                                         },
                                         error: function(e){
                                           revertFunc();
                                           alert('Erro ao processar requisição: '+e.responseText+' ERRO:'+e.error);
                                         }
                                         });
                            } ,
                
                            eventMouseover: function(event, jsEvent, view) {
                                if (view.name !== 'agendaDay') {
                                    $(jsEvent.target).attr('title', event.title);
                                }
                            },
            
                            loading: function(bool){
                                if(bool) $('.loading').show();
                                else $('.loading').hide();
                            }, // end loading        

                            events: {
                                    url: 'Agenda.php',
                                    type: 'POST',
                                    async: false,
                                    dataType: 'json',            
                                    data: {
                                        type: 'retrieveAll',
                                        eventId: 0,
                                    },
                                    error: function() {
                                        alert('Não foi possível obter os agendamentos!!');
                                    },
                                    
                                }, // end events            
            
                    }); // end fullcalendar
             }// end InitializeCalendar
              
            ");
        
        $btnAgendar = new TButton('btnAgendar');
        $btnAgendar->setLabel('Novo agendamento');
        $btnAgendar->popover = 'true';
        $btnAgendar->poptitle = 'Agendamento';
        $btnAgendar->popcontent = 'Clique aqui para agendar um paciente';
        $btnAgendar->setImage('bs:calendar white');
        $btnAgendar->class = 'btn btn-success btn-sm';
        $btnAgendar->setAction(new TAction(array($this, 'onFormAgenda')),'Novo agendamento');

        /*
        $btnProximaDataDisponivel = new TButton('btnProximaDataDisponivel');
        $btnProximaDataDisponivel->setLabel('Próxima data disponível');
        $btnProximaDataDisponivel->popover = 'true';
        $btnProximaDataDisponivel->poptitle = 'Seleção de próxima data disponível';
        $btnProximaDataDisponivel->popcontent = 'Localiza a próxima data disponível de consulta <br />para o médico selecionado.';
        $btnProximaDataDisponivel->setImage('bs:next white');
        $btnProximaDataDisponivel->class = 'btn btn-info btn-sm';
        $btnProximaDataDisponivel->setAction(new TAction(array($this, 'onFormAgenda')),'Próxima data disponível');
        */
        
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($eventContent);
        $container->add($calendario);
        $container->add($script);
        
        
        // Formulário de inclusão na agenda - será apresentado no método onFormAgenda
        $this->form = new TForm('agenda_paciente_form');
        $this->form->class = 'tform'; // CSS class
        $this->form->style = '';
       
        $aps_pfs_id = new TDBCombo('aps_pfs_id', 'consultorio', 'Profissional', 'pfs_id', 'pfs_nome');
        $aps_pfs_id->setSize(300);
        $aps_pfs_id->setFirstSelected(TRUE);

        $aps_id = new TEntry('aps_id');
        $aps_id->setEditable(FALSE);
        $aps_id->setSize(100);
        
        $aps_cps_id = new TDBCombo('aps_cps_id', 'consultorio', 'ConvenioProfissional', 'cps_id', 'cps_descricao');
        $aps_cps_id->setSize(300);
        
        $aps_data_agendada = new TDate('aps_data_agendada');
        $aps_data_agendada->setMask('dd/mm/yyyy');
        $aps_data_agendada->setSize(90);
        
        $aps_hora_agendada = new TEntry('aps_hora_agendada');
        $aps_hora_agendada->setMask('99:99');
        $aps_hora_agendada->setSize(70);
        
        $aps_pms_id = new TDBCombo('aps_pms_id','consultorio', 'ProcedimentoProfissional', 'pms_id', 'pms_descricao');
        $aps_pms_id->setSize(300);
        //$aps_pms_id->setChangeAction(new TAction(array($this,'onExitNome')));
        
        self::$aps_pts_id = new TSeekButton('aps_pts_id'); 
        self::$aps_pts_id->setSize(60);
        self::$aps_pts_id->id = 'aps_pts_id';
        
        
        $obj = new PacienteSeek();
        $action = new TAction(array($obj, 'onReload'));
        self::$aps_pts_id->setAction($action);
        
        self::$aps_nome_paciente = new TEntry('aps_nome_paciente');
        //self::$aps_nome_paciente->setExitAction(new TAction(array($this,'onExitNome')));
        
        $aps_data_nascimento = new TDate('aps_data_nascimento');
        $aps_data_nascimento->setMask('dd/mm/yyyy');
        $aps_data_nascimento->setSize(90);
        
        //$this->itemsStatus = array("A"=>"Agendado","C"=>"Cancelado", "E"=>"Encerrado");
        $aps_status = new TCombo('aps_status');
        //$aps_status->setDefaultOption('selected');
        $aps_status->addItems($this->itemsStatus);
        $aps_status->setSize(100);
        
        $items = array("S"=>"Sim","N"=>"Não");
        $aps_confirmado = new TCombo('aps_confirmado');
        $aps_confirmado->addItems($items);
        $aps_confirmado->setSize(70);
        
        
        $aps_telefone_contato1 = new TEntry('aps_telefone_contato1');
        $aps_telefone_contato1->setSize(100);
        
        $aps_telefone_contato2 = new TEntry('aps_telefone_contato2');
        $aps_telefone_contato2->setSize(100);
        
        // INSERT'S TABLE
        
        $tblFormAgendarPaciente = new TTable;
        $tblFormAgendarPaciente-> width = '100%';
        $this->form->add($tblFormAgendarPaciente);
        
        // add a row for the form title
        $row = $tblFormAgendarPaciente->addRow();
        $row->class = 'tformtitle'; // CSS class
        $row->addCell( new TLabel('Agendar') )->colspan = 2;
        
        
        $row = $tblFormAgendarPaciente->addRow();
        $cellLblId = $row->addCell(new TLabel('ID: '));
        $cellId = $row->addMultiCell($aps_id);
        
        $row = $tblFormAgendarPaciente->addRow();
        $cellLblProfissional = $row->addCell(new TLabel('Médico: '));
        $cellProfissional = $row->addMultiCell($aps_pfs_id);
        
        $row = $tblFormAgendarPaciente->addRow();
        $cellLblDataAgenda = $row->addCell(new TLabel('Data agendamento: '));
        $cellDataAgenda = $row->addMultiCell($aps_data_agendada, new TLabel('Hora:'),$aps_hora_agendada);
        
        
        $row = $tblFormAgendarPaciente->addRow();
        $cellLblConvenio = $row->addCell(new TLabel('Convênio: '));
        $cellConvenio = $row->addMultiCell($aps_cps_id);
        
        $row = $tblFormAgendarPaciente->addRow();
        $cellLblPaciente = $row->addMultiCell(new TLabel('Paciente: '));
        $cellPaciente = $row->addMultiCell(self::$aps_pts_id,self::$aps_nome_paciente);
        
        $row = $tblFormAgendarPaciente->addRow();
        $cellLblPaciente = $row->addMultiCell(new TLabel('Data Nascimento: '));
        $cellPaciente = $row->addMultiCell($aps_data_nascimento);
        
        $row = $tblFormAgendarPaciente->addRow();
        $cellLblProcedimento = $row->addMultiCell(new TLabel('Procedimento: '));
        $cellProcedimento = $row->addMultiCell($aps_pms_id);

        $row = $tblFormAgendarPaciente->addRow();
        $cellLblTelefoneContato = $row->addMultiCell(new TLabel('Telefone 1: '));
        $cellTelefoneContato = $row->addMultiCell($aps_telefone_contato1,new TLabel('Telefone 2:'),$aps_telefone_contato2);
        
        $row = $tblFormAgendarPaciente->addRow();
        $cellLblStatusConfirmado = $row->addMultiCell(new TLabel('Status: '));
        $cellStatusConfirmado = $row->addMultiCell($aps_status, 'Confirmado: ',$aps_confirmado);
        
        // create an action button (save)
        $save_button=new TButton('save');
        $save_button->setAction(new TAction(array($this, 'onSave')), _t('Save'));
        $save_button->setImage('ico_save.png');
        
        $close_button=new TButton('close');
        $close_button->setAction(new TAction(array($this,'onFormClose')), _t('Close'));
        $close_button->setImage('ico_close.png');

        $this->incluirFilaAtendimento_button=new TButton('incluirFilaAtendimento_button'); 
        $actIncluirFilaAtendimento_button = new TAction(array($this,'onIncluirFilaAtendimento'));
        $this->incluirFilaAtendimento_button->setAction($actIncluirFilaAtendimento_button,'Incluir na fila de atendimento');
        $this->incluirFilaAtendimento_button->setImage('ico_add.png');
        
        $this->new_button=new TButton('new');
        $this->actCadastrarPaciente = new TAction(array('PacienteForm','onCadastroPaciente'));
        $this->new_button->setAction($this->actCadastrarPaciente, 'Cadastro de paciente');
        $this->new_button->setImage('ico_new.png');
        
        $this->form->add($tblFormAgendarPaciente);
        
        $this->form->setFields(
                                array(
                                        $aps_id,
                                        $aps_pfs_id,
                                        $aps_cps_id, 
                                        $aps_data_agendada,
                                        $aps_hora_agendada, 
                                        $aps_pms_id, 
                                        self::$aps_pts_id,
                                        self::$aps_nome_paciente,
                                        $aps_data_nascimento, 
                                        $aps_status,
                                        $aps_confirmado,
                                        $aps_telefone_contato1,
                                        $aps_telefone_contato2,
                                        $save_button,
                                        $close_button,
                                        $this->new_button,
                                        $this->incluirFilaAtendimento_button,
                                )
                              );
        
        
        $buttons = new THBox;
        $buttons->add($save_button);
        $buttons->add($this->new_button);
        $buttons->add($this->incluirFilaAtendimento_button);
        $buttons->add($close_button);
        
        $row = $tblFormAgendarPaciente->addRow();
        $row->class = 'tformaction';
        $cell = $row->addCell( $buttons );
        $cell->style = 'margin-bottom: 0;   text-align: left;  background-color: #f5f5f5;  border-top: 1px solid #DDD;   border-radius: 0 0 2px 2px; box-shadow: inset 0 1px 0 #fff;   display: table-cell; vertical-align: inherit;';
        $cell->colspan = 2;

        parent::add($container);
    
    
    }
    
    public function onFormAgenda( $param )
    {
        
        $wndAgenda = new TWindow;
        $wndAgenda->setTitle('Agendar paciente');
        $wndAgenda->setModal(TRUE);
        $wndAgenda->setSize(630, 510);
        
        $wndAgenda->add($this->form);

        parent::add($wndAgenda);
        
        try
        {
            if (isset($param['key']))
            {
                $this->new_button->style = 'display:visible;';
                $this->incluirFilaAtendimento_button->style = 'display:visible;';
                $key=$param['key'];
                
                TTransaction::open('consultorio');
                $object = new AgendaPaciente($key);
                
                if (!empty($object->aps_pts_id))
                {
                    $objPaciente = new Paciente($object->aps_pts_id);
                    $object->aps_pts_id          = $objPaciente->pts_id;
                    $object->aps_nome_paciente   = $objPaciente->pts_nome;
                    $object->aps_data_nascimento = TDate::format(TDate::parseDate($objPaciente->pts_data_nascimento),'d/m/Y');
                }
                else
                {
                    $object->aps_data_nascimento    = TDate::format(TDate::parseDate($object->aps_data_nascimento),'d/m/Y');
                } 

                $object->aps_data_agendada          = TDate::format(TDate::parseDate($object->aps_data_agendada),'d/m/Y');
                $object->aps_hora_agendada          = date("H:i",strtotime($object->aps_hora_agendada));
                $this->form->setData($object);
                TTransaction::close();
            }
            else
            {
                $this->new_button->style = 'display:none;';
                $this->incluirFilaAtendimento_button->style = 'display:none;';
                $this->form->clear();
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    public function onSave()
    {
            try 
            {
                TTransaction::open('consultorio');

                $object = $this->form->getData('AgendaPaciente');

                if (empty($object->aps_data_cadastro))
                {
                    $object->aps_data_cadastro = date("d/m/Y");
                }
                if (empty($object->aps_status))
                {
                    $object->aps_status = 'A';
                }
                if (empty($object->aps_confirmado))
                {
                    $object->aps_confirmado = 'N';
                }
                
                $this->form->validate();

                $object->store();

                TTransaction::close();
                
                new TToast("Paciente ".strtolower($this->itemsStatus[$object->aps_status])." com sucesso",'success','Sucesso',2000);
                
            }catch (Exception $e)
            {
                $this->form->setData($object); // keep form data
                new TToast('Erro ao agendar paciente','error','Erro');
                new TMessage('error','ERRO: '.$e->getMessage());
                TTransaction::rollback();
            }
    }

    public function onReload( $param ){}

    public function onFormClose( $param )
    {
        parent::close(); 
    }
    
    public function onIncluirFilaAtendimento($param)
    {
       
        if (empty($param['aps_pts_id'])){
            new TMessage('info',"Para incluir o paciente na fila de espera é necessário que este já esteja cadastrado no sistema.<br />
                                 O paciente {$param['aps_nome_paciente']} agendado para {$param['aps_data_agendada']} parece não estar cadastrado.<br />
                                 Cadastre-o !!
                          ");
            return;
        }
        
        if ($param['aps_data_agendada'] != date("d/m/Y")){
            new TMessage('info',"Paciente {$param['aps_nome_paciente']} não agendado para hoje! Por favor verifique!");
            return;
        }
        
        
        if ( strtoupper($param['aps_status'] ) != 'A'){
            new TMessage('info',"Não é possível incluir {$param['aps_nome_paciente']} na fila de atendimento. O Status deste agendamento está {$this->itemsStatus[$param['aps_status']]}! Por favor verifique!");
            return;
        }
        
        try 
        {
            TTransaction::open('consultorio');
            
            $objConsulta = new Consulta();
            $objConsulta->cns_pts_id = $param['aps_pts_id']; // id do paciente
            $objConsulta->cns_pfs_id = $param['aps_pfs_id']; //id do profissional
            $objConsulta->cns_pms_id = $param['aps_pms_id']; // id co procedimento profissional
            $objConsulta->cns_cps_id = $param['aps_cps_id']; // id co procedimento profissional

            $objConsulta->cns_encerrada = 'N'; // se está encerrado ou não
            $objConsulta->cns_data_consulta = date("Y-m-d");
            $objConsulta->cns_data_hora_chegada = date("Y-m-d H:i");
            //$objConsulta->cns_tipo_desconto  = $param['aps_tipo_desconto'];
            //$objConsulta->cns_total_desconto = $param['aps_valor_desconto'];
            //$objConsulta->cns_valor = 0; // valor da consulta - originado da tabela de valores
            //$objConsulta->cns_valor_cobrado = 0; // valor real cobrado visto que existem as possibilidades de isenção ou desconto
            $objConsulta->store();
            TTransaction::close();
            new TToast("Paciente {$param['aps_nome_paciente']} incluído na fila de atendimento com sucesso!");    
        } catch (Exception $e) {
            new TMessage('error', "Erro ao incluir paciente na fila de atendimento.<br />ERRO: {$e->getMessage()}");
            TTransaction::rollback();
        }
        
    }
    
}