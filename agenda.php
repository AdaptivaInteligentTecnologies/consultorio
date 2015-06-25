<?php
try {
    
    $dbh = new PDO("pgsql:host=localhost dbname=consultorio user=postgres password=postgres host=localhost port=5432");
/*
    
    $dataIni = $_GET['data_ini'];
    $dataFim = $_GET['data_fim'];
    
    $di = explode('/',$dataIni);
    $df = explode('/',$dataFim);
    
    $data_ini = "{$di[2]}-{$di[1]}-{$di[0]}";
    $data_fim = "{$df[2]}-{$df[1]}-{$df[0]}";
    
*/
    $stmt = $dbh->prepare("

        SELECT 	
        aps_id,
        aps_pts_id,
    	aps_pfs_id, 
    	aps_pms_id,
    	pms.pms_descricao,
    	pms.pms_cor backgroundColor,         
    	aps_cps_id ,
    	aps_nome_paciente ,
    	aps_data_cadastro, 
    	to_char(aps_data_hora_ini,'YYYY-MM-DD\"T\"HH24:MI') AS aps_data_hora_ini,
    	to_char(aps_data_hora_fim,'YYYY-MM-DD\"T\"HH24:MI') AS aps_data_hora_fim,
        to_char(( aps_data_hora_ini + time '00:30:00' ), 'YYYY-MM-DD\"T\"HH24:MI') AS aps_data_hora_ini_plus_30_minutes

        FROM agenda_pacientes aps inner join procedimentos_profissionais pms on pms_id = aps_pms_id
    
        --WHERE
        --aps_data_hora_ini >= ? AND aps_data_hora_ini <= ?
        --aps_data_hora_ini = current_date_time 
        
        ");

    //$stmt->bindParam(1,$data_ini,PDO::PARAM_STR);
    //$stmt->bindParam(2,$data_fim,PDO::PARAM_STR);
    
    //$stmt->debugDumpParams();
        
    $stmt->execute();
    
    $events = array();
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){

        $eventArray['id']                                      = $row['aps_id'];
        $eventArray['title']                                   = $row['aps_nome_paciente'];
        $eventArray['start']                                   = $row['aps_data_hora_ini'];
        $eventArray['end']                                     = $row['aps_data_hora_ini_plus_30_minutes'];
        $eventArray['backgroundColor']                         = $row['backgroundcolor'];
        $eventArray['borderColor']                             = 'black';
        //$eventArray['allDay']                                = 'false';
        $eventArray['editable']                                = 'false';
        
/*
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
    
    //var_dump($events);
    
    echo json_encode($events);
    
    
    
} catch (PDOException  $e) {
        echo json_encode(array('status'=>'failed'));
};