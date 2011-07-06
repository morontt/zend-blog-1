<?php

class Application_Model_DbTable_Users extends Zend_Db_Table_Abstract
{
    protected $_name = 'users';

	public function getNameUsers()
	{
	    $data = $this->fetchAll();
		
		foreach($data as $value_cat)
		{
			$key = $value_cat->user_id;
			$arrayName[$key] = $value_cat->username;
		}
		
		return $arrayName;
	}
    
    public function getById($id)
    {
		$row = $this->fetchRow('user_id = '.$id);
        if (!$row) {
            throw new Exception("Count not find row $id");
        }
        return $row;
    }
    
    public function getAllUsers($page)
	{
		$select = $this->select()->order('username ASC');
        
        $paginator = Zend_Paginator::factory($select);
		
		$config = new Zend_Config_Ini('../application/configs/application.ini','production');
		$itemPerPage = $config->users->per->page;
		$paginator->setItemCountPerPage($itemPerPage);
		
		$paginator->SetCurrentPageNumber($page);
        
		return $paginator;
	}
    
	public function getSaltPassword($login)
	{
		$row = $this->fetchRow($this->select()->where('login = ?',$login));
		
		if ($row) :
		    $result = $row->password_salt;
		else :
		    $result = '?';
		endif;
		
        return $result;
	}
	
    public function loginUser($data)
    {
        $auth = Zend_Auth::getInstance();
        $authAdapter = new Zend_Auth_Adapter_DbTable($this->getAdapter(), 'users');
				
		$passwordMd5 = md5($data['password']);
		$salt = $this->getSaltPassword($data['username']);
		$passwordMd5 = md5($passwordMd5 . $salt);
		//echo $passwordmd5;
		//echo $salt;
				
		$authAdapter->setIdentityColumn('login')
                    ->setCredentialColumn('password')
                    ->setIdentity($data['username'])
                    ->setCredential($passwordMd5);
						
        $result = $auth->authenticate($authAdapter);
        if ($result->isValid())
		{
            $storage = new Zend_Auth_Storage_Session();
            $storage->write($authAdapter->getResultRowObject(array('user_id',
																  'username',
																	 'login',
                                                                 'user_type',)));
            $rez = TRUE;
        } else
        {
            $rez = FALSE;
        }
        
        return $rez;
    }
    
	public function signinNewUser($data)
	{
		$dynamicSalt = chr(rand(48, 122));
		for ($i = 0; $i < 10; $i++)
		$dynamicSalt .= chr(rand(48, 122));
		
		$dynamicSalt = md5($dynamicSalt);
		$password = md5(md5($data['password']) . $dynamicSalt);
		
		$newRecordDb = array('username' => $data['nickname'],
		                        'login' => $data['username'],
                        'password_salt' => $dynamicSalt,
                             'password' => $password,
					        'user_type' => 'member',
                         'time_created' => date('Y-m-d H:i:s')
		);
				
        return $this->insert($newRecordDb);
	}

    public function editUser($id, $type)
    {
        $data = array(
               'user_type' => $type,
        );
        $this->update($data, 'user_id = ' . (int)$id);
    }
    
    public function deleteUser($id)
    {
        if ($id != 1)
        {
            $del = $this->delete('user_id = ' . (int)$id);
            //if (!$del)
            //{
            //    throw new Exception("Count not find row $id");
            //}
        }
    }
    
    public function latestActivity($id)
    {
        $data = array('time_last' => date('Y-m-d H:i:s'));
        $this->update($data, 'user_id = ' . $id);
    }
    
}
