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
class AgendaPacienteForm extends TPage
{
    
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
                var btnCalendar = '<div class=\"fc-button-next ui-state-default ui-corner-left ui-corner-right\">' +
                                                        '<img src=\"app/images/calendario1.png\" id=\"date_picker\" />' +
                                                    '</div>';
            
            var btnNewScheduler =      '<div style=\"display:inline-block;\"><button class=\"btn btn-success btn-sm\" onclick=\"Adianti.waitMessage = \'Carregando\';__adianti_post_data(\'form_agendar_paciente\', \'class=AgendaPacienteForm&amp;method=onFormAgenda\');return false;\" id=\"tbutton_btnAgendar\" name=\"btnAgendar\" popover=\"true\" poptitle=\"Agendamento\" '+
                                                                                'popcontent=\"Clique aqui para agendar um paciente\" data-original-title=\"\" title=\"\"><span>'+
                                                                                '<i class=\"glyphicon glyphicon-plus white\" style=\"padding-right:4px\"></i>Agendar</span></button></div>';
            
            $('.fc-toolbar .fc-left').append(\"<div id='lixeira'  class='ui-button ui-state-default ui-corner-left ui-corner-right' style=' text-align:center; width:30px; height:29px; '>  <img src='app/images/trash-icon2.png' border=0 width=24px height=24px></div>\");
            $('.fc-toolbar .fc-left').append(btnCalendar);
            $('.fc-toolbar .fc-left').append(btnNewScheduler);
            
           $('#date_picker').datepicker({
                dateFormat: 'dd-mm-yy',
                dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
                dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
                dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
                monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
                monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
                nextText: 'Próximo',
                prevText: 'Anterior',            
                changeMonth: true,
                changeYear: true,
                onSelect: function (date, inst) {
                                alert('select!');
            
                    if (inst.input) {
                    alert('select!');
                    inst.input.trigger('change');
                    };
      }
    });
            
        /* 
        $('#date_picker').on ('click',function() {
            $('#date_picker').datepicker(\"show\");
        });
            */
     //$('#datePickerImage').datepicker($.datepicker.regional['pt-BR']);

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
                            element.qtip({
                                        content : element.description,
                                        });            


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
            
                    events: [
                                      {
                                                id: 1,
                            					title: 'Nome ',
                            					start: '2015-06-17',
                                                allDay:true,
                                                //url: 'http://google.com/',
                                                color: 'red',
                                                description: 'Nome',
                                      },
                                      {
                                                id:2,
                            					title: 'Nome do fulano do paciente',
                            					start: '2015-06-07T10:30:00',
                                            	end:   '2015-06-07T11:30:00',
                                                //url: 'http://google.com/',
                                                color: 'red',
                                                description: 'Nome do fulano do paciente',
                                       },
                                       {
                                                id: 3,
                            					title: 'TESTE TESTE TESTE',
                            					start: '2015-06-17T13:30:00',
                                            	end: '2015-06-17T14:30:00',
                                                //url: 'http://google.com/',
                                                color: 'orange',
                                                textColor:'white',
                                                allDay:false,
                                                borderColor: '#5173DA',
                                               description: 'TESTE TESTE TESTE TESTE',
                                       }, 
                    ],
            });
          }           
              
            ");
        
             
        //$qform = new TForm('form_agendar_paciente');
       // $qform->class='tform';

/*        
        $cmbData = new TDate('cmbData');
        $cmbData->setMask('dd/mm/yyyy');
        $cmbData->setValue(date('d/m/Y'));
        $cmbData->setSize(90);
*/    
        
        
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
        
        
/*        
        $tableToolBar       = new TTable('tabelaToolBar');
        $tableToolBar->border = 0;
        
        $tableToolBar->style = 'width:100%;';
        
        $con_id                  = new TCombo('con_id');
        $con_id->setSize(200);
        $con_id->addItems(Consultorio::getConsultorios());
        
        $med_id                  = new TCombo('med_id');
        $med_id->setSize(200);
        $med_id->addItems(Medico::getMedicos());
        
        $row = $tableToolBar->addRow();

        $cellLabelConsultorio = $row->addCell('Consultório:');
        $cellLabelConsultorio->align = 'right';
        
        $cellConsultorio= $row->addMultiCell($con_id,'Médico: ',$med_id,$btnProximaDataDisponivel);
        
        $cellConsultorio->width = '100%;';
        $cellConsultorio->align = 'left';
        $cellConsultorio->colspan = 2;
        
        $qform->setFields(array($con_id,$med_id,$btnProximaDataDisponivel));
        $qform->style = 'width: 100%';
        $qform->add($tableToolBar);
        $container->add($qform);
        */
        
        $container->add($eventContent);
        $container->add($divWarning);
        $container->add($divLoading);
        $container->add($divQtip);
        $container->add($calendario);
        $container->add($script);
        
        parent::add($container);
    
    
    }
    
    /**
     * Open an input dialog
     */
    public function onFormAgenda( $param )
    {

        
        
        $form = new TForm('agenda_paciente_form');
        $form->style = 'padding:20px';
    
        $medId = new TDBCombo('medId', 'consultorio', 'Medico', 'med_id', 'med_nome');
        $medId->setSize(300);
        
        $cmsId = new TDBCombo('conId', 'consultorio', 'ConvenioMedico', 'cps_id', 'cps_descricao');
        $cmsId->setSize(300);
        
        $ptsId = new TDBSeekButton('ptsId', 'consultorio', 'agenda_paciente_form', 'Paciente', 'pts_nome', 'ptsId', 'ptsNome') ;
        $ptsId->setSize(60);
        $ptsNome = new TEntry('ptsNome');
        
/*
 *         $btnLocalizapaciente = new TButton('btnLocalizaPaciente');
            $btnLocalizapaciente->setAction(new TAction(array($this,'onFormOk'),'Localizar'));
            $btnLocalizapaciente->setImage('ico_save.png');
 */        

        
        $tabFormPaciente = new TTable;
        $tabFormPaciente->addRowSet(new TLabel('Médico: '),$medId,'');
        $tabFormPaciente->addRowSet(new TLabel('Convênio: '),$cmsId,'');
        $tabFormPaciente->addRowSet(new TLabel('Paciente: '),$ptsId,$ptsNome);
        
        $form->add($tabFormPaciente);
        
        $form->setFields(array($medId,$cmsId,$ptsId,$ptsNome));
        
        $bntFechar = new TButton('btnFechar');
        $actBtnFechar = new TAction(array($this,'onFormCancel'),'Fechar');
        $bntFechar->setAction($actBtnFechar);
        
        //$formSearch = new TWindow('formSearch');
        //$formSearch->setStackOrder($order);
        
        //$form->setStackOrder = '9999';
        new TInputDialog('Agendamento de Paciente', $form,$actBtnFechar,'Fechar');
        
        
        //parent::add($form);

        
//        $this->form->addQuickAction('Salvar',      new TAction(array($this, 'onFormOk')), 'ico_save.png');
//        $this->form->addQuickAction('Cancelar', new TAction(array($this, 'onFormCancel')), 'ico_cancel.png');
    
        // show the input dialog
        
        //new TInputDialog('Agendar paciente', $this->form);
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
    
    static public function onDateSelect(){
        $script = new TElement('script');
        $script->type = 'text/javascript';
        $script->add("
            var d = new Date('2015-07-01');
            $('#calendar').fullCalendar('gotoDate', d);
        }
    }); 
            
            ");    
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