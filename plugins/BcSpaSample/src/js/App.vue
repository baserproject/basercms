<template>

    <div id="Page" class="bca-app">

        <header id="Header" class="bca-header">

            <div id="ToolBar" class="bca-toolbar">
                <div id="ToolbarInner" class="clearfix bca-toolbar__body">

                    <div class="bca-toolbar__logo">
                        <a href="/" class="bca-toolbar__logo-link">
                            <img src="/bc_admin_third/img/admin/logo_icon.svg" alt="" width="24" height="21"
                                 class="bca-toolbar__logo-symbol"/>
                            <span class="bca-toolbar__logo-text">baserCMS</span>
                        </a>
                    </div>

                    <div id="ToolMenu" class="bca-toolbar__tools">
                        <div class="bca-toolbar__tools-mode">
                        </div>
                    </div>

                    <div id="UserMenu" class="bca-toolbar__users" v-show="loginId">
                        <ul class="clearfix">
                            <li>
                                <a href="javascript:void(0)" class="title">
                                    {{ loginName }}
                                    <img src="/bc_admin_third/img/admin/btn_dropdown.png" width="8" height="11"
                                         class="bc-btn" alt=""/>
                                </a>
                                <ul>
                                    <li>
                                        <router-link :to="{ path: '/user_edit' + '/' + loginId }">アカウント設定</router-link>
                                    </li>
                                    <li><a href="javascript:void(0)" @click="logout()">ログアウト</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
        </header>

        <div id="Wrap" class="bca-container">

            <main id="Contents" class="bca-main">

                <article id="ContentsBody" class="contents-body bca-main__body">

                    <div class="bca-main__header">
                        <h1 class="bca-main__header-title">{{ pageTitle }}</h1>
                        <div class="bca-main__header-actions" v-if="addLink">
                            <router-link title="新規追加" class="bca-btn" data-bca-btn-type="add" data-bca-btn-size="sm" :to="{ path: addLink }">
                                新規追加
                            </router-link>
                        </div>
                    </div>

                    <div id="MessageBox" class="message-box" v-if="message">
                        <div id="flashMessage"
                             class="message"
                             :class="{ 'alert-message' : isError, 'notice-message' : !isError }">
                            {{ message }}
                        </div>
                    </div>

                    <div class="bca-main__contents clearfix">
                        <router-view
                            v-if="mount"
                            :accessToken="accessToken"
                            :loginUserId="loginId"
                            @set-login="setLogin"
                            @set-title="setTitle"
                            @set-message="setMessage"
                            @clear-message="clearMessage"
                            @set-add-link="setAddLink"
                        >
                        </router-view>
                    </div>

                    <!-- / bca-main__body --></article>
                <!-- / .bca-main --></main>
            <!-- / #Wrap --></div>

        <div id="Footer" class="bca-footer" data-loggedin="">
            <div class="bca-footer__inner--full">
                <div class="bca-footer__main">
                    <div class="bca-footer__baser-version">baserCMS</div>
                    <ul class="bca-footer__banner">
                        <li class="bca-footer__banner__item"></li>
                        <li class="bca-footer__banner__item"></li>
                    </ul>
                </div>
                <div class="bca-footer__sub">
                    <div class="bca-footer__copyright">Copyright &copy; baserCMS Users Community All rights reserved.
                    </div>
                </div>
            </div>
        </div>

        <!-- / #Page --></div>

</template>

<script lang="ts" src="./App.ts"></script>
