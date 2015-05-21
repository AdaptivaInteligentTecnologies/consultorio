<?php
class TMastHead extends TElement
{

	public function __construct($nomeModulo = NULL, $descricaoModulo = NULL, $icone = NULL)
    {
    	
        
    	
    	parent::__construct('div');
        $this->{'id'} = 'masthead';
  		  	
        $div_class_container				= new TElement('div');
    	$div_class_container->{'class'}		= 'container';

    	$div_class_masthead_pad 			= new TElement('div');
    	$div_class_masthead_pad->{'class'}	= 'masthead-pad';
    	
    	$div_class_masthead_text 			= new TElement('div');
    	$div_class_masthead_text->{'class'}	= 'masthead-text';
    	
    	$tag_h2								= new TElement('h2');
    	$tag_h2->add($nomeModulo);
    	
    	$tag_p								= new TElement('p');
    	$tag_p->add($descricaoModulo);
    	
    	
    	$div_class_masthead_text->add($tag_h2);
    	$div_class_masthead_text->add($tag_p);
    	
    	$div_class_masthead_pad->add($div_class_masthead_text);
    	
    	$div_class_container->add($div_class_masthead_pad);
    	
    	$this->add($div_class_container);
    	
    	
        	
        parent::add( $this->container );
        parent::show();
    }
    
    public function show()
    {
        //TPage::include_css('lib/adianti/include/tbreadcrumb/tbreadcrumb.css');
        parent::show();
    }
}
?>