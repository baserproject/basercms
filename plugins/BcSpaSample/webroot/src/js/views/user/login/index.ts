import Vue from 'vue';
import axios from 'axios';

const userLogin =  Vue.extend({
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
    data: () => {
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
        login,
    }
});

/**
 * Login
 */
function login() {
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

export {userLogin};
