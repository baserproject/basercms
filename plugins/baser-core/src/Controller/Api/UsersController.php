<?php
namespace BaserCore\Controller\Api;
use BaserCore\Controller\AppController;
use BaserCore\Service\UsersServiceInterface;

class UsersController extends AppController {
    /**
     * http://localhost/baser/api/users/index.json で呼び出す
     * @param UsersServiceInterface $users
     */
    public function index(UsersServiceInterface $users)
    {
        $this->paginate = [
            'limit' => $this->request->getQuery('num'),
            'contain' => ['UserGroups']
        ];
        $this->set([
            'users' => $this->paginate($users->adminList($this->request->getQuery(), $this->paginate))
        ]);
        $this->viewBuilder()->setOption('serialize', ['users']);
    }

}
