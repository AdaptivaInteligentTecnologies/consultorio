<?php

$type       = $_POST['type'];
$eventId    = $_POST['eventId'];


if (!isset($type)){
    echo json_encode('Tipo de evento não informado',JSON_UNESCAPED_UNICODE);
    exit;
}

try {
        
            $dbh = new PDO("pgsql:host=localhost dbname=consultorio user=postgres password=postgres host=localhost port=5432");
        
            if($type == 'retrieveAll') {
        
        
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
                            	to_char(aps_data_agendada,'YYYY-MM-DD') ||'T'||to_char(aps_hora_agendada,'HH24:MI') AS aps_data_hora_agenda,
                                to_char(aps_data_agendada,'YYYY-MM-DD') ||'T'||to_char( (aps_hora_agendada + interval '30 minutes'),'HH24:MI') AS aps_data_hora_agenda_plus_30,
	                            pfs.pfs_nome
                    FROM agenda_pacientes aps inner join procedimentos_profissionais pms on pms_id = aps_pms_id
                    INNER JOIN profissionais pfs on pfs.pfs_id = aps_pfs_id
                    
                    ");

                $stmt->execute();
                $events = array();
        
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                    $eventArray['id']                                      = $row['aps_id'];
                    $eventArray['title']                                   = "Paciente: {$row['aps_nome_paciente']} Profissional: {$row['pfs_nome']} ";
                    $eventArray['start']                                   = $row['aps_data_hora_agenda'];
                    $eventArray['end']                                     = $row['aps_data_hora_agenda_plus_30'];
                    $eventArray['backgroundColor']                         = $row['backgroundcolor'];
                    $eventArray['borderColor']                             = 'black';
                    //$eventArray['url']                                     = 'index.php?class=AgendaPacienteForm&method=onEdit&key='.$row['aps_id'];
                    //$eventArray['url']                                     = TScript::create("Adianti.waitMessage = 'Carregando';__adianti_post_data('form_agendar_paciente', 'class=AgendaPacienteForm&method=onEdit&key={$row['aps_id']}');return false;");
                    //$eventArray['editable']                                = 'true';
                    array_push($events, $eventArray);
                }
                echo json_encode($events);
            }
            
            if($type == 'removeEvent') {
                    $stmt = $dbh->prepare("DELETE FROM agenda_pacientes where aps_id = {$eventId}");
                    $stmt->execute();
                    echo json_encode(array('status'=>'success'));
            }
            
            if($type == 'alterEvent') {
                echo "teste";
                exit;
                $stmt = $dbh->prepare("UPDATE agenda_pacientes SET where aps_id = {$eventId}");
                $stmt->execute();
                echo json_encode(array('status'=>'success'));
            }
            
} 
catch (PDOException  $e) 
{
    echo json_encode(array('status'=>'failed','error'=>$e->getMessage()));
};
        
    


 
?>