/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

import Vue from 'vue'
import App from './App.vue'
import router from './router'


export type User = {
    id?: number,
    name?: string,
    real_name_1?: string,
    real_name_2?: string,
    email?: string,
    nickname?: string,
    created?: Date,
    modified?: Date,
    status?: boolean,
    user_groups?: number[],
    password_1?: string,
    password_2?: string,
    login_user_id?: number,
};

Vue.config.productionTip = false;

new Vue({
  router,
  render: h => h(App),
}).$mount('#app')
