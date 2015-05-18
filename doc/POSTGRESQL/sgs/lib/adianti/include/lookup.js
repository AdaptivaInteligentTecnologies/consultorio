function ajaxLookup(string_action, field)
{
    var id = field.value;
    //if (id)
    {
        ajaxExec(string_action +'&key='+id);
    }
}

function ajaxExec(string_action)
{
    uri = 'engine.php?' + string_action +'&static=1';
    
    $.get(uri, function(data)
    {
        tmp = data;
        tmp = new String(tmp.replace(/\<script language=\'JavaScript\'\>/g, ''));
        tmp = new String(tmp.replace(/\<script type=\'text\/javascript\'\>/g, ''));
        tmp = new String(tmp.replace(/\<script type=\"text\/javascript\"\>/g, ''));
        tmp = new String(tmp.replace(/\<\/script\>/g, ''));
        tmp = new String(tmp.replace(/window\.opener\./g, ''));
        tmp = new String(tmp.replace(/window\.close\(\)\;/g, ''));
        tmp = new String(tmp.replace(/(\n\r|\n|\r)/gm,''));
        tmp = new String(tmp.replace(/^\s+|\s+$/g,"")); //trim
        
        if ($('[widget="TWindow"]').length > 0)
        {
           // o código dinâmico gerado em ajax lookups (ex: seekbutton)
           // deve ser modificado se estiver dentro de window para pegar window2
           tmp = new String(tmp.replace(/TWindow/g, 'TWindow2'));
        }
        
        try {
            eval(''+tmp+''); 
        } catch (e) {
            if (e instanceof SyntaxError) {
                //alert(e.message + ': ' + tmp);
                $('<div />').html(e.message + ': ' + tmp).dialog({modal: true, title: 'Error', width : '80%', height : 'auto', resizable: true, closeOnEscape:true, focus:true});
            }
        }
        
    }).fail(function(jqxhr, settings, exception) {
       //alert(exception + ': ' + jqxhr.responseText);
       $('<div />').html(jqxhr.responseText).dialog({modal: true, title: 'Error', width : '80%', height : 'auto', resizable: true, closeOnEscape:true, focus:true});
    });
}