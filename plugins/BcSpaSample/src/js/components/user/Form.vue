<template>
    <section id="UserForm">

        <table id="FormTable" class="form-table bca-form-table">
            <tbody>
            <tr>
                <th class="col-head bca-form-table__label"><label for="id">No</label></th>
                <td class="col-input bca-form-table__input">
                    {{ user.id }}
                </td>
            </tr>
            <tr>
                <th class="col-head bca-form-table__label">
                    <label for="name">アカウント名</label> &nbsp;
                    <span class="bca-label" data-bca-label-type="required">必須</span>
                </th>
                <td class="col-input bca-form-table__input">
                <span class="bca-textbox">
                    <input type="text"
                           name="name"
                           size="20"
                           maxlength="255"
                           autofocus="autofocus"
                           class="bca-textbox__input"
                           required="required"
                           id="name" v-model="user.name">
                </span>
                    <div class="error-wrap" v-if="errors.name">
                        <ul>
                            <li class="error-message" v-for="(message) in errors.name" :key="message">{{ message }}</li>
                        </ul>
                    </div>
                </td>
            </tr>
            <tr>
                <th class="col-head bca-form-table__label">
                    <label for="real-name-1">名前</label> &nbsp;
                    <span class="bca-label" data-bca-label-type="required">必須</span>
                </th>
                <td class="col-input bca-form-table__input">
                    <small>[姓]</small>
                    <span class="bca-textbox">
                    <input type="text"
                           name="real_name_1"
                           size="12"
                           maxlength="255"
                           class="bca-textbox__input"
                           required="required"
                           id="real-name-1"
                           v-model="user.real_name_1">
                </span>
                    <small>[名]</small>
                    <span class="bca-textbox">
                    <input type="text"
                           name="real_name_2"
                           size="12"
                           maxlength="255"
                           class="bca-textbox__input"
                           id="real-name-2"
                           v-model="user.real_name_2">
                </span>
                    <div class="error-wrap" v-if="errors.real_name_1">
                        <ul>
                            <li class="error-message" v-for="(message, id) in errors.real_name_1" :key="message-id">{{ message }}</li>
                        </ul>
                    </div>
                </td>
            </tr>
            <tr>
                <th class="col-head bca-form-table__label">
                    <label for="nickname">ニックネーム</label>
                </th>
                <td class="col-input bca-form-table__input">
                <span class="bca-textbox">
                    <input type="text"
                           name="nickname"
                           size="40"
                           maxlength="255"
                           class="bca-textbox__input"
                           id="nickname"
                           v-model="user.nickname">
                </span>
                </td>
            </tr>
            <tr>
                <th class="col-head bca-form-table__label">
                    <label>グループ</label> &nbsp;
                    <span class="bca-label" data-bca-label-type="required">必須</span>
                </th>
                <td class="col-input bca-form-table__input">
                <span class="bca-checkbox-group">
                    <input type="hidden" name="user_groups[_ids]" value=""/>
                    <span v-for="(title, id) in this.userGroups" class="bca-checkbox" :key="title">
                        <input type="checkbox"
                               name="user_groups[_ids][]"
                               :value="id"
                               :id="'user-groups-ids-' + id"
                               class="bca-checkbox__input"
                               v-model="user.user_groups">
                        <label class="bca-checkbox__label" :for="'user-groups-ids-' + id">
                            {{ title }}
                        </label>
                    </span>
                </span>
                    <div class="error-wrap" v-if="errors.user_groups">
                        <ul>
                            <li class="error-message" v-for="(message) in errors.user_groups" :key="message">{{ message }}</li>
                        </ul>
                    </div>
                </td>
            </tr>
            <tr>
                <th class="col-head bca-form-table__label">
                    <label for="email">Eメール</label> &nbsp;
                    <span class="bca-label" data-bca-label-type="required">必須</span>
                </th>
                <td class="col-input bca-form-table__input">
                    <input type="text" name="dummy-email" style="top:-100px;left:-100px;position:fixed;">
                    <span class="bca-textbox">
                    <input type="text"
                           name="email"
                           size="40"
                           maxlength="255"
                           class="bca-textbox__input"
                           required="required"
                           id="email"
                           v-model="user.email">
                </span>
                    <div class="error-wrap" v-if="errors.email">
                        <ul>
                            <li class="error-message" v-for="(message, id) in errors.email" :key="message-id">{{ message }}</li>
                        </ul>
                    </div>
                </td>
            </tr>
            <tr>
                <th class="col-head bca-form-table__label">
                    <label for="password-1">パスワード</label></th>
                <td class="col-input bca-form-table__input">
                    <div v-if="userId"><small>
                        [パスワードは変更する場合のみ入力してください]</small><br></div>
                    <input type="password" name="dummy-pass" autocomplete="off"
                           style="top:-100px;left:-100px;position:fixed;">
                    <span class="bca-textbox">
                    <input type="password"
                           name="password_1"
                           size="20"
                           maxlength="255"
                           autocomplete="off"
                           class="bca-textbox__input"
                           id="password-1"
                           v-model="user.password_1">
                </span>
                    <span class="bca-textbox">
                    <input type="password"
                           name="password_2"
                           size="20"
                           maxlength="255"
                           autocomplete="off"
                           class="bca-textbox__input"
                           id="password-2"
                           v-model="user.password_2">
                </span>
                    <div class="error-wrap" v-if="errors.password">
                        <ul>
                            <li class="error-message" v-for="(message, id) in errors.password" :key="message-id">{{ message }}</li>
                        </ul>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>

        <div class="submit section bca-actions">
            <router-link :to="{ path: '/user_index' }" class="bca-btn bca-actions__item" data-bca-btn-type="back-to-list">
                一覧に戻る
            </router-link>
            <div class="bca-actions__main">
                <button class="button bca-btn bca-actions__item"
                        data-bca-btn-type="save"
                        data-bca-btn-size="lg"
                        data-bca-btn-width="lg"
                        id="BtnSave"
                        type="submit"
                        @click="save(userId)">
                    保存
                </button>
            </div>
            <div class="bca-actions__sub" v-if="userId">
                <a href="#"
                    @click="remove(userId)"
                    class="submit-token bca-btn bca-actions__item"
                    data-bca-btn-type="delete"
                    data-bca-btn-size="sm"
                    data-bca-btn-color="danger">
                    削除
                </a>
            </div>
        </div>

    </section>
</template>
<script lang="ts" src="Form.ts"></script>
