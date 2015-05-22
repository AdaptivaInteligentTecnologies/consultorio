<?php 
class SystemUsuario extends TRecord
{
	const TABLENAME		= 'usuarios';
	const PRIMARYKEY	= 'id';
	const IDPOLICY		= 'serial';
	
	//private $frontpage;
	
	private $array_programas_do_usuario = array();
	private $array_icons = array();
  	private $array_unidades = array();
	//private $usuario_tem_perfil = array();
	//private $perfil_tem_modulo = array();
	
	public function __construct( $id = NULL )
	{
		parent::addAttribute('usu_nome'); // Limita os atributos que serão gravados em STORE()
		parent::addAttribute('usu_senha');	
		parent::addAttribute('usu_email');	
		parent::addAttribute('usu_ativo');	
		parent::addAttribute('usu_expirar_senha');	
		parent::addAttribute('usu_dt_expirar_senha');	
		//parent::addAttribute('usu_dt_criacao');	
		parent::addAttribute('usu_login');		
	}
	
    /**
     * Autentica o usuários
     * @param $login String com nome do usuário
     * @param $password String com a senha do usuário
     * @returns TRUE se a senha for correta, caso contrário lança uma Exception
     */
	
	public static function autenticate($login, $password)
    {
        $user = self::newFromLogin($login);
        
        if ($user instanceof SystemUsuario)
        {
            if (isset( $user->usu_senha ) AND ($user->usu_senha == md5($password)) )
            {
                return $user;
            }
            else
            {
                throw new Exception(_t('Wrong password'));
            }
        }
        else
        {
            throw new Exception(_t('User not found'));
        }
    }
    
    /**
     * Retorna um objeto SystemUsuario baseado no seu login
     * @param $login String com login do usuário
     */
    static public function newFromLogin($login)
    {
        $repos = new TRepository('SystemUsuario');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('usu_login', '=', $login));
        $objects = $repos->load($criteria);
        if (isset($objects[0]))
        {
            return $objects[0];
        }
    }    
	
    /**
     * Limpa as matrizes carregadas pelas agregações
     */
    public function clearParts()
	{
		$this->array_programas_do_usuario = array();
		$this->array_icons = array();
	}
	
    /**
     * Retorna a matriz usuario_tem_perfil
     */
//    public function get_usuario_tem_perfil()
//	{
//		return $this->usuario_tem_perfil;
//	}
	
//    public function get_perfil_tem_modulo()
//	{
//		return $this->perfil_tem_modulo;
//	}

//	public function addUsuarioTemPerfil(SystemUsuarioTemPerfil $object)
//    {
//        $this->usuario_tem_perfil [] = $object;
//    }
	
	//Adiciona programas de usuário a matriz
	public function addSystemPrograms(SystemProgramasDoUsuario $object)
    {
        $this->array_programas_do_usuario [] = $object;
    }
	

  // Adiciona unidades a matriz do usuário
  public function addSystemUnidades(SystemUnidadesDoUsuario $object)
  {
      $this->array_unidades[] = $object;
  }

  // Retorna matriz contento as unidades do usuário
  public function getsystemUnidades()
  {
    return $this->array_unidades;
  }


	public function addSystemIcons(SystemProgramasDoUsuario $object)
    {
        $this->array_icons [] = $object;
    }
    
	//Retorna itens da matriz array_programas_do_usuario
	public function getSystemPrograms()
	{
		return $this->array_programas_do_usuario;
	}

	public function getSystemIcons()
	{
		return $this->array_icons;
	}

public function getUnidades()
{
  $i=0;
  $x=0;
  foreach ( $this->getsystemUnidades() as $unidade ) {
    $und[$i][$x+0] = $unidade->id;
    $und[$i][$x+1] = $unidade->und_nome;
    $i++;
    $x=0;
  }
  return $und;
}
      
	
	/**
     * Return the programs the user has permission to run
     */
    
	public function getPrograms()
    {
        
        foreach( $this->getSystemPrograms() as $prog )
          {
              $programs[$prog->mod_controller] = true;
           }
                
        return $programs;
    }
	
	public function getIcons()
    {
        $i=0;
        $x=0;
        foreach( $this->getSystemIcons() as $icon)
          {
              $icons[$i][$x+0] = $icon->usu_id;
              $icons[$i][$x+1] = $icon->mod_id;
              $icons[$i][$x+2] = $icon->mod_modulo;
              $icons[$i][$x+3] = $icon->mod_controller;
              $icons[$i][$x+4] = $icon->mod_imagem;
              $icons[$i][$x+5] = $icon->mod_modulo_pai;
              $icons[$i][$x+6] = $icon->mod_item_de_menu;
              
              $i++;
              $x=0;
              
          }
                
        return $icons;
    }
    
    
	
    
    
    /**
     * Carrega o objeto e seus agregados
     * @param $id object ID
     */

    
    public function load($id)
    {

    	/*
    	 * Carrega os objetos SystemProgramasDoUsuario relacionados
    	 */
    	  $repository = new TRepository('SystemProgramasDoUsuario');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('usu_id', '=', $id));
        
        $array_programas_do_usuario = $repository->load($criteria);

        foreach ($array_programas_do_usuario as $prog)
        {
            $this->addSystemPrograms($prog);
            $this->addSystemIcons($prog);
        }


      /*
       * Carrega as unidades do usuário através de SystemUnidadesDoUsuario
       */
        $repository = new TRepository('SystemUnidadesDoUsuario');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('id', '=', $id));
        
        $array_unidades_do_usuario = $repository->load($criteria);

        foreach ($array_unidades_do_usuario as $unidade)
        {
            $this->addSystemUnidades($unidade);
        }
        
        
        parent::load($id);
    }	
    
    
}

?>