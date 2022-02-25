import Vue from 'vue';
import UserForm from '../../components/UserForm.vue';

export default Vue.extend({
    /**
     * Name
     */
    name: 'UserEdit',
    /**
      * Props
      */
    props: {
        accessToken: String,
        loginUserId: Number
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
    mounted: function () {
        this.$emit('set-title', 'ユーザー編集')
        if (!this.accessToken) {
            this.$router.push('/')
        }
    }
});
