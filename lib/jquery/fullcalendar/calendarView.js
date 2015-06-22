/**
 * 
 */
jQuery(document).ready(function(){

			var date = new Date();
		    var d = date.getDate();
		    var m = date.getMonth();
		    var y = date.getFullYear();

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
                                //if (inst.input) {
                                	//	alert('select!');
                                		//inst.input.trigger('change');
                                //};
                }
           });
            
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
          
});
    
    
    /*
     <div id='wrap'>
        <div id='external-events'>
            <h4>
                Servicos Complementares</h4>
            <div class='fc-event'>
                Servico 1
            </div>
            <div class='fc-event'>
                Servico 2
            </div>
            <div class='fc-event'>
                Servico 3
            </div>
            <div class='fc-event'>
                Servico 4
            </div>
            <div class='fc-event'>
                Servico 5
            </div>
            <p>
                <input type='checkbox' id='drop-remove' />
                <label for='drop-remove'>
                    Remover após a escolha da data</label>
            </p>
        </div>
        <div id='calendar'>
        </div>
        <div style='clear: both'>
        </div>
    </div>
    
    */