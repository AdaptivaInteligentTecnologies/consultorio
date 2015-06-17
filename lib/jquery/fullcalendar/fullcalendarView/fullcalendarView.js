$(document).ready(function() {

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
            displayEventEnd:true,
            selectHelper : true,
            selectable : true,
            events : 'http://localhost/calendario/events.php',
            eventLimitText:'agendamento(s)',
      /*      
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
            
            
            eventRender: function (event, element) {
                                element.attr('href', 'javascript:void(0);');
                                element.click( 
                                function() {
                                    $('#startTime').html(moment(event.start).format('MMM Do h:mm A'));
                                    $('#endTime').html(moment(event.end).format('MMM Do h:mm A'));
                                    $('#eventInfo').html(event.description);
                                    $('#eventLink').attr('href', event.url);
                                    $('#eventContent').dialog({ modal: true, title: event.title, width:350});
                                });
            },
            
            eventDragStop: function(event,jsEvent) {
                var trashEl = jQuery('#lixeira');
                var ofs = trashEl.offset();
                var x1 = ofs.left;
                var x2 = ofs.left + trashEl.outerWidth(true);
                var y1 = ofs.top;
                var y2 = ofs.top + trashEl.outerHeight(true);
                if (jsEvent.pageX >= x1 && jsEvent.pageX<= x2 &&
                        jsEvent.pageY>= y1 && jsEvent.pageY <= y2) {

                        $('#calendar').fullCalendar('removeEvents', event.id);
                        //$('#calendar').fullCalendar('removeEventSource', event.id);
            
                    }
                },

            eventMouseover: function(event, jsEvent, view) {
            //if (view.name !== 'agendaDay') {
                $(jsEvent.target).attr('title', event.title);
            //}
        },        
            
eventDrop: function(event, delta, revertFunc) {

        if (!confirm('Confirma o reagendamento para'+event.start.format()+'?')) {
            revertFunc();
        }

    } ,         
    select : function(start, end, allDay) {
            var title = prompt('Evento');
            var descricao = prompt('Descrição');
            if (title) {
                var start = $.fullCalendar.moment(start).format();
                var end = $.fullCalendar.moment(end).format();
                $.ajax({
                    url : 'http://localhost/calendario/add_events.php',
                    data : '&title=' + title + '&start=' + start + '&end=' + end + '&obs_acao=' + descricao,
                    type : 'POST',
                    sucess : function(json) {
                        alert('OK');
                    }
                });
                calendar.fullCalendar('renderEvent', {
                    title : title,
                    start : start,
                    end : end,
                    allDay : allDay
                }, true);
            }
            calendar.fullCalendar('unselect');
        },            
            events: [
                {
                    id: 1,
					title: 'Nome do fulano do paciente',
					start: '2015-06-01',
                    //url: 'http://google.com/',
                    color: 'red',
                },
                            {
                    id:2,
					title: 'Nome do fulano do paciente',
					start: '2015-06-01',
                    url: 'http://google.com/',
                    color: 'red',
                },
                                {
                    id: 3,
					title: 'TESTE TESTE TESTE',
					start: '2015-06-12T13:30:00',
                    url: 'http://google.com/',
                    color: 'orange',
                    textColor:'white',
                    allday:false,
                    borderColor: '#5173DA',
                   description: '<p>This is just a fake description for the Poker Night.</p><p>Nothing to see!</p>',
                    
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
					title: 'Evento Longo',
					start: '2015-06-11T16:30:00',
					end: '2015-06-13T17:30:00',
                    url: 'http://google.com/',
				},            
            
            ],
            
            
            
		});
            }
            
            
            
	}); 
            
            