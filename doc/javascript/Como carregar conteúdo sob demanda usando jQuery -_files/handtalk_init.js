/*
* HAND TALK - Translator for web sites content - version 1.5.0
* http://www.handtalk.me   |    carlos@handtalk.com.br
*
* API Documentation: http://api.handtalk.me/info
* SSL Certificate Validation - By COMODO
*/

var dactive=0;function loadScript(url,callback){var script=document.createElement('script')
script.type='text/javascript';if(script.readyState){script.onreadystatechange=function(){if(script.readyState=='loaded'||script.readyState=='complete'){script.onreadystatechange=null;callback();}};}else{script.onload=function(){callback();};}
script.src=url;document.getElementsByTagName('head')[0].appendChild(script);}
var ptl=('https:'==document.location.protocol?'https://':'http://');if(typeof jQuery=='undefined'){loadScript('//code.jquery.com/jquery-1.7.1.min.js',function(){loadScript(ptl+'api.handtalk.me/libs/1.5.0/handtalk.min.js',function(){});});}else{loadScript(ptl+'api.handtalk.me/libs/1.5.0/handtalk.min.js',function(){});}