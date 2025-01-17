<template>
    <Authentication />
</template>
<script setup>
definePageMeta({
    layout: false,
});
import { onMounted } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();

// if (import.meta.client) {
//     const token = sessionStorage.getItem('token');

//     if (token) {
//         router.push('/home');
//     }
// }

</script>