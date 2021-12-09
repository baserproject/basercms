<template>
    <div id="LoginInner">
        <div v-if="isError" id="MessageBox" class="message-box">
            <div id="flashMessage" class="message alert-message">{{ message }}</div>
        </div>
        <h1 class="bca-login__title">
            <img src="/bc_admin_third/img/admin/logo_large.png" alt="管理システムログイン" class="bca-login__logo"/>
        </h1>
        <div id="AlertMessage" class="message" hidden></div>
        <div class="login-input bca-login-form-item">
            <label for="UserName">アカウント名</label>
            <span class="bca-textbox required">
                <input name="data[User][name]"
                       v-model="name"
                       tabindex="1"
                       autofocus="autofocus"
                       class="bca-textbox__input"
                       maxlength="255"
                       type="text"
                       id="UserName"
                       required="required"/>
            </span>
        </div>
        <div class="login-input bca-login-form-item">
            <label for="UserPassword">パスワード</label>
            <span class="bca-textbox required">
                <input
                    v-model="password"
                    name="data[User][password]"
                    tabindex="2"
                    class="bca-textbox__input"
                    type="password"
                    id="UserPassword"
                    required="required"/>
            </span>
        </div>
        <div class="submit bca-login-form-btn-group">
            <button
                @click="login()"
                type="submit"
                class="bca-btn--login bca-btn"
                data-bca-btn-type="login"
                id="BtnLogin"
                tabindex="4">
                ログイン
            </button>
        </div>
    </div>
</template>

<script>

import axios from 'axios'

export default {
    /**
     * Name
     */
    name: 'Login',

    /**
     * Props
     */
    props: {
        accessToken: String
    },

    /**
     * Data
     * @returns {{password: null, isError: boolean, name: null}}
     */
    data: function () {
        return {
            isError: false,
            message : null,
            name: null,
            password: null
        }
    },

    /**
     * Mounted
     */
    mounted() {
        this.$emit('set-title', 'ログイン')
    },

    /**
     * Methods
     */
    methods: {

        /**
         * Login
         */
        login: function () {
            this.isError = false
            axios.post('/baser/api/baser-core/users/login.json', {
                email: this.name,
                password: this.password
            }).then(function (response) {
                if (response.data) {
                    this.$emit('set-login', response.data.access_token, response.data.refresh_token)
                    this.$router.push('user_index')
                }
            }.bind(this))
            .catch(function (error) {
                this.isError = true
                if(error.response.status === 401) {
                    this.message = 'アカウント名、パスワードが間違っています。'
                } else {
                    this.message = error.response.data.message
                }
            }.bind(this))
        }
    }
}

</script>
