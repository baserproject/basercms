<?php
namespace BaserCore\Service;
use Cake\ORM\TableRegistry;

class UsersService implements UsersServiceInterface
{

    public $Users;

    public function __construct()
    {
        $this->Users = TableRegistry::getTableLocator()->get('BaserCore.Users');
    }

    public function adminList($get, $paginate = [])
    {
        $query = $this->Users->find('all', $paginate);
        if (!empty($get['user_group_id'])) {
            $query->matching('UserGroups', function($q) use ($get) {
                return $q->where(['UserGroups.id' => $get['user_group_id']]);
            });
        }
        return $query;
    }

}
