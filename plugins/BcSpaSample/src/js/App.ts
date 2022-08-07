import Vue from 'vue';
import axios from "axios";
import { User } from './main';

type DataType = {
    accessToken?: string,
    mount: boolean,
    pageTitle: string,
    loginId?: number,
    loginName?: string,
    message?: string,
    isError: boolean,
    addLink?: string,
    isFlash: boolean
};

export default Vue.extend({
    /**
     * Name
     */
    name: 'App',
    /**
     * Data
     */
    data: (): DataType => {
        return {
            accessToken: undefined,
            mount: false,
            pageTitle: '　',
            loginId: undefined,
            loginName: undefined,
            message: undefined,
            isError: false,
            addLink: undefined,
            isFlash: false
        }
    },

    /**
     * Mounted
     */
    mounted() {
        if (this.accessToken || this.$route.path === '/') {
            this.mount = true
        }
        if (!localStorage.refreshToken) {
            if (this.$route.path !== '/') {
                this.$router.push('/')
            }
        } else {
            this.getToken(localStorage.refreshToken).then(() => {
                this.mount = true
                if (typeof this.accessToken === 'string') {
                    this.setUser(this.accessToken);
                }
            })
        }
        this.$router.beforeEach((to, from, next) => {
            if (!this.isFlash) {
                this.message = undefined
                this.isError = false
            } else {
                this.isFlash = false
            }
            this.addLink = undefined
            next()
        }).bind(this)
    },

    /**
     * Methods
     */
    methods: {
        /**
         * Set Title
         * @param title
         */
        setTitle: function (title: string): void {
            this.pageTitle = title
        },

        /**
         * Set Login
         * @param accessToken
         * @param refreshToken
         */
        setLogin: function (accessToken: string, refreshToken: string): void {
            this.setToken(accessToken, refreshToken)
            this.setUser(accessToken)
        },

        /**
         * Set User
         * @param accessToken
         */
        setUser: function (accessToken: string): void {
            axios.get('/baser/api/baser-core/users/view/1.json', {
                headers: {"Authorization": accessToken},
                data: {}
            }).then((response) => {
                const user: User = response.data.user;
                this.loginId = user.id
                this.loginName = user.name
            });
        },

        /**
         * Set Token
         * @param accessToken
         * @param refreshToken
         */
        setToken: function (accessToken: string, refreshToken: string): void {
            this.accessToken = accessToken
            localStorage.refreshToken = refreshToken
        },

        /**
         * Get Token
         * @param refreshToken
         * @returns {Promise<void>}
         */
        getToken: async function (refreshToken: string) {
            await axios.get('/baser/api/baser-core/users/refresh_token.json', {
                headers: {"Authorization": refreshToken},
                data: {}
            }).then((response) => {
                if (response.data) {
                    this.setToken(response.data.access_token, response.data.refresh_token)
                } else {
                    this.$router.push('/')
                }
            }).catch(function (error) {
                    if (error.response.status === 401) {
                        localStorage.refreshToken = ''
                    }
                })
        },

        /**
         * Remove Token
         */
        removeToken: function (): void {
            localStorage.refreshToken = undefined
            this.accessToken = undefined
        },

        /**
         * Logout
         */
        logout: function (): void {
            this.removeToken()
            this.loginId = undefined
            this.loginName = undefined
            this.$router.push('/')
        },

        /**
         * Set Message
         * @param message
         * @param isError
         * @param isFlash
         */
        setMessage: function (message: string, isError: boolean, isFlash: boolean): void {
            this.message = message
            this.isError = isError
            this.isFlash = isFlash
        },

        /**
         * Clear Message
         */
        clearMessage: function (): void {
            this.message = undefined
            this.isError = false
            this.isFlash = false
        },

        /**
         * Set Add Link
         * @param link
         */
        setAddLink: function (link: string): void {
            this.addLink = link
        }
    }
});
