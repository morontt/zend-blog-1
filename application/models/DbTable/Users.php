<?php

class Application_Model_DbTable_Users extends Zend_Db_Table_Abstract
{
    protected $_name = 'users';

	public function getNameUsers()
	{
	    $data = $this->fetchAll();
		
		foreach($data as $value_cat) {
			$key = $value_cat->user_id;
			$arrayName[$key] = $value_cat->username;
		}
		
		return $arrayName;
	}
    
    public function getById($id)
    {
		$row = $this->fetchRow('user_id = ' . $id);
        
        return $row;
    }
    
    public function getAllUsers()
	{
		$select = $this->select()->order('username ASC');
        
        $paginator = Zend_Paginator::factory($select);
		
		return $paginator;
	}
    
	public function getSaltPassword($login)
	{
		$row = $this->fetchRow($this->select()->where('login = ?', $login));
		
		if ($row) {
		    $result = $row->password_salt;
		} else {
		    $result = '?';
		}
		
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
        if ($result->isValid()) {
            $storage = new Zend_Auth_Storage_Session();
            $storage->write($authAdapter->getResultRowObject(array('user_id',
																  'username',
																	 'login',
                                                                 'user_type',)));
            Zend_Session::rememberMe(1209600);
            $rez = TRUE;
        } else {
            $rez = FALSE;
        }
        
        return $rez;
    }
    
	public function signinNewUser($data)
	{
		$dynamicSalt = chr(rand(48, 122));
		for ($i = 0; $i < 10; $i++) {
            $dynamicSalt .= chr(rand(48, 122));
        }
		
		$dynamicSalt = md5($dynamicSalt);
		$password = md5(md5($data['password']) . $dynamicSalt);
		
		$newRecordDb = array('username'      => $data['nickname'],
		                     'login'         => $data['username'],
                             'password_salt' => $dynamicSalt,
                             'password'      => $password,
					         'user_type'     => 'member',
                             'ip_addr'       => $_SERVER['REMOTE_ADDR'],
                             'time_created'  => date('Y-m-d H:i:s'));
				
        return $this->insert($newRecordDb);
	}

    public function editUser($id, $type)
    {
        $data = array(
               'user_type' => $type);
        
        $this->update($data, 'user_id = ' . $id);
    }
    
    public function deleteUser($id)
    {
        if ($id != 1) {
            $del = $this->delete('user_id = ' . $id);
        }
    }
    
    public function latestActivity($id)
    {
        $data = array('time_last' => date('Y-m-d H:i:s'),
                      'ip_last'   => $_SERVER['REMOTE_ADDR']);
        $this->update($data, 'user_id = ' . $id);
    }

    public function getHashByLogin($login)
    {
        $row = $this->fetchRow($this->select()->where('login = ?', $login));

        if ($row) {
            $hash = md5(md5($row->login) . $row->password_salt);
            $userId = $row->user_id;
            $result = array('id'   => $userId,
                            'hash' => $hash);
        } else {
            $result = false;
        }

        return $result;
    }

    public function getHashById($id)
    {
        $row = $this->fetchRow($this->select()->where('user_id = ?', $id));

        if ($row) {
            $result = md5(md5($row->login) . $row->password_salt);
        } else {
            $result = false;
        }

        return $result;
    }

    public function recoveryPassword($id, $password)
    {
        $row = $this->fetchRow($this->select()->where('user_id = ?', $id));

        if ($row) {
            $salt = $row->password_salt;
            $password = md5(md5($password) . $salt);
            $data = array('password' => $password);
            $this->update($data, "user_id = $id");
            $result = TRUE;
        } else {
            $result = false;
        }

        return $result;
    }
    
}
