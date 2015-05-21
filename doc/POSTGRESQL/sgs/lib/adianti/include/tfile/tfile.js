function TFileAjaxUpload(idFile, action, divParent)
{
    var idFile = idFile;
    var action = action;
    var divParent = $('#'+divParent);
    
    // Function that will allow us to know if Ajax uploads are supported
    TFileAjaxUpload.prototype.supportAjaxUploadWithProgress = function()
    {
        return this.supportFileAPI() && this.supportAjaxUploadProgressEvents() && this.supportFormData();
    };
    
    // Is the File API supported?
    TFileAjaxUpload.prototype.supportFileAPI = function()
    {
        var fi = document.createElement('INPUT');
        fi.type = 'file';
        return 'files' in fi;
    };
        
    // Are progress events supported?
    TFileAjaxUpload.prototype.supportAjaxUploadProgressEvents = function()
    {
        var xhr = new XMLHttpRequest();
        return !! (xhr && ('upload' in xhr) && ('onprogress' in xhr.upload));
    };
    
    // Is FormData supported?
    TFileAjaxUpload.prototype.supportFormData = function()
    {
        return !! window.FormData;
    };
    
    TFileAjaxUpload.prototype.initFileAjaxUpload = function()
    {
        if (this.supportAjaxUploadWithProgress())
        {
            var formData = new FormData();
                        
            // FormData only has the file
            var fileInput = document.getElementById( idFile );
            var file = fileInput.files[0];
            
            formData.append('fileName', file);
            
            // Code common to both variants
            this.sendXHRequest(formData, action);
        }
    };
    
    // Once the FormData instance is ready and we know
    // where to send the data, the code is the same
    // for both variants of this technique
    TFileAjaxUpload.prototype.sendXHRequest = function(formData, uri)
    {
        // Get an XMLHttpRequest instance
        var xhr = new XMLHttpRequest();
        
        // Set up events
        xhr.upload.addEventListener('loadstart', this.onloadstartHandler, false);
        xhr.upload.addEventListener('progress', this.onprogressHandler, false);
        xhr.upload.addEventListener('load', this.onloadHandler, false);
        xhr.addEventListener('readystatechange', this.onreadystatechangeHandler, false);
        
        // Set up request
        xhr.open('POST', uri, true);
        
        // Fire!
        xhr.send(formData);
    };
    
    // Handle the start of the transmission
    TFileAjaxUpload.prototype.onloadstartHandler = function(evt)
    {
        if( $(divParent).children('img').size() == 0 )
            $(divParent).prepend($('<img>'));
        
        $(divParent).children('img').attr({src:'lib/adianti/images/tfile_loader.gif',title:'...'});
    };
    
    // Handle the end of the transmission
    TFileAjaxUpload.prototype.onloadHandler = function(evt)
    {
    };
    
    // Handle the progress
    TFileAjaxUpload.prototype.onprogressHandler = function(evt)
    {
        //var percent = evt.loaded/evt.total*100;
    };
    
    // Handle the response from the server
    TFileAjaxUpload.prototype.onreadystatechangeHandler = function(evt)
    {
        var status = null;
        
        try
        {
            status = evt.target.status;
        }
        catch(e)
        {
            return;
        }
    
        if (status == '200' && evt.target.responseText)
        {
            var response = $.parseJSON( evt.target.responseText );
            
            if( response.type == 'success' )
            {
                
                $(divParent).children('input[type=hidden]').val( response.fileName );
                $(divParent).children('img').attr({src :'lib/adianti/images/ico_ok.png', title: 'Sucesso'});
            }
            else
                $(divParent).children('img').attr({src: 'lib/adianti/images/error.png',title: response.msg});
        }
    };
}
