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
class AgendaPacienteForm extends TPage
{
    public function __construct()
    {
        parent::__construct();


/*
 *         events: {
				url: 'php/get-events.php',
				error: function() {
					$('#script-warning').show();
				}
			},
			loading: function(bool) {
				$('#loading').toggle(bool);
			}            

 */        
        
        
        
        //TPage::include_css('lib/jquery/fullcalendar/fullcalendar.print.css');
        TPage::include_css('lib/jquery/fullcalendar/fullcalendar.css');
        TPage::include_js('lib/jquery/fullcalendar/lib/moment.min.js');
        TPage::include_js('lib/jquery/fullcalendar/fullcalendar.min.js');
        TPage::include_js('lib/jquery/fullcalendar/lang-all.js');
        
        $container = new TVBox() ;
        $container->style = 'width:100%;height:100%';
        
        // fullcalendar
       
        $divLoading = new TElement('div');
        $divLoading->id = 'loading';
        $divLoading->add('loading...');
        $divLoading->style="display: none;position: absolute;top: 10px;right: 10px;";

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
        
        $script = new TElement('script');
        $script->type = 'text/javascript';
        $script->add("$(document).ready(function() {

            
            function renderCalendar() {
            
            
            $('#calendar').fullCalendar({
			    header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,basicWeek,basicDay'
			},
			
            defaultDate: '".date("Y-m-d")."',
            lang: 'pt-br',
            buttonIcons: true,
            
            weekNumbers: false,
            businessHours: true,
			editable: true,
			eventLimit: true, 
            theme:true,
            
            events: [
                {
                    id: 1,
					title: 'Nome do fulano do paciente',
					start: '2015-06-01',
                    url: 'http://google.com/',
                    color: 'red',
                },
                            {
                    id: 2,
					title: 'Nome do fulano do paciente',
					start: '2015-06-01',
                    url: 'http://google.com/',
                    color: 'red',
                },
                                {
                    id: 3,
					title: 'Nome do fulano do paciente',
					start: '2015-06-01',
                    url: 'http://google.com/',
                    color: 'orange',
                    
                },
                                {
                    id: 4,
					title: 'Nome do fulano do paciente',
					start: '2015-06-01',
                    url: 'http://google.com/',
                    color: 'red',
                },
                                {
                    id: 5,
					title: 'Nome do fulano do paciente',
					start: '2015-06-01',
                    url: 'http://google.com/',
                    color: 'red',
                },
                                {
                    id: 6,
					title: 'Nome do fulano do paciente',
					start: '2015-06-01',
                    url: 'http://google.com/',
                    color: 'red',
                },
                                                {
                    id: 7,
					title: 'Nome do fulano do paciente',
					start: '2015-06-01',
                    url: 'http://google.com/',
                    color: 'red',
                },
                {
                    id: 2,
					title: 'Long Event',
					start: '2015-06-11T16:00:00',
					end: '2015-06-13T16:00:00',
                    url: 'http://google.com/',
				},            
            
            ],
            
            
            
		});
            }
            
            renderCalendar();
            
	}); 
            
            
            
            ");
        
        $qform = new TForm('form_agendar_paciente');
        $qform->class='tform';

        $btnAgendar = new TButton('btnAgendar');
        $btnAgendar->setLabel('Novo agendamento');
        $btnAgendar->popover = 'true';
        $btnAgendar->poptitle = 'Agendamento';
        $btnAgendar->popcontent = 'Clique aqui para agendar um paciente';
        $btnAgendar->setImage('bs:calendar white');
        $btnAgendar->class = 'btn btn-success btn-sm';
        $btnAgendar->setAction(new TAction(array($this, 'onInputDialog')),'Novo agendamento');

        $btnProximaDataDisponivel = new TButton('btnProximaDataDisponivel');
        $btnProximaDataDisponivel->setLabel('Próxima data disponível');
        $btnProximaDataDisponivel->popover = 'true';
        $btnProximaDataDisponivel->poptitle = 'Seleção de próxima data disponível';
        $btnProximaDataDisponivel->popcontent = 'Localiza a próxima data disponível de consulta <br />para o médico selecionado.';
        $btnProximaDataDisponivel->setImage('bs:next white');
        $btnProximaDataDisponivel->class = 'btn btn-info btn-sm';
        $btnProximaDataDisponivel->setAction(new TAction(array($this, 'onInputDialog')),'Próxima data disponível');
        
        
        
        $tableToolBar       = new TTable('tabelaToolBar');
        $tableToolBar->style = 'width:100%;';
        
        
        $med_id                  = new TCombo('med_id');
        $med_id->setSize(250);
        $med_id->addItems(array(1=>"Médico 1",2=>"Médico 2"));

        //$tableToolBar->addRowSet(new TLabel('Médico: '),$med_id);
        $row = $tableToolBar->addRow();
        $cellMedico= $row->addCell('Médico: ');
        $cellMedico->width = '70px;';
        $cellMedico->align = 'right';
        $cellComboMedico = $row->addCell($med_id);
        $cellComboMedico->width = '255px';
        
        
        $cellBtnProximaDataDisponivel = $row->addCell($btnProximaDataDisponivel)->align='left';
        
        $cellBtnAgendar = $row->addCell($btnAgendar)->align='right';
        
        $row->colspan='4';

        
        $qform->setFields(array($med_id,$btnProximaDataDisponivel, $btnAgendar));
        $qform->style = 'width: 100%';
        $qform->add($tableToolBar);
        
        $container->add($qform);
        $container->add($divWarning);
        $container->add($divLoading);
        $container->add($calendario);
        $container->add($script);
        
        parent::add($container);
    
    
    }
    
    /**
     * Open an input dialog
     */
    public function onInputDialog( $param )
    {
        $form = new TQuickForm('input_form');
        $form->style = 'padding:20px';
    
        $login = new TEntry('login');
        $pass  = new TPassword('password');
    
        $form->addQuickField('Login', $login);
        $form->addQuickField('Password', $pass);
    
        $form->addQuickAction('Confirm 1', new TAction(array($this, 'onConfirm1')), 'ico_save.png');
        $form->addQuickAction('Confirm 2', new TAction(array($this, 'onConfirm2')), 'ico_apply.png');
    
        // show the input dialog
        new TInputDialog('Agendar paciente', $form);
    }
    
    /**
     * Show the input dialog data
     */
    public function onConfirm1( $param )
    {
        new TMessage('info', 'Confirm1 : ' . json_encode($param));
    }
    
    /**
     * Show the input dialog data
     */
    public function onConfirm2( $param )
    {
        new TMessage('info', 'Confirm2 : ' . json_encode($param));
    }    
}

?>