<template>
    <div id="FavoriteListWrap">
        <h2 class="bca-nav-favorite-title">
            <button type="button" id="btn-favorite-expand" class="bca-collapse__btn bca-nav-favorite-title-button"
                    data-bca-collapse="favorite-collapse" data-bca-target="#favoriteBody" :aria-expanded="ariaExpanded"
                    aria-controls="favoriteBody" @click="changeOpenFavorite">
                {{ i18Favorite }} <i class="bca-icon--chevron-down bca-nav-favorite-title-icon"></i>
            </button>
        </h2>
        <ul v-if="favorites.length" :style="'display:' + favoriteBoxOpened" class="favorite-menu-list bca-nav-favorite-list bca-collapse" id="favoriteBody">
            <li v-for="(favorite, i) in favorites" :key="i" :id="'FavoriteRow' + favorite.id" :data-id="favorite.id" class="bca-nav-favorite-list-item" @mousedown="changeSelected(favorite)">
                <a :href="baseUrl + favorite.url" :title="favorite.url"><span class="bca-nav-favorite-list-item-label">{{ favorite.name }}</span></a>
            </li>
        </ul>

        <ul :style="'display:' + favoriteBoxOpened" v-else class="favorite-menu-list bca-nav-favorite-list bca-collapse" id="favoriteBody">
            <li class="no-data"><small>{{ i18NoData }}</small></li>
        </ul>

        <div id="FavoriteDialog" title="お気に入り登録" class="ui-widget">
            <modal ref="modalFavoriteForm" :scrollable="false" hidden>
                <favorite-form ref="FavoriteForm"
                    :user-id="userId"
                    :current-page-url="currentPageUrl"
                    :current-page-name="currentPageName"
                    :current-favorite="currentFavorite"
                    @formUpdated="formUpdated"
                    @formSubmitted="formSubmitted"
                />
                <template slot="footer">
                    <button class="bca-btn" type="button" @click="$refs.modalFavoriteForm.closeModal()">キャンセル</button>&nbsp;
                    <button class="bca-btn" type="button" @click="$refs.FavoriteForm.formSubmit()" :disabled="formError">
                        確定
                    </button>
                </template>
            </modal>
        </div>

        <ul id="FavoritesMenu" class="context-menu" style="display:none">
            <li class="edit"><a href="#FavoriteEdit">{{ i18Edit }}</a></li>
            <li class="delete"><a href="#FavoriteDelete">{{ i18Delete }}</a></li>
        </ul>
    </div>
</template>


<script>
import axios from "axios";
import FavoriteForm from "./form.vue";
import Modal from '../../common/modal.vue';

export default {
    data: function () {
        return {
            favoriteBoxOpened: "none",
            i18Favorite: 'お気に入り',
            i18NoData: 'nodata',
            i18Title: 'title',
            i18Url: 'url',
            i18Edit: 'edit',
            i18Delete: 'delete',
            favorites: [],
            registerUrl: $.bcUtil.apiBaseUrl + "bc-favorite/favorites/add.json",
            ariaExpanded: 'true',
            baseUrl: $.bcUtil.baseUrl,
            formError: false,
            favorite: {},
            currentFavorite: null
        }
    },
    props: ['userId', 'currentPageName', 'currentPageUrl'],
    components: {
        FavoriteForm,
        Modal
    },
    mounted: function () {
        this.initFavorite();
    },
    /**
     * Methods
     */
    methods: {
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
            var url = $.bcUtil.apiBaseUrl + "bc-favorite/favorites/get_favorite_box_opened.json";
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
                    "FavoriteEdit": {name: "編集", icon: "edit"},
                    "FavoriteDelete": {name: "削除", icon: "delete"}
                },
                callback: this.contextMenuClickHandler
            });

        },

        changeSelected: function(favorite) {
            this.currentFavorite = favorite;
        },

        changeOpenFavorite: function () {
            if (this.favoriteBoxOpened == 'block') {
                // ボタンの制御
                this.favoriteBoxOpened = 'none';
                this.ariaExpanded = 'true';
                var url = $.bcUtil.apiBaseUrl + "bc-favorite/favorites/save_favorite_box.json";
                axios.post(url, {}, {
                    headers: {
                        "Authorization": $.bcJwt.accessToken,
                    }
                });
            } else {
                // ボタンの制御
                this.favoriteBoxOpened = 'block';
                this.ariaExpanded = 'false';
                var url = $.bcUtil.apiBaseUrl + "bc-favorite/favorites/save_favorite_box/1.json";
                axios.post(url, {}, {
                    headers: {
                        "Authorization": $.bcJwt.accessToken,
                    }
                });
            }
        },
        refresh: function () {
            // 一覧呼び出し
            const indexUrl = $.bcUtil.apiBaseUrl + "bc-favorite/favorites/index.json";
            axios.get(indexUrl, {
                headers: {
                    "Authorization": $.bcJwt.accessToken,
                }
            }).then(function (response) {
                this.favorites = response.data.favorites;
            }.bind(this));
        },
        openModal: function (index) {
            this.$refs.modalFavoriteForm.openModal(index);
        },
        formSubmitted: function () {
            this.refresh();
            this.$refs.modalFavoriteForm.closeModal();
        },
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
                        axios.post($.bcUtil.apiBaseUrl + "bc-favorite/favorites/delete/" + id + ".json", {}, {
                            headers: {
                                "Authorization": $.bcJwt.accessToken,
                            }
                        }).then(function (response) {
                            if (response.status === 200) {
                                $('#FavoriteRow' + id).fadeOut(300, function () {
                                    $(this).remove();
                                });
                            } else {
                                alert("サーバーでの処理に失敗しました。");
                            }
                            $("#Waiting").hide();
                        }.bind(this))
                        .catch((error) => {
                            alert("サーバーでの処理に失敗しました。");
                            $("#Waiting").hide();
                        });
                    }, {hideLoader: false});

                    break;
            }
        }
    },

}
</script>
