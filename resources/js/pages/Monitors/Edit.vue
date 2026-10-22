<script setup lang="ts">
import Form from './Form.vue';

defineProps<{ monitor: any }>();
</script>

<template><Form :monitor="monitor" /></template>
