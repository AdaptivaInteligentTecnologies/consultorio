<?php 
class SystemMenu extends TPage
{
	private $icons = array(); 
	private $sessao;
	//private $breadcrumb;
	
	public function __construct()
    {
        parent::__construct();
        //parent::add('Pagina incial');

        
         
        
        $this->sessao = new TSession;
        
        $style = new TStyle('boxes');
        $style->{'min-width'}				= '100px';
        $style->{'max-width'}				= '64px';
        $style->{'width'}					= '64px';
        $style->{'height'}					= '100px';
        //$style->{'background-color'}		= '#dedede';
        $style->{'background-color'}		= 'none';
        $style->{'float'} 					= 'left';
        $style->{'margin-right'} 			= '20px';     
        $style->{'margin-bottom'} 			= '20px';
        $style->{'-moz-border-radius'} 		= '5px';
        $style->{'-webkit-border-radius'}	= '5px';
        $style->{'-khtml-border-radius'}	= '5px';
        $style->{'border-radius'} 			= '5px';
        $style->{'padding'}					= '15px';
        $style->{'text-align'}							= 'center';
        $style->show();
//        $this->onShowIcons();
       
    }
    
    public function onShowIcons($param){
    	//echo $param['modulo'];
    	//new TXMLBreadCrumb('menu.xml', __CLASS__);
    	$mastHead = new TMastHead('Menu Principal');
    	
        $this->icons = $this->sessao->getValue('icons');      
		//var_dump($this->icons);
        foreach ($this->icons as $icon) 
        {
			if ( trim(strtoupper($icon[6])) == 'S') {
	        	if ( trim($icon[5]) == $param['modulo']) {
					$div = new TElement('div');
			        $link = new TElement('a');
			        $iconImage = new TImage('img');
			        if ( trim($icon[3]) != ''){
			        	$link-> href = "index.php?class=".$icon[3];
			        }
			        else{
			        	$link-> href = "index.php?class=SystemMenu&method=onShowIcons&modulo=".$icon[1];
			        }
			        $link-> generator = 'adianti';
					$iconImage->src = $icon[4];
			        $link->add($iconImage);
			        $link->add('<h6>'.$icon[2].'</h3>');		        
			        $div->add($link);
			        $div->class = 'boxes';
			        $div->show();
				
        		}
       		}			        
        	
        } // fim foreach
    } // fim onShowIcons

} // fim da classe SystemMenu    
?>