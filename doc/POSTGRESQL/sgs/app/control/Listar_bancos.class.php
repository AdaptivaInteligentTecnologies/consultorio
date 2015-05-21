<?php
/**
 * Description of BancosList
 *
 * @author teste
 */
class Listar_bancos extends TStandardList
{
    protected $form;     // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    
    private $masterPanelList;
    private $ListOptions;
    private $ListContent;
    
    private $notebook;
     private $step;
    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    
    public function __construct()
    {
    	
      
        parent::__construct();
        
        new TMastHead('Bancos','Cadastro de bancos');
        
       
        // security check        
        
        // defines the database
        parent::setDatabase('sgs');
        
        // defines the active record
        parent::setActiveRecord('Bancos');
        
        // defines the filter field
        parent::setFilterField('bnc_nome');
        
        parent::setLimit(7);
        
        
        $this->notebook = new TNotebook(100,100);
        $this->notebook->setSize(730, 80);    


        
        $this->masterPanelList = new TElement('div');
        $this->masterPanelList->{'id'} = 'adaptiva-sgs-data-wrapper'; 
        
        
    
    
/*        $this->ListOptions = new TElement('div');
        $this->ListOptions->{'class'} = 'adaptiva-sgs-data-search-options';
        */
//        $this->ListOptions->add('teste teste teste testeteste teste teste testeteste teste teste testeteste teste teste testeteste teste teste testeteste teste teste teste'); 
        
/*        $this->ListContent = new TElement('div');
        $this->ListContent->{'class'} = 'adaptiva-sgs-data-content';

        $this->masterPanelList->add($this->ListOptions);
        $this->masterPanelList->add($this->ListContent);
        */
        
        // creates the form
        $this->form = new TForm('form_search_banco');
        
        // creates a table
        $table = new TTable;
        $this->notebook->appendPage('Pesquisa',$table);
      //  $this->notebook->appendPage('Dados para Pesquisa', $table);
        
        // add the table inside the form
        $this->form->add($this->notebook);
        
        // create the form fields
        $filter = new TEntry('bnc_nome');       
        
        // add a row for the filter field
        $row=$table->addRow();
        $row->addMultiCell(new TLabel('Banco: '),$filter);
        //$row->addCell();
        
        // create two action buttons to the form
        $find_button = new TButton('find');
        $new_button  = new TButton('new');
        // define the button actions
        $find_button->setAction(new TAction(array($this, 'onSearch')), _t('Find'));
        $find_button->setImage('ico_find.png');
        
        $class = 'BancosForm';
        $new_button->setAction(new TAction(array($class, 'onEdit')), _t('New'));
        $new_button->setImage('ico_new.png');
        
        // add a row for the form actions
        $row=$table->addRow();
        $row->addMultiCell($find_button,$new_button);
       // $row->addCell();
        
        // define wich are the form fields
        $this->form->setFields(array($filter, $find_button, $new_button));
        
        // creates a DataGrid

        $this->datagrid = new TQuickGrid;
        $this->datagrid->setHeight(320);
        

        // creates the datagrid columns
        $this->datagrid->addQuickColumn('id', 'id', 'right', 130, new TAction(array($this, 'onReload')), array('order', 'id'));
        $this->datagrid->addQuickColumn('Nome', 'bnc_nome', 'left', 450, new TAction(array($this, 'onReload')), array('order', 'bnc_nome'));
        $this->datagrid->addQuickColumn('Ativo', 'bnc_ativo', 'left', 100, new TAction(array($this, 'onReload')), array('order', 'bnc_ativo'));
       
        
        // add the actions to the datagrid
        $class = 'BancosForm';
        $this->datagrid->addQuickAction(_t('Edit'), new TDataGridAction(array($class, 'onEdit')), 'id', 'ico_edit.png');
        $this->datagrid->addQuickAction('Ativar\Inativar', new TDataGridAction(array($this, 'onInativar')), 'id', 'ico_delete.png');
        
        // create the datagrid model
        $this->datagrid->createModel();
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        // creates the page structure using a table
        $table = new TTable;
        $row = $table->addRow();
        $row->addCell($this->datagrid);
        // add a row for page navigation
        $row = $table->addRow();
        $row->addCell($this->pageNavigation);
        // add the table inside the page
	
//        $this->ListOptions->add($this->form);
//        $this->ListContent->add($table);
        
        parent::add($this->notebook);
    }
  
    
    function onInativar($param){
     
        
        // get the parameter $key
        $key=$param['key'];
        
        // define two actions
        $action = new TAction(array($this, 'Inativa'));
        
        // define the action parameters
        $action->setParameter('key', $key);
        
        // shows a dialog to the user
        new TQuestion('Voce Realmente deseja alterar status deste Banco?', $action);
          
    }
    
    function inativa($param){
        
            // get the parameter $key
            $key=$param['key'];
            // open a transaction with database 'changeman'
            TTransaction::open('sgs');
            
            // instantiates object Issue
            $object = new Bancos($key);
            if ($object->status == 'Y'){     
              $object-> status = 'N';
            }
            else {
               $object-> status = 'Y'; 
              
            }
            $object->store();
            TTransaction::close();
             new TMessage('info','Status alterado com Sucesso!') ;    
            $this->onReload(); 
    }
}

?>
