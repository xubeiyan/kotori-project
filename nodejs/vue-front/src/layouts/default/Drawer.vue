<template>
  <v-navigation-drawer location="left" :rail="!props.navigation_open">
    <v-list density="compact" nav>
      <v-list-item prepend-icon="mdi-upload" title="上传图片" value="upload" />
      <v-list-item prepend-icon="mdi-upload" title="浏览图片" value="upload" />
      <v-list-item prepend-icon="mdi-upload" title="随机图片" value="upload" />
    </v-list>
  </v-navigation-drawer>
</template>
<script setup>
const props = defineProps({
  navigation_open: {
    type: Boolean,
  },
});
</script>
