<template>
  <v-app>
    <default-bar @click-it="toggleNavigationOpen" />
    <drawer :navigation_open="navigation_open" />
    <default-view />
  </v-app>
</template>

<script setup>
import { ref } from "vue";

import DefaultBar from "./AppBar.vue";
import Drawer from "./Drawer.vue";
import DefaultView from "./View.vue";

// 菜单是否打开
let navigation_open = ref(true)

// 菜单打开关闭切换
const toggleNavigationOpen = () => {
  // console.log(navigation_open.value)
  navigation_open.value = !navigation_open.value;
};
</script>
