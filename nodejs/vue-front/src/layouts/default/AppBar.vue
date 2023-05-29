<template>
  <v-app-bar>
    <v-app-bar-nav-icon @click="$emit('clickIt')" />
    <v-toolbar-title @click="toIndex">Kotori Project</v-toolbar-title>
  </v-app-bar>
</template>

<script setup>
import { useRouter } from 'vue-router';
const router = useRouter();
// 跳转到主页
function toIndex() {
  router.push({ name: "Home" });
}
</script>
