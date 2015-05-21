function Adianti(){}

function __adianti_goto_page(page)
{
    window.location = page;
}

function __adianti_load_html(content)
{
   if ($('[widget="TWindow"]').length > 0)
   {
       $('[widget="TWindow"]').attr('remove', 'yes');
       $('#adianti_online_content').hide();
       $('#adianti_online_content').html(content);
       $('[widget="TWindow"][remove="yes"]').remove();
       $('#adianti_online_content').show();
   }
   else
   {
       if (content.indexOf("TWindow") > 0)
       {
           $('#adianti_online_content').html(content);
       }
       else
       {
           $('#adianti_div_content').html(content);
       }
   }
}

function __adianti_load_page_no_register(page)
{
    $.get(page, function(data)
    {
        __adianti_load_html(data);
    });
}

function __adianti_append_page(page)
{
    $.get(page, function(data)
    {
        $('#adianti_online_content').after('<div></div>').html(data);
    });
}

function __adianti_load_page(page)
{
    $( '.modal-backdrop' ).remove();
    
    url = page;
    url = url.replace('index.php', 'engine.php');
    __adianti_load_page_no_register(url);
    
    if ( history.pushState && ($('[widget="TWindow"]').length == 0) )
    {
        __adianti_register_state(url);
    }
}

function __adianti_block_ui(wait_message)
{
     $.blockUI({ 
        message: '<h1>'+wait_message+'</h1>',
        css: { 
            border: 'none', 
            padding: '15px', 
            backgroundColor: '#000', 
            'border-radius': '5px 5px 5px 5px',
            opacity: .5, 
            color: '#fff' 
        }
    });
}

function __adianti_unblock_ui()
{
    $.unblockUI();
}

function __adianti_post_data(form, url)
{
    url = 'index.php?'+url;
    __adianti_register_state(url); 
    url = url.replace('index.php', 'engine.php');
    data = $('#'+form).serialize();
    
    $.post(url, data,
        function(result)
        {
            __adianti_load_html(result);
            __adianti_unblock_ui();
        });
}

function __adianti_register_state(url)
{
    var stateObj = { url: url };
    if (typeof history.pushState != 'undefined')
    {
        history.pushState(stateObj, "", url.replace('engine.php', 'index.php'));
    }
    if (typeof Adianti.onLoadClass == "function")
    {
        Adianti.onLoadClass(url);
    }
}