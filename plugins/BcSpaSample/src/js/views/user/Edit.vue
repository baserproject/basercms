<template>
    <user-form
        :access-token="accessToken"
        :loginUserId="loginUserId"
        :user-id="Number($route.params.id)"
        @set-message="(message, isError, isFlash) => $emit('set-message', message, isError, isFlash)"
        @clear-message="$emit('clear-message')" />
</template>
<script lang="ts" src="Edit.ts"></script>
