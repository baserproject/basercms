<template>
    <user-form
        :access-token="accessToken"
        @set-message="(message, isError, isFlash) => $emit('set-message', message, isError, isFlash)"
        @clear-message="$emit('clear-message')" />
</template>
<script lang="ts" src="Add.ts"></script>
