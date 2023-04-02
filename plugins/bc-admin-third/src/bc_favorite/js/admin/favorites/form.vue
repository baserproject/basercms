<!--
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */
-->


<template>
    <form method="POST" id="FavoriteAjaxForm">
        <h2 class="bca-main__header-title">{{ windowTitle }}</h2>
        <input type="hidden" name="id" :value="id"/>
        <input type="hidden" name="user_id" :value="userId"/>
        <input type="hidden" name="_csrfToken"/>
        <dl>
            <dt><label for="FavoriteName">{{ labelTitle }}</label></dt>
            <dd>
                <span class="bca-textbox">
                    <input class="required" type="text" v-model="name" id="FavoriteName" :placeholder="labelTitle" size=30 name="name" @input="formUpdated" autofocus/>
                </span><br>
                <div class="invalid-feedback" v-if="$v.name.$invalid">{{ alertRequire }}</div>
            </dd>
            <dt><label for="FavoriteUrl"/>{{ labelUrl }}</dt>
            <dd>
                <span class="bca-textbox">
                    <input class="required" type="text" v-model="url" id="FavoriteUrl" :placeholder="labelUrl" size=30 name="url" @input="formUpdated"/>
                </span><br>
                <div class="invalid-feedback" v-if="$v.url.$invalid">{{ alertRequire }}</div>
            </dd>
        </dl>
    </form>
</template>

<script>

const {validationMixin, default: Vuelidate} = require('vuelidate')
const {required} = require('vuelidate/lib/validators')
import axios from "axios";

export default {
    /**
     * name
     */
    name: "FavoriteForm",

    /**
     * Data
     */
    data() {
        return {
            alertRequire: bcI18n.alertRequire,
            windowTitle: null,
            addTitle: bcI18n.addTitle,
            editTitle: bcI18n.editTitle,
            labelTitle: bcI18n.labelTitle,
            labelUrl: bcI18n.labelUrl,
            name: '',
            url: '',
            id: ''
        }
    },

    /**
     * Validations
     */
    validations: {
        name: {required},
        url: {required}
    },

    /**
     * props
     */
    props: [
        'userId',
        'currentPageName',
        'currentPageUrl',
        'currentFavorite',
    ],

    /**
     * Mounted
     */
    mounted() {
        if (this.currentFavorite) {
            this.id = this.currentFavorite.id;
            this.name = this.currentFavorite.name;
            this.url = this.currentFavorite.url;
            this.windowTitle = this.editTitle;
        } else {
            this.name = this.currentPageName;
            this.url = this.currentPageUrl;
            this.windowTitle = this.addTitle;
        }
    },

    /**
     * Methods
     */
    methods: {

        /**
         * Form Updated
         */
        formUpdated: function () {
            this.$emit("formUpdated", this.$v.$invalid);
        },

        /**
         * Form Submit
         */
        formSubmit: function () {
            let apiUrl;
            if (this.id) {
                apiUrl = $.bcUtil.apiAdminBaseUrl + "bc-favorite/favorites/edit/" + this.id + '.json';
            } else {
                apiUrl = $.bcUtil.apiAdminBaseUrl + "bc-favorite/favorites/add" + '.json';
            }
            let userId = this.userId;
            let name = this.name;
            let url = this.url;
            let favoriteForm = this
            $.bcToken.check(function () {
                $('#FavoriteAjaxForm input[name="_csrfToken"]').val($.bcToken.key);
                axios.post(apiUrl, {
                        user_id: userId,
                        name: name,
                        url: url
                    }, {
                        headers: {
                            "Authorization": $.bcJwt.accessToken,
                        }
                    }
                ).then(function (response) {
                    if (response.data) {
                        $("#Waiting").hide();
                        $.bcToken.key = null;
                        favoriteForm.$emit("formSubmitted");
                    }
                }.bind(this))
                    .catch(function (error) {
                        if (error.response) {
                            var errorMessage = error.response.data.message + "\n";
                            var errors = error.response.data.errors;
                            Object.keys(errors).forEach(function (key) {
                                Object.keys(errors[key]).forEach(function (subKey) {
                                    errorMessage = errorMessage + "\n" + errors[key][subKey];
                                });
                            });
                            alert(errorMessage);
                        } else {
                            console.log('Error', error.message);
                        }
                        $("#Waiting").hide();
                        $.bcToken.key = null;
                        favoriteForm.$emit("formSubmitted");

                        // TODO ucmitz
                        // if (XMLHttpRequest.responseText) {
                        //     alert(bcI18n.favoriteAlertMessage2 + '\n\n' + XMLHttpRequest.responseText);
                        // } else {
                        //     alert(bcI18n.favoriteAlertMessage2 + '\n\n' + XMLHttpRequest.statusText);
                        // }
                    });
            }, {useUpdate: false, hideLoader: false});
        }
    }
}

</script>
