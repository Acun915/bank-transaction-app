<template>
  <span :class="classes" class="px-2 py-0.5 rounded text-xs font-medium">
    {{ status }}
  </span>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { statusClasses } from '@/utils/status';

const props = defineProps<{
  status: 'success' | 'partial' | 'failed';
}>();

const classes = computed(() => statusClasses[props.status]);
</script>
