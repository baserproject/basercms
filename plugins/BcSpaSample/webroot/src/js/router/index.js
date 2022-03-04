/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

import Vue from 'vue'
import Router from 'vue-router'
import Login from '../views/Login.vue'
import UserIndex from '../views/UserIndex.vue'
import UserEdit from '../views/UserEdit.vue'
import UserAdd from '../views/UserAdd.vue'

Vue.use(Router)

export default new Router({
    routes: [
        {
            path: '/',
            component: Login
        },
        {
            path: '/user_index',
            component: UserIndex
        },
        {
            path: '/user_edit/:id',
            component: UserEdit
        },
        {
            path: '/user_add',
            component: UserAdd
        }
    ]
})
