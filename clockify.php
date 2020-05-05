<?php
    /* CONSIDERACIONES PARA UN BUEN FUNCIONAMIENTO 
        Escribir en Clockify en pasado
        Aplicar de preferencia una etiqueta
        Para comentarios de reporte: Aplicar etiqueta Comment

        Las siguientes aplican al finalizar la semana en viernes
        Para objetivos futuros: Aplicar etiqueta Target
        Para necesidades o pendientes que requieren direccion: Aplicar etiqueta Obstacle
    
    */

    require 'vendor/autoload.php';

    $builder = new JDecool\Clockify\ClientBuilder();
    $client = $builder->createClientV1('Xno/MlcyDTOYoXO+');
    
    $workspaces = $client->get('workspaces');
    $workspace_id = $workspaces[0]["id"];
    $user =  $client->get('user');
    $user_id = $user["id"];
    $projects = $client->get('workspaces/'.$workspace_id.'/projects');
    $project_HVL;
    foreach($projects as $project) {
        if($project['name'] == 'HVL') {
            $project_HVL = $project;
            break;
        }
    }
    $tags = $client->get('workspaces/'.$workspace_id.'/tags');

    $time_entries = $client->get('workspaces/'.$workspace_id.'/user/'.$user_id.'/time-entries');
    $week_tasks = [];
    $i = 0;
    $monday = new DateTime('monday this week', new DateTimeZone('America/Denver'));
    foreach($time_entries as $task) {
        // Primero revisamos las tareas que son de HVL
        if($task['projectId'] == $project_HVL['id']) {
            $timeTask = $task['timeInterval']['start'];
            // Luego verificamos que las tareas son de esta semana
            if(strtotime($timeTask) > $monday->getTimestamp()) {
                $incidencia = $task['description'];
                $etiquetas = [];
                if(isset($task['tagIds'])) {
                    $tags_id = $task['tagIds'];
                    foreach($tags_id as $tag) {
                        $etiquetas[] = getNameTag($tag);
                    }
                }

                // Creamos un nuevo objeto con la informacion necesaria
                $object = array(
                    'incidencia' => $incidencia,
                    'etiquetas' => $etiquetas[0]
                );
                // Agregamos al array
                $week_tasks[$i] = $object;
                $i++;
            }
        }
    }

    $argument = json_encode($week_tasks);
    $cmd = "python sendEmail.py '".$argument."'";
    $output=shell_exec($cmd);
    echo "<pre>$output</pre>";
    
    function getNameTag($id) {
        global $tags;
        foreach($tags as $tag) {
            if($tag['id'] == $id) {
                return $tag['name'];
            }
        }
    }
?>