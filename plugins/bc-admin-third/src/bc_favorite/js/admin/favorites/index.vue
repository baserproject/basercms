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
    <div id="FavoriteListWrap">
        <h2 class="bca-nav-favorite-title">
            <button
                type="button"
                id="btn-favorite-expand"
                class="bca-collapse__btn bca-nav-favorite-title-button"
                data-bca-collapse="favorite-collapse"
                data-bca-target="#favoriteBody"
                :aria-expanded="ariaExpanded"
                aria-controls="favoriteBody" @click="changeOpenFavorite"
            >
                {{ i18nFavorite }}
                <i class="bca-icon--chevron-down bca-nav-favorite-title-icon"></i>
            </button>
        </h2>
        <ul
            v-if="favorites.length"
            :style="'display:' + favoriteBoxOpened"
            class="favorite-menu-list bca-nav-favorite-list bca-collapse"
            id="favoriteBody"
        >
            <li v-for="(favorite, i) in favorites"
                :key="i" :id="'FavoriteRow' + favorite.id"
                :data-id="favorite.id"
                class="bca-nav-favorite-list-item"
                @mousedown="changeSelected(favorite)"
            >
                <a :href="baseUrl + favorite.url" :title="favorite.url">
                    <span class="bca-nav-favorite-list-item-label">{{ favorite.name }}</span>
                </a>
            </li>
        </ul>

        <ul :style="'display:' + favoriteBoxOpened"
            v-else class="favorite-menu-list bca-nav-favorite-list bca-collapse"
            id="favoriteBody"
        >
            <li class="no-data"><small>{{ i18nNoData }}</small></li>
        </ul>

        <div id="FavoriteDialog" class="ui-widget">
            <modal ref="modalFavoriteForm" :scrollable="false" hidden>
                <favorite-form
                    ref="FavoriteForm"
                    :user-id="userId"
                    :current-page-url="currentPageUrl"
                    :current-page-name="currentPageName"
                    :current-favorite="currentFavorite"
                    @formUpdated="formUpdated"
                    @formSubmitted="formSubmitted"
                />
                <template slot="footer">
                    <button class="bca-btn" type="button" @click="$refs.modalFavoriteForm.closeModal()">{{ buttonCancel }}</button>&nbsp;
                    <button class="bca-btn" type="button" @click="$refs.FavoriteForm.formSubmit()" :disabled="formError">
                        {{ buttonSubmit }}
                    </button>
                </template>
            </modal>
        </div>
    </div>
</template>


<script>
import axios from "axios";
import FavoriteForm from "./form.vue";
import Modal from "../../../../js/common/modal.vue";

export default {

    /**
     * Data
     */
    data: function () {
        return {
            favoriteBoxOpened: "none",
            buttonSubmit: bcI18n.buttonSubmit,
            buttonCancel: bcI18n.buttonCancel,
            i18nFavorite: bcI18n.i18nFavorite,
            i18nNoData: bcI18n.i18nNoData,
            i18nEdit: bcI18n.i18nEdit,
            i18nDelete: bcI18n.i18nDelete,
            favorites: [],
            registerUrl: $.bcUtil.apiAdminBaseUrl + "bc-favorite/favorites/add.json",
            ariaExpanded: 'true',
            baseUrl: $.bcUtil.baseUrl,
            formError: false,
            favorite: {},
            currentFavorite: null
        }
    },

    /**
     * Props
     */
    props: ['userId', 'currentPageName', 'currentPageUrl'],

    /**
     * Components
     */
    components: {
        FavoriteForm,
        Modal
    },

    /**
     * Mounted
     */
    mounted: function () {
        this.initFavorite();
    },

    /**
     * Methods
     */
    methods: {

        /**
         * Form Updated
         * @param formError
         */
        formUpdated: function (formError) {
            this.formError = formError;
        },

        /**
         * initFavorite
         */
        initFavorite: function () {
            // 一覧呼び出し
            this.refresh();
            // 開閉
            var url = $.bcUtil.apiAdminBaseUrl + "bc-favorite/favorites/get_favorite_box_opened.json";
            axios.get(url, {
                headers: {
                    "Authorization": $.bcJwt.accessToken,
                }
            }).then(function (response) {
                if (response.data.result === "1") {
                    this.favoriteBoxOpened = "block";
                    this.ariaExpanded = 'false';
                } else {
                    this.favoriteBoxOpened = 'none';
                    this.ariaExpanded = 'true';
                }
            }.bind(this));
            $.contextMenu({
                selector: '.favorite-menu-list li',
                items: {
                    "FavoriteEdit": {name: bcI18n.i18nEdit, icon: "edit"},
                    "FavoriteDelete": {name: bcI18n.i18nDelete, icon: "delete"}
                },
                callback: this.contextMenuClickHandler
            });

        },

        /**
         * Change Selected
         *
         * @param favorite
         */
        changeSelected: function (favorite) {
            this.currentFavorite = favorite;
        },

        /**
         * Change Open Favorite
         */
        changeOpenFavorite: function () {
            const baseUrl = $.bcUtil.apiAdminBaseUrl + "bc-favorite/favorites/save_favorite_box";
            if (this.favoriteBoxOpened === 'block') {
                // ボタンの制御
                this.favoriteBoxOpened = 'none';
                this.ariaExpanded = 'true';
                axios.post(baseUrl + '.json', {}, {
                    headers: {
                        "Authorization": $.bcJwt.accessToken,
                    }
                });
            } else {
                // ボタンの制御
                this.favoriteBoxOpened = 'block';
                this.ariaExpanded = 'false';
                axios.post(baseUrl + '/1.json', {}, {
                    headers: {
                        "Authorization": $.bcJwt.accessToken,
                    }
                });
            }
        },

        /**
         * Refresh
         */
        refresh: function () {
            // 一覧呼び出し
            const indexUrl = $.bcUtil.apiAdminBaseUrl + "bc-favorite/favorites/index.json";
            axios.get(indexUrl, {
                headers: {
                    "Authorization": $.bcJwt.accessToken,
                }
            }).then(function (response) {
                this.favorites = response.data.favorites;
            }.bind(this));
        },

        /**
         * モーダルを開く
         * @param index
         */
        openModal: function (index) {
            this.$refs.modalFavoriteForm.openModal(index);
        },

        /**
         * Form Submitted
         */
        formSubmitted: function () {
            this.refresh();
            this.$refs.modalFavoriteForm.closeModal();
        },

        /**
         * Context Menu Click Handler
         * @param key
         * @param options
         * @param res
         * @returns {boolean}
         */
        contextMenuClickHandler: function (key, options, res) {
            switch (key) {
                case 'FavoriteEdit':
                    this.openModal();
                    break;
                case 'FavoriteDelete':
                    if (!confirm(bcI18n.commonConfirmDeleteMessage)) return false;
                    var id = this.currentFavorite.id;
                    $.bcToken.check(function () {
                        $("#Waiting").show();
                        axios.post($.bcUtil.apiAdminBaseUrl + "bc-favorite/favorites/delete/" + id + ".json", {}, {
                            headers: {
                                "Authorization": $.bcJwt.accessToken,
                            }
                        }).then(function (response) {
                            if (response.status === 200) {
                                $('#FavoriteRow' + id).fadeOut(300, function () {
                                    $(this).remove();
                                });
                            } else {
                                alert(bcI18n.alertServerError);
                            }
                            $("#Waiting").hide();
                        }.bind(this))
                            .catch((error) => {
                                alert(bcI18n.alertServerError);
                                $("#Waiting").hide();
                            });
                    }, {hideLoader: false});

                    break;
            }
        }
    },

}
</script>
