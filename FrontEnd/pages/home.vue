<template>
    <AppContent />
</template>
<script setup>
import { onBeforeMount } from 'vue';
onBeforeMount(() => {
    const token = localStorage.getItem('token');

    if (!token) {

        return navigateTo('/');
    }
})
// definePageMeta({
//     middleware: ['auth'],  
// });
</script>
