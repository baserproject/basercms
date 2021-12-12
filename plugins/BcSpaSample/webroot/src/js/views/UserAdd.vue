<template>
    <user-form :access-token="accessToken"
        @set-message="(message, isError, isFlash) => $emit('set-message', message, isError, isFlash)"
        @clear-message="$emit('clear-message')" />
</template>

<script>

import UserForm from '../components/UserForm.vue'

export default {

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
    mounted: function () {
        this.$emit('set-title', 'ユーザー登録')
        if (!this.accessToken) {
            this.$router.push('/')
        }
    },

}
</script>
