<template>
  <div class="max-w-5xl mx-auto mt-10 p-6">
    <router-link to="/" class="text-blue-600 hover:underline text-sm mb-4 inline-block">
      ← Back to list
    </router-link>

    <AppStateDisplay :loading="loading" :error="error">
      <template v-if="importDetail">
        <div class="bg-white rounded shadow p-6 mb-6">
          <h1 class="text-2xl font-bold mb-4">{{ importDetail.file_name }}</h1>
          <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
              <span class="text-gray-500">Total records:</span>
              <span class="ml-2 font-medium">{{ importDetail.total_records }}</span>
            </div>
            <div>
              <span class="text-gray-500">Status:</span>
              <AppStatusBadge :status="importDetail.status" class="ml-2" />
            </div>
            <div>
              <span class="text-gray-500">Failed:</span>
              <span class="ml-2 font-medium text-red-700">{{ importDetail.failed_records }}</span>
            </div>
            <div>
              <span class="text-gray-500">Date:</span>
              <span class="ml-2 font-medium">{{ new Date(importDetail.created_at).toLocaleString() }}</span>
            </div>
            <div>
              <span class="text-gray-500">Successful:</span>
              <span class="ml-2 font-medium text-green-700">{{ importDetail.successful_records }}</span>
            </div>
          </div>
        </div>

        <div v-if="importDetail.logs.length > 0">
          <h2 class="text-lg font-semibold mb-3">Error Logs</h2>
          <table class="w-full text-sm border rounded overflow-hidden bg-white">
            <thead class="bg-gray-100 text-left">
              <tr>
                <th class="px-4 py-2">Transaction ID</th>
                <th class="px-4 py-2">Error</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="log in importDetail.logs"
                :key="log.id"
                class="border-t"
              >
                <td class="px-4 py-2 font-mono text-xs">{{ log.transaction_id ?? '—' }}</td>
                <td class="px-4 py-2 text-red-700">{{ log.error_message }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-else class="text-gray-500 text-sm">No errors for this import.</div>
      </template>
    </AppStateDisplay>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { useImport } from '@/composables/useImport';
import AppStatusBadge from '@/components/ui/AppStatusBadge.vue';
import AppStateDisplay from '@/components/ui/AppStateDisplay.vue';

const route = useRoute();
const { importDetail, loading, error, fetchImport } = useImport();

onMounted(() => {
  fetchImport(Number(route.params.id));
});
</script>
