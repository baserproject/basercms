import Vue from 'vue';
import UserForm from '../../components/user/Form.vue';

export default Vue.extend({
        /**
         * Name
         */
        name: 'UserAdd',
        /**
          * Props
          */
        props: {
            accessToken: String
        },
        /**
          * Components
          */
        components: {
            UserForm
        },
        /**
          * Mounted
          */
        mounted: function (): void {
            this.$emit('set-title', 'ユーザー登録')
            if (!this.accessToken) {
                this.$router.push('/')
            }
        },
});
