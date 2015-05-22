<?php
/**
 * LoginForm Registration
 * @author  <your name here>
 */
class LoginForm extends TPage
{
    protected $form; // form
    protected $notebook;
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct()
    {
        parent::__construct();
        
        $table = new TTable;
        $table->width = '100%';
        
        // creates the form
        $this->form = new TForm('form_login');
        $this->form->class = 'tform';
        $this->form->style = 'margin:auto;width: 350px';
        
        // add the notebook inside the form
        $this->form->add($table);
        
        // create the form fields
        $login      = new TEntry('login');
        $password   = new TPassword('password');

        // define the sizes
        $login->setSize(150, 40);
        $password->setSize(150, 40);
        
        // create an action button (save)
        $login_button=new TButton('btn_login');
		/*
        $span = new TElement('span');
		$span->add('class="glyphicon glyphicon-circle-arrow-right"');
        */
		$login_button->setAction(new TAction(array($this, 'onLogin')), _t('Enter'));
        $login_button->setImage('ico_apply.png');
        $login_button->{'class'} = 'btn btn-primary btn-sm';
//        $login_button->setLabel('teste<span class="glyphicon glyphicon-circle-arrow-right"></span>');
        
        
        
        //<a href="#" class="btn btn-success btn-lg">Entrar <span class="glyphicon glyphicon-circle-arrow-right"></span></a>
        
        // add a row for the field login
        $row=$table->addRow();
        $cell = $row->addCell(new TLabel('Login'));
        $cell->colspan = 2;
        $row->class = 'tformtitle';
        
        $table->addRowSet(new TLabel(_t('User') . ': '), $login);
        $table->addRowSet(new TLabel(_t('Password') . ': '),$password);
        $row = $table->addRowSet($login_button, '');
        $row->class = 'tformaction';

        $this->form->setFields(array($login,$password,$login_button));
        
        // add the form to the page
        parent::add($this->form);
    }

    /**
     * Autenticates the User
     */
    function onLogin()
    {
        try
        {
            TTransaction::open('sgs');
            $data = $this->form->getData('StdClass');
            $this->form->validate();
            $user = SystemUsuario::autenticate( $data->login, $data->password );
            if ($user)
            {
                $programs = $user->getPrograms();
                
                $icons = $user->getIcons();

                $unidades = $user->getUnidades();

                //var_dump($icons);
                //var_dump($programs);
                //var_dump($unidades);
                
                $programs['LoginForm'] = TRUE;
                $programs['SystemMenu'] = TRUE;
                
                TSession::setValue('logged', TRUE);
                TSession::setValue('login', $data->login);
                TSession::setValue('username', $user->usu_nome);
                TSession::setValue('programs',$programs);
                TSession::setValue('icons',$icons);
                TSession::setValue('unidades',$unidades);
                
                TApplication::gotoPage('SystemMenu','onShowIcons',array('modulo'=>'')); // reload
                TSession::setValue('systemmenu', 'SystemMenu');
                
//                $frontpage = $user->frontpage;
                
//                if ($frontpage instanceof SystemProgram AND $frontpage->controller)
//                {
//                    TApplication::gotoPage($frontpage->controller); // reload
//                	TSession::setValue('frontpage', $frontpage->controller);
//                }
//                else
//                {
//                    TApplication::gotoPage('EmptyPage'); // reload
//                    TSession::setValue('frontpage', 'EmptyPage');
//                }
            }
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error',$e->getMessage());
            //new TMEssage('error',$e->getTraceAsString());
            TSession::setValue('logged', FALSE);
            TTransaction::rollback();
        }
    }
    
    /**
     * Logout
     */
    function onLogout()
    {
        TSession::freeSession();
        TApplication::gotoPage('LoginForm', '');
    }
}
?>