import Vue from 'vue';
import axios from 'axios';
import { User } from '../../main';

type UserGroup = {
    1?: string;
    2?: string;
};

type DataType = {
    user: User,
    userGroups: UserGroup,
    errors: string[]
};

export default Vue.extend({
    /**
     * Name
     */
    name: 'UserForm',

    /**
      * Props
      */
    props: {
        userId: Number,
        accessToken: String,
        loginUserId: Number,
    },

    /**
      * Data
      * @returns {{users: null}}
      */
    data: (): DataType => {
        return {
            user: {
                id: undefined,
                name: undefined,
                real_name_1: undefined,
                real_name_2: undefined,
                email: undefined,
                nickname: undefined,
                created: undefined,
                modified: undefined,
                status: undefined,
                user_groups: undefined,
                password_1: undefined,
                password_2: undefined,
                login_user_id: undefined,
            },
            userGroups: {},
            errors: []
        }
    },

    /**
      * Mounted
      */
    mounted() {
        if (this.accessToken) {
            axios.get('/baser/api/baser-core/user_groups/list.json', {
                headers: {"Authorization": this.accessToken}
            }).then((response) => {
                this.userGroups = response.data.userGroups
            });
        }
        this.load(this.userId);
    },

    /**
      * Methods
      */
    methods: {
        /**
          * Load
          * @param id
          */
        load: function (id: number): void {
            if (id) {
                axios.get('/baser/api/baser-core/users/view/' + id + '.json', {
                    headers: {"Authorization": this.accessToken}
                }).then((response) => {
                    this.user = response.data.user;
                    this.user.login_user_id = Number(response.data.user.login_user_id);
                    let userGroups:number[] = [];
                    this.user.user_groups?.forEach((userGroup) => {
                        userGroups.push(userGroup);
                    })
                    this.user.user_groups = userGroups;
                })
            } else {
                this.user = {
                    name: '',
                    real_name_1: '',
                    real_name_2: '',
                    nickname: '',
                    user_groups: [1],
                    email: '',
                    password_1: '',
                    password_2: '',
                }
            }
        },
        /**
          * Save
          */
        save: function (id?: number): void {
            this.$emit('clear-message');
            this.errors = [];
            let endPoint = '/baser/api/baser-core/users/';
            let user: User = {
                name:  this.user.name,
                real_name_1: this.user.name,
                real_name_2: this.user.real_name_2,
                nickname: this.user.nickname,
                user_groups: this.user.user_groups,
                email: this.user.email,
                password_1: this.user.password_1,
                password_2: this.user.password_2,
                login_user_id: this.loginUserId
            }
            if (id !== undefined) {
                endPoint += 'edit/' + id + '.json';
                user.id = id;
            } else {
                endPoint += 'add.json'
            }
            axios.post(endPoint, user, {
                headers: {"Authorization": this.accessToken}
            }).then((response) => {
                this.$emit('set-message', response.data.message, false)
                if (!id) {
                    this.$emit('set-message', response.data.message, false, true)
                    this.$router.push('/user_edit/' + response.data.user.id)
                } else {
                    this.$emit('set-message', response.data.message, false)
                }
            })
            .catch((error) => {
                this.$emit('set-message', error.response.data.message, true)
                if (error.response.status === 400) {
                    this.errors = error.response.data.errors
                }
            });
        },

        /**
          * Remove
          * @param id
          */
        remove: function (id: number): void {
            this.$emit('clear-message');
            if(!confirm('ユーザー情報を削除します。本当によろしいですか？')) {
                return;
            }
            axios.post('/baser/api/baser-core/users/delete/' + id + '.json', {}, {
                headers: {"Authorization": this.accessToken}
            }).then((response) => {
                this.$emit('set-message', response.data.message, false, true);
                this.$router.push('/user_index');
            }).catch((error) => {
                this.$emit('set-message', error.response.data.message, true);
            });
        }
    }
});
