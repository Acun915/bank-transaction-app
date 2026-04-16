<template>
  <div v-if="loading" class="text-gray-500">Loading...</div>
  <div v-else-if="error" class="text-red-600">{{ error }}</div>
  <div v-else-if="empty" class="text-gray-500">{{ emptyMessage }}</div>
  <slot v-else />
</template>

<script setup lang="ts">
defineProps<{
  loading: boolean;
  error: string | null;
  empty?: boolean;
  emptyMessage?: string;
}>();
</script>
