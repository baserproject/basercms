import Vue from 'vue';
import axios, { AxiosError } from 'axios';


type DataType = {
    isError: boolean,
    message? : string,
    name? : string,
    password? : string
};

export default Vue.extend({
    /**
     * Name
     */
    name: 'UserLogin',
    /**
     * Props
     */
    props: {
        accessToken: String
    },
    /**
     * Data
     */
    data: (): DataType => {
        return {
            isError: false,
            message : undefined,
            name: undefined,
            password: undefined
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
        /**
         * Login
         */
        login: function(): void {
            this.isError = false;
            const postData = {
                email: this.name,
                password: this.password
            };
            axios.post('/baser/api/baser-core/users/login.json', postData)
            .then((response) => {
                if (response.data) {
                    this.$emit('set-login', response.data.access_token, response.data.refresh_token)
                    this.$router.push('user_index')
                }
            })
            .catch((error: AxiosError) => {
                if (error.response) {
                    if(error.response.status === 401) {
                        this.message = 'アカウント名、パスワードが間違っています。'
                    } else {
                        this.message = error.response.data.message
                    }
                }
            });
        }
    }
});
