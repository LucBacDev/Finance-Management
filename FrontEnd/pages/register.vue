<template>
  <AuthenticationSignUp />
</template>
<script setup>
definePageMeta({
  layout: false, // Không dùng layout mặc định
  middleware: ["auth"]
})
</script>