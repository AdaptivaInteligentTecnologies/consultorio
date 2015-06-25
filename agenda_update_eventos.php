<?php
try {
    
    $dbh = new PDO("pgsql:host=localhost dbname=consultorio user=postgres password=postgres host=localhost port=5432");

    $dataIni = $_GET['data_ini'];
    $dataFim = $_GET['data_fim'];
    
    $di = explode('/',$dataIni);
   $df = explode('/',$dataFim);
    
   $data_ini = "{$di[2]}-{$di[1]}-{$di[0]}";
   $data_fim = "{$df[2]}-{$df[1]}-{$df[0]}";
    
    /*
    $data_ini->setDate($di[2],$di[1],$di[0]);
    $data_ini->format('Y-m-d');
    
    
    
    $data_fim      = new DateTime;
    $data_fim->setDate($df[2],$df[1],$df[0]);
    $data_fim->format('Y-m-d');
    */
    
    
    
    
    //echo $data_ini;
    //exit;

    /*
    print_r($data_ini->format('Y-m-d') );
    echo "<br />";
    print_r($data_fim->format('Y-m-d') );
    echo "<br />";
    */

    $stmt = $dbh->prepare("
UPDATE agenda_pacientes SET aps_data_hora_agendada
        SELECT 	
        aps_id,
        aps_pts_id,
    	aps_med_id, 
    	aps_pms_id,
    	pms.pms_descricao,
    	pms.pms_cor backgroundColor,         
    	aps_cms_id ,
    	aps_nome_paciente ,
    	aps_data_cadastro, 
    	to_char(aps_data_hora_ini,'YYYY-MM-DD\"T\"HH24:MI') AS aps_data_hora_ini,
    	to_char(( aps_data_hora_ini + time '00:30:00' ), 'YYYY-MM-DD\"T\"HH24:MI') AS aps_data_hora_agendada_plus_30_minutes

        FROM agenda_pacientes aps inner join procedimentos_medicos pms on pms_id = aps_pms_id
    
     WHERE
         aps_data_hora_agendada >= ? AND aps_data_hora_agendada <= ? 
        
        ");

    $stmt->bindParam(1,$data_ini,PDO::PARAM_STR);
    $stmt->bindParam(2,$data_fim,PDO::PARAM_STR);
        
    $stmt->execute();
    
    $events = array();
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){

        $eventArray['id']                                        = $row['aps_id'];
        $eventArray['title']                                    = $row['aps_nome_paciente'];
        $eventArray['start']                                   = $row['aps_data_hora_agendada'];
        $eventArray['end']                                     = $row['aps_data_hora_agendada_plus_30_minutes'];
        $eventArray['backgroundColor']          = $row['backgroundcolor'];
        
        $eventArray['borderColor']                   = 'black';
        //$eventArray['allDay']                                = 'false';
        $eventArray['editable']                           = 'true';
        
/*
 *         
 *         
        "url": "http://google.com", // Optional, will not open because of browser-iframe security issues
        "className": "test-class", // Optional
        "editable": true, // Optional
        "color": "yellow", // Optional
        "borderColor": "red", // Optional
        "backgroundColor": "yellow", // Optional
        "textColor": "green" // Optional
 */        

        $events[] = $eventArray;
        
    }
    
    echo json_encode($events);
    
    
    
} catch (PDOException  $e) {
    print $e->getMessage();
}