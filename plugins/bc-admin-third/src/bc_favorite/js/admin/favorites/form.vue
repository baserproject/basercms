<template>
    <form method="POST" id="FavoriteAjaxForm">
        <input type="hidden" name="id" :value="id"/>
        <input type="hidden" name="user_id" :value="userId"/>
        <input type="hidden" name="_csrfToken"/>
        <dl>
            <!-- TDDO: ucmitz favorite-nameをnameに変更する? -->
            <dt><label for="favorite-name">{{ i18Title }}</label></dt>
            <dd>
                <span class="bca-textbox">
                    <input class="required" type="text" v-model="name" id="FavoriteName" placeholder="タイトル" size=30 name="name" @input="formUpdated"/>
                </span>
                <div class="invalid-feedback" v-if="$v.name.$invalid" style="color:red">必須です</div>
            </dd>
            <dt><label for="favorite-url"/>{{ i18Url }}</dt>
            <dd>
                <span class="bca-textbox">
                    <input class="required" type="text" v-model="url" id="FavoriteUrl" placeholder="URL" size=30 name="url" @input="formUpdated"/>
                </span>
                <div class="invalid-feedback" v-if="$v.url.$invalid" style="color:red">必須です</div>
            </dd>
        </dl>
    </form>
</template>

<script>

const { validationMixin, default: Vuelidate } = require('vuelidate')
const { required } = require('vuelidate/lib/validators')
import axios from "axios";

export default {
    name: "FavoriteForm",
    data() {
        return {
            i18Title: 'title',
            i18Url: 'url',
            name: '',
            url: '',
            id: ''
        }
    },
    validations: {
        name: {required},
        url: {required}
    },
    props: ['userId', 'currentPageName', 'currentPageUrl', 'currentFavorite'],
    mounted() {
        if(this.currentFavorite) {
            this.id = this.currentFavorite.id;
            this.name = this.currentFavorite.name;
            this.url = this.currentFavorite.url;
        } else {
            this.name = this.currentPageName;
            this.url = this.currentPageUrl;
        }
    },
    methods: {
        formUpdated: function () {
            this.$emit("formUpdated", this.$v.$invalid);
        },
        formSubmit: function () {
            let apiUrl;
            if(this.id) {
                apiUrl = $.bcUtil.apiBaseUrl + "bc-favorite/favorites/edit/" + this.id + '.json';
            } else {
                apiUrl = $.bcUtil.apiBaseUrl + "bc-favorite/favorites/add" + '.json';
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
