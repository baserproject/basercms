<template>
    <user-form
        :access-token="accessToken"
        :loginUserId="loginUserId"
        :user-id="$route.params.id"
        @set-message="(message, isError, isFlash) => $emit('set-message', message, isError, isFlash)"
        @clear-message="$emit('clear-message')" />
</template>

<script>

import UserForm from '../components/UserForm.vue'

export default {

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

}
</script>
