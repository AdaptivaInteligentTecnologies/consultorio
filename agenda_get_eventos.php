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
    SELECT 	
        aps_id,
        aps_pts_id,
    	aps_med_id, 
    	aps_pms_id, 
    	aps_cms_id ,
    	aps_nome_paciente ,
    	aps_data_cadastro, 
    	to_char(aps_data_hora_agendada,'YYYY-MM-DD\"T\"HH24:MI') AS aps_data_hora_agendada,
    	to_char(( aps_data_hora_agendada + time '00:30:00' ), 'YYYY-MM-DD\"T\"HH24:MI') AS aps_data_hora_agendada_plus_30_minutes

        FROM agenda_pacientes
    
     WHERE
         aps_data_hora_agendada >= ? AND aps_data_hora_agendada <= ? 
        
        ");

    $stmt->bindParam(1,$data_ini,PDO::PARAM_STR);
    $stmt->bindParam(2,$data_fim,PDO::PARAM_STR);
        
    $stmt->execute();
    
    $events = array();
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        
        $eventArray['aps_id']                            = $row['aps_id'];
        $eventArray['aps_pts_id']                    = $row['aps_pts_id'];
        $eventArray['aps_med_id']                  = $row['aps_med_id'];
        $eventArray['aps_pms_id']                  = $row['aps_pms_id'];
        $eventArray['aps_cms_id']                   = $row['aps_cms_id'];
        $eventArray['aps_nome_paciente']   = stripslashes($row['aps_nome_paciente']);
        $eventArray['start']                                = $row['aps_data_hora_agendada'];
        $eventArray['end']                                  = $row['aps_data_hora_agendada_plus_30_minutes'];

        $events[] = $eventArray;
        
    }
    
    echo json_encode($events);
    
    
    
} catch (PDOException  $e) {
    print $e->getMessage();
}