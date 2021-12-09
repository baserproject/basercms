<template>
    <user-form :access-token="accessToken"
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
        this.$emit('set-title', 'ユーザー編集')
        if (!this.accessToken) {
            this.$router.push('/')
        }
    }

}
</script>
