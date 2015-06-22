<?php
//namespace control\agendas;

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
class AgendaPacienteForm extends TPage
{
    
    protected $form;
    static public $aps_pts_id;
    static public $aps_nome_paciente;
    
    public function __construct()
    {
        parent::__construct();

        //TPage::include_css('lib/jquery/fullcalendar/fullcalendar.print.css');
        TPage::include_css('lib/jquery/fullcalendar/fullcalendar.css');
        TPage::include_css('lib/jquery/jquery.qtip/jquery.qtip.min.css');
        
        TPage::include_js('lib/jquery/fullcalendar/lib/moment.min.js');
//        TPage::include_js('lib/jquery/jquery.qtip/jquery.qtip.min.js');
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
                                                    display: none;	
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
        $calendario->style='
                                                    width: 100%;
                                                    height:100%; 
                                                    margin: 0 auto;
            ';

        $eventContent = new TElement('div');
        $eventContent->id = 'eventContent';
        $eventContent->title = 'Detalhes do evento';
        $eventContent->style = 'display:none;';

        $eventContent->add('
        Start: <span id="startTime"></span><br>
        End: <span id="endTime"></span><br><br>
        <p id="eventInfo"></p>
        <p><strong><a id="eventLink" href="" target="_blank">Read More</a></strong></p>            
            
            ');
        
        $script = new TElement('script');
        $script->type = 'text/javascript';
        $script->add("

            InitializeCalendar();

            var btnLixeira = '<div id=\"lixeira\"  class=\"ui-button ui-state-default ui-corner-left ui-corner-right\" style=\"text-align:center; width:30px; height:29px;\">'+
            '  <img src=\"app/images/trash-icon2.png\"  border=0 width=24px height=24px /></div>';
            
            var btnCalendar = '<div class=\"fc-button-next ui-state-default ui-corner-left ui-corner-right\">' +
                                                        '<img src=\"app/images/calendario1.png\" id=\"date_picker\" />' +
                                                    '</div>';
            
            var btnNewScheduler =      '<div style=\"display:inline-block;\"><button class=\"btn btn-success btn-sm\" onclick=\"Adianti.waitMessage = \'Carregando\';__adianti_post_data(\'form_agendar_paciente\', \'class=AgendaPacienteForm&amp;method=onFormAgenda\');return false;\" id=\"tbutton_btnAgendar\" name=\"btnAgendar\" popover=\"true\" poptitle=\"Agendamento\" '+
                                                                                'popcontent=\"Clique aqui para agendar um paciente\" data-original-title=\"\" title=\"\"><span>'+
                                                                                '<i class=\"glyphicon glyphicon-plus white\" style=\"padding-right:4px\"></i>Agendar</span></button></div>';
            
            var existeLixeira = $('#lixeira').length;
            if (!existeLixeira)
            {
                $('.fc-toolbar .fc-left').append($('.fc-toolbar .fc-left'),btnLixeira,btnCalendar,btnNewScheduler);
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
           /* 
                $('#date_picker').on ('click',function() {
                    $('#date_picker').datepicker(\"show\");
                });
           */
            
    function InitializeCalendar(){

            var date = new Date();
            var d = date.getDate();
            var m = date.getMonth();
            var y = date.getFullYear();
            
            $('#calendar').fullCalendar({
                    
                    header: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'month,agendaWeek,agendaDay'
                    },

                    theme: true,
                    height: 500,
                    allDayDefault: false,
                    allDayText: 'dia todo',
                    aspectRatio: 1.5,
        		    axisFormat: 'H:mm',
                    buttonIcons: true,
               
                    defaultView: 'agendaDay',
                    lang: 'pt-br',
                    dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
                    dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb'],
                    defaultDate: '".date("Y-m-d")."',
                    displayEventEnd:true,
                    eventLimitText:'...',
        			editable: true,
        			eventLimit: true,
                    businessHours: {
                    start: '08:00',
                    end: '18:00',
                    dow: [ 1, 2, 3, 4,5 ],
    },
                    selectHelper : true,
                    selectable : true,
                    monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
        		    monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
            
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
            
                    editable: true,
                    draggable: false,

            eventRender: function(calEvent, element) {
                            //element.attr('href', 'javascript:void(0);');
/*                            element.qtip({
                                        content : element.description,
                                        });
            */            


                            element.click(function() {
                                    $('#startTime').html(moment(event.start).format('MMM Do h:mm A'));
                                    $('#endTime').html(moment(event.end).format('MMM Do h:mm A'));
                                    $('#eventInfo').html(event.description);
                                    $('#eventLink').attr('href', event.url);
                                    $('#eventContent').dialog({ modal: true, title: event.title, width:350});
                            });
            
                    },

                    eventDragStop: function( event, jsEvent) {
                        var trashEl = jQuery('#lixeira');
                        var ofs = trashEl.offset();
                        var x1 = ofs.left;
                        var x2 = ofs.left + trashEl.outerWidth(true);
                        var y1 = ofs.top;
                        var y2 = ofs.top + trashEl.outerHeight(true);
                        if (jsEvent.pageX >= x1 && jsEvent.pageX<= x2 && jsEvent.pageY>= y1 && jsEvent.pageY <= y2) {
                            $('#calendar').fullCalendar('removeEvents', event.id);
                        }
                    },
            eventClick: function(calEvent, jsEvent, view){
                $(this).css('border-color', 'black');
            },

            eventDrop: function(event, delta, revertFunc) {
                        if (!confirm('Confirma o reagendamento para: '+event.start.format()+'?')) {
                            revertFunc();
                        }
                    } ,         

                    eventMouseover: function(event, jsEvent, view) {
                        if (view.name !== 'agendaDay') {
                            $(jsEvent.target).attr('title', event.title);
                        }
                    },
              loading: function(bool){
                if(bool) $('.loading').show();
                else $('.loading').hide();
            },        

                events: {
                        url: 'http://consultorio.dev/agenda_get_eventos.php?data_ini=21/06/2015&data_fim=23/06/2015',
                        type: 'POST',
                        data: {
                            //custom_param1: 'something',
                            //custom_param2: 'somethingelse'
                        },
                        error: function() {
                            alert('Não foi possível obter os agendamentos!!');
                        },
                        color: 'yellow',   // a non-ajax option
                        textColor: 'black' // a non-ajax option
                    },            
            
            });
          }           
              
            ");
        
             

        
        
        $btnAgendar = new TButton('btnAgendar');
        $btnAgendar->setLabel('Novo agendamento');
        $btnAgendar->popover = 'true';
        $btnAgendar->poptitle = 'Agendamento';
        $btnAgendar->popcontent = 'Clique aqui para agendar um paciente';
        $btnAgendar->setImage('bs:calendar white');
        $btnAgendar->class = 'btn btn-success btn-sm';
        $btnAgendar->setAction(new TAction(array($this, 'onFormAgenda')),'Novo agendamento');

        $btnProximaDataDisponivel = new TButton('btnProximaDataDisponivel');
        $btnProximaDataDisponivel->setLabel('Próxima data disponível');
        $btnProximaDataDisponivel->popover = 'true';
        $btnProximaDataDisponivel->poptitle = 'Seleção de próxima data disponível';
        $btnProximaDataDisponivel->popcontent = 'Localiza a próxima data disponível de consulta <br />para o médico selecionado.';
        $btnProximaDataDisponivel->setImage('bs:next white');
        $btnProximaDataDisponivel->class = 'btn btn-info btn-sm';
        $btnProximaDataDisponivel->setAction(new TAction(array($this, 'onFormAgenda')),'Próxima data disponível');
        
        
        $container->add($eventContent);
        $container->add($divWarning);
        $container->add($divLoading);
        $container->add($divQtip);
        $container->add($calendario);
        $container->add($script);
        

        
        
        // Formulário de inclusão na agenda - será apresentado no método onFormAgenda
        $this->form = new TForm('agenda_paciente_form');
        //$this->form->style .= '; width:100%; padding:0px margin:0px;';
        
        
        $aps_med_id = new TDBCombo('aps_med_id', 'consultorio', 'Medico', 'med_id', 'med_nome');
        $aps_med_id->setSize(300);

        $aps_cms_id = new TDBCombo('aps_cms_id', 'consultorio', 'ConvenioMedico', 'cms_id', 'cms_descricao');
        $aps_cms_id->setSize(300);
        
        $aps_data_hora_agendada = new TDate('aps_data_hora_agendada');
        $aps_data_hora_agendada->setMask('dd/mm/yyyy');
        $aps_data_hora_agendada->setSize(100);
        
        $aps_pms_id = new TDBCombo('aps_pms_id','consultorio', 'ProcedimentoMedico', 'pms_id', 'pms_descricao');
        $aps_pms_id->setSize(300);
        //$aps_pms_id->setChangeAction(new TAction(array($this,'onExitNome')));
        
        self::$aps_pts_id = new TSeekButton('aps_pts_id'); 
        self::$aps_pts_id->setSize(60);
        self::$aps_pts_id->id = 'aps_pts_id';
        
        
        $obj = new PacienteSeek();
        $action = new TAction(array($obj, 'onReload'));
        self::$aps_pts_id->setAction($action);
        
        self::$aps_nome_paciente = new TEntry('aps_nome_paciente');
        self::$aps_nome_paciente->setExitAction(new TAction(array($this,'onExitNome')));
        
        $tblFormAgendarPaciente = new TTable;
        
        $row = $tblFormAgendarPaciente->addRow();
        $cellLblMedico = $row->addCell(new TLabel('Médico: '));
        $cellMedico = $row->addMultiCell($aps_med_id);
        
        $row = $tblFormAgendarPaciente->addRow();
        $cellLblDataAgenda = $row->addCell(new TLabel('Data: '));
        $cellDataAgenda = $row->addMultiCell($aps_data_hora_agendada);
        
        
        $row = $tblFormAgendarPaciente->addRow();
        $cellLblConvenio = $row->addCell(new TLabel('Convênio: '));
        $cellConvenio = $row->addMultiCell($aps_cms_id);
        
        $row = $tblFormAgendarPaciente->addRow();
        $cellLblPaciente = $row->addMultiCell(new TLabel('Paciente: '));
        $cellPaciente = $row->addMultiCell(self::$aps_pts_id,self::$aps_nome_paciente);
        
        $row = $tblFormAgendarPaciente->addRow();
        $cellLblProcedimento = $row->addMultiCell(new TLabel('Procedimento: '));
        $cellProcedimento = $row->addMultiCell($aps_pms_id);
        
        
        // create an action button (save)
        $save_button=new TButton('save');
        $save_button->setAction(new TAction(array($this, 'onSave')), _t('Save'));
        $save_button->setImage('ico_save.png');
        
        $cancel_button=new TButton('voltar');
        $cancel_button->setAction(new TAction(array($this,'onFormCancel')), _t('Cancel'));
        $cancel_button->setImage('ico_close.png');
        
        $this->form->add($tblFormAgendarPaciente);
        $this->form->setFields(array($aps_med_id,$aps_cms_id, $aps_data_hora_agendada, $aps_pms_id, self::$aps_pts_id,self::$aps_nome_paciente,$save_button,$cancel_button));
        
        $buttons = new THBox;
        $buttons->add($save_button);
        $buttons->add($cancel_button);
        
        $row = $tblFormAgendarPaciente->addRow();
        $row->class = 'tformaction';
        $cell = $row->addCell( $buttons );
        $cell->style = 'margin-bottom: 0;   text-align: left;  background-color: #f5f5f5;  border-top: 1px solid #DDD;   border-radius: 0 0 2px 2px; box-shadow: inset 0 1px 0 #fff;   display: table-cell; vertical-align: inherit;';
        $cell->colspan = 2;

        parent::add($container);
    
    
    }
    
    /**
     * Open an input dialog
     */
    public function onFormAgenda( $param )
    {
        
        
        $wndAgenda = new TWindow;
        $wndAgenda->setTitle('Agendar paciente');
        $wndAgenda->setModal(TRUE);
        $wndAgenda->style .= ';padding:0; marigin:0 auto;';
        $wndAgenda->setSize(480, 350);
        
        
        $wndAgenda->add($this->form);
        
        
        //parent::add($scriptAgendaMedica);
        parent::add($wndAgenda);
    }
    
    public function onSave()
    {
            try 
            {
                TTransaction::open('consultorio');
           
                //var_dump($this->form);
                $object = $this->form->getData('AgendaPaciente');
                //var_dump($object);
                
                $this->form->validate();
                $object->store();
                $this->form->sendData('agenda_paciente_form', $object);
                
                TTransaction::close();
                new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
            }catch (Exception $e)
            {
                new TMessage('error','ERRO: '.$e->getMessage());
                TTransaction::rollback();
            }
    }
    
    /**
     * Show the input dialog data
     */
    public function onFormOk( $param )
    {
        new TMessage('info', 'Salvar : ' . json_encode($param));
    }
    
    /**
     * Show the input dialog data
     */
    public function onFormCancel( $param )
    {
        //new TMessage('info', 'Cancelar : ' . json_encode($param));
    }    
    
    static public function onExitNome($param)
    {
        //new TMessage('info',print_r($param,true));
        //$obj = TForm::getFormByName('form_agendar_paciente_Seek');
       /*
        if (empty(self::$pts_id) AND (!empty(self::$pts_nome)))
        {
            $object = new StdClass;
            $object->pts_id         = NULL;
            TForm::sendData('agenda_paciente_form', $object);
        }
        */
    }
    

}
/*
 * /*      
            eventClick: function(calEvent, jsEvent, view) {
                $('#calendar').fullCalendar('removeEvents', function (event) {
                return event == calEvent;
                });
                },
                eventClick: function(calEvent, jsEvent, view) {
                    alert('Event: ' + calEvent.title);
                    alert('Coordinates: ' + jsEvent.pageX + ',' + jsEvent.pageY);
                    alert('View: ' + view.name);
                    // change the border color just for fun
                    $(this).css('border-color', 'red');
             },            
            eventClick: function (calEvent, jsEvent, view) {
                $.ajax({
                    url: 'calendario.php',
                    data: {'calendario_id' : event.id},
                    success: function() {
                        $('#calendar').fullCalendar('removeEvents', event.id);
            
                    }
                });
            },

*/         



?>