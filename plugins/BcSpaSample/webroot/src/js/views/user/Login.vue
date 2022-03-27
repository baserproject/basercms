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
<script lang="ts" src="./Login.ts"></script>
