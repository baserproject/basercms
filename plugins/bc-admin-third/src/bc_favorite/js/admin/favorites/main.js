/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

// import Vue from 'vue'
import Vue from 'vue/dist/vue.js'
import FavoriteIndex from './index.vue'
import Vuelidate from 'vuelidate'

Vue.use(Vuelidate)

$(function () {
	new Vue({
		el : '#FavoriteMenu',
		components: {
			FavoriteIndex
		},
	})
})

