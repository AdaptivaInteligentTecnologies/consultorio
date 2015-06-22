var nvg19412 = new function(){
  this.w=window;
  this.d=document;
  this.version= 8; 
  this.url=this.dom=this.usr=this.ctr=this.wct=this.wcc=this.qry=this.nvgc=this.nvgt=this.crl=this.hca=0;
  this.wst=this.dsy=1;/* want string, do sync*/
  this.syn=this.val=this.nvg='';
  this.acc = 19412;


  this.account && (this.acc=this.account);
  this.ckn='nav'+this.acc;
  this.ser = ['navdmp.com','navdmp.com'];/* server */
  this.seg = "gender age education marital income city region country connection brand product interest career cluster prolook custom industry everybuyer".split(" ");/* segment positions */
    
  this.preLoad=function(){
    if(this.w.location.hostname.search(this.dom)==-1)this.dom='';
    this.nvg = this.getCookie(this.ckn) || 0;
    if (this.nvg){
      this.nvg=this.nvg.split('_');
      this.usr=this.nvg[0].split('|');
      this.syn=this.usr[1]?'|'+this.usr[1]:'';
      this.ctr=this.nvg[1]||false;
      this.usr=this.usr[0];
    }
    if(typeof(this.w.localStorage)=="object"){
      var lsv=this.w.localStorage.getItem(this.ckn);
      if(lsv)try{lsv=lsv.split(':');this.val=lsv[1].split('_');}catch(e){};
    };
    if(!this.val && this.nvg[2]) this.val=this.nvg[2].split('_');
    if (this.tmc) this.include('','script',this.tmc);
    this.hca && this.makeCampaing();
    this.hca && this.makeTransaction();
  };

  this.load=function(){
    if(!this.nvg)this.preLoad();
    if(!this.usr || this.tmg || this.ctr!=this.datestr() || this.getParameter('navegg_debug')=='1' || !this.syn){
      var url='/usr?v='+this.version;
      url+='&acc='+this.acc;
      if(!this.ctr || (this.ctr != this.datestr()) )url+='&upd=1';
      if(this.usr){
        url+='&id='+this.usr;
        if(!this.syn)
          url+='&jds=1';
      }else url+='&new=1';
      if(!this.wst)url+='&wst=0';
      if(this.wct)url+='&wct=1';
      if(!this.dsy) url += '&dsy=0';
      if (this.getParameter('navegg_debug')=='1') url+='&rdn='+parseInt(Math.random()*1e8);
      this.include('//'+this.ser[0]+url,'script');
    };
    if(this.nvg&&this.getParameter('navegg_debug')!='1')this.saveRequest(this.usr);
    if( typeof(this.loadAddOns) == "function" ) this.loadAddOns();
  };

  this.start=function(id,values){
    if(this.usr!=id ||this.ctr!=this.datestr() && id!=''){
        this.setCookie(this.ckn,id+this.syn+'_'+this.datestr());
        this.usr = id;
    };
    if(values){
      this.val=values.split('_');
      this.saveLocal(this.ckn,id+':'+values);
    if(this.wcc && values) this.cokCustom(id+this.syn);
    if(typeof(this.dataCustom)=="function")this.dataCustom();
    };
    if(!this.nvg&&!(this.getParameter('navegg_debug')=='1'))this.saveRequest(id);
  };

  this.makeCampaing=function(){
    this.nvgc=this.getParameter('nvgc')||this.getCampaingUrl()||this.getCookie('nvgc'+this.acc)||0;
    this.nvgc && this.setCookie('nvgc'+this.acc,this.nvgc,30); /* 30 min */
  };

  this.makeTransaction=function(){
    var camTTL = 30;
    this.nvgt=this.getCookie('nvgt'+this.acc);
    var d = new Date();
      if(this.nvgt){
        this.nvgt=this.nvgt.split('_');
        if(this.nvgt[2]!=this.nvgc){
          var time=(d.getTime() - parseInt(this.nvgt[0]));
          time=camTTL*24*60*60*1000 - time;
          time=time/(60*1000); /* now + camTTL days - timePassed */
          this.setCookie('nvgt'+this.acc,this.nvgt[0]+'_2_'+this.nvgc+'_'+(this.nvgc||this.nvgt[3]),time);
        }
      }else this.setCookie('nvgt'+this.acc,d.getTime()+'_1_'+this.nvgc+'_'+this.nvgc,camTTL*24*60);
      this.nvgt=this.getCookie('nvgt'+this.acc).split('_');
  };

  this.conversion = function(valor){
    if(!this.nvgt)this.makeTransaction();
    var url= '//navdmp.com/req?acc='+this.acc+'&id='+this.usr+'&nvt='+this.nvgt[0]+'&nvc='+this.nvgt[3]+'&revenue='+(valor||'0');
    this.include(url);
    this.setCookie('nvgt'+this.acc,'',1/1E6);
  };

  this.saveRequest = function(profile){
    var a;
    this.par='/req?v=' + this.version;
    this.par += '&id=' + profile + this.syn;
    if(!this.ctr || (this.ctr != this.datestr()) )this.par+='&upd=1';
    if (this.acc) this.par += '&acc=' + this.acc;
    if (!this.usr) this.par += '&new=1';
    if (this.product) this.par += '&prd=' + this.product;
    if (this.category) this.par += '&cat=' + this.category;
    if (this.url) this.par += '&url=' + escape(this.url);
    if (this.d.referrer) this.par += '&ref=' + escape(this.d.referrer);
    this.par+='&tit='+escape(this.d.title);
    if(a=this.getCookie('__utmz')) this.par += '&utm='+escape(a);
    this.nvgc && (this.par+='&nvc='+this.nvgc);
    this.nvgt && (this.par+='&nvt='+this.nvgt[0]+'&nvts='+this.nvgt[1]);
    this.getParameter('nvgc') && (this.par+='&clk=1');
    this.include('//' + this.ser[1] + this.par);
    if(typeof navegg_callback=="function")try{ navegg_callback(); } catch(e) {};
    this.call_callbacks();
  };

  this.call_callbacks = function(){
    function callUserFunc(userFunc){
      if(typeof userFunc=="function")
        try{ userFunc(); } catch(e) {};
    }
    function AsyncExecutor (pending){
      if(typeof pending != "undefined" && pending.length)
        for (var i = 0; i < pending.length; i++)
          callUserFunc(pending[i]);
    }
    AsyncExecutor.prototype['push'] = function(userFunc) {
      callUserFunc(userFunc);
    };
    this.w.naveggReady = new AsyncExecutor(this.w.naveggReady);
  }

  this.setCustom = function(custom){
    var toCus = '/req';
    toCus     += '?acc=' + this.acc;
    if(this.usr)toCus    += '&usr=' + this.usr;
    toCus    += '&cus=' + custom;
    this.include('//' + this.ser[1] + toCus);
  };

  this.getSegment=function(fld){
    var pos = this.findOf(fld,this.seg);
    return 0 > pos ? '' : this.val[ pos ]||'';
  };

  this.getParameter=function(fld){
    if(this.qry==0){
      this.qry = {};
      var prmstr = this.w.location.search.substr(1);
      var prmarr = prmstr.split ("&");
      for(var i = 0; i < prmarr.length; i++){
        var tmparr = prmarr[i].split("=");
        this.qry[tmparr[0]] = tmparr[1];
      };
    };
    return this.qry[fld] || '';
  };

  this.cokCustom=function(){
    var res=[];
    for(var x in this.seg){
      var c=[];
      var a= this.getSegment(this.seg[x]);
      if(a.indexOf('-')>=0){
        a=a.split('-');
        for(var b=0;a[b] && b<10;b++){
          c[b] = a[b];
        }
        res[x]=c.join('-');
        continue;
      }
      res[x]=a; 
    }
    res=res.join(':');
    this.setCookie(this.ckn,this.usr+this.syn+'_'+this.datestr()+'_'+res);
  };

  this.getCookie=function(name){
    var start = this.d.cookie.indexOf( name + "=" );
    var len = start + name.length + 1;
    if(( !start) && ( name != this.d.cookie.substring( 0, name.length ) ) ) return null;
    if(start == -1) return null;
    var end = this.d.cookie.indexOf( ";", len );
    if ( end == -1 ) end = this.d.cookie.length;
    return unescape( this.d.cookie.substring( len, end ) );
  };

  this.setCookie=function(fld,vle,ttl){
    var ltd='';
    if (this.dom) ltd = ';domain=' + this.dom;
    var d = new Date();
    if(ttl!=ttl || !ttl) ttl=365*24*60;
    d.setTime(d.getTime()+(ttl*60*1000));
    var ttl = d.toGMTString();
    this.d.cookie = fld + "=" + vle + ";expires=" + ttl + ";path=/" + ltd;
  };

  this.datestr=function(){
    var d = new Date();
    return (d.getMonth().toString() + d.getDate().toString()) ;
  };

  this.include=function(src, inctype, html, nvgasync){
    if (inctype == '' || inctype == undefined) inctype="script";
    if (nvgasync === '' || nvgasync === undefined) nvgasync=true;
    var c=this.d.createElement(inctype);
    if (inctype == 'script')   c.type="text/javascript";
    if(html) c.text = html;
    else     c.src = src;
    c.async = nvgasync;

    var p = this.d.getElementsByTagName('script')[0];
    p.parentNode.insertBefore(c, p);
  };

  this.saveLocal=function(id,data){
    window.localStorage.setItem(id,data);
  };

 this.doSync = function(version)
 {
   var cok = this.getCookie(this.ckn)||'';
   cok = cok.split('_');
   if(cok[0].search(/\|/)>=0)
   {
       cok[0] = cok[0].split('|');
       cok[0] = cok[0][0];
   };
   cok[0] +='|'+version;
   cok = cok.join('_');
   this.setCookie(this.ckn,cok);
 };

  this.findOf=function(val,ar){
    if(typeof(ar)!='object') return -1;
    for(var x in ar) if(ar[x]==val) return x;
    return -1;
  };

  this.regexEscape = function(text) {
    return text.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, "\\$&");
  };

  this.getCampaingUrl = function(){
  if(this.crl){
    var nvg_arr_pos,nvg_pos,nvg_camId,nvg_rules;
    for(nvg_arr_pos=0;nvg_arr_pos<this.crl.length;nvg_arr_pos++){
      nvg_camId = this.crl[ nvg_arr_pos ][0];
      nvg_rules = this.crl[ nvg_arr_pos ][1];
      for(nvg_pos=0;nvg_pos<nvg_rules.length;nvg_pos++)
        if(window.location.href.search(this.regexEscape(nvg_rules[nvg_pos]))>=0)
          return nvg_camId;
    }
  }
  return 0;
}


};
function nvgGetSegment(f){return nvg19412.getSegment(f);};
function ltgc(s){return nvg19412.getSegment(s);};

nvg19412.load();
