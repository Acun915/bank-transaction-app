<template>
  <div class="max-w-5xl mx-auto mt-10 p-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Imports</h1>
      <router-link
        to="/upload"
        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
      >
        Upload File
      </router-link>
    </div>

    <AppStateDisplay
      :loading="loading"
      :error="error"
      :empty="imports.length === 0"
      empty-message="No imports yet."
    >
      <table class="w-full text-sm border rounded overflow-hidden bg-white">
        <thead class="bg-gray-100 text-left">
          <tr>
            <th class="px-4 py-2">File</th>
            <th class="px-4 py-2">Total</th>
            <th class="px-4 py-2">OK</th>
            <th class="px-4 py-2">Failed</th>
            <th class="px-4 py-2">Status</th>
            <th class="px-4 py-2">Date</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="item in imports"
            :key="item.id"
            class="border-t hover:bg-gray-50 cursor-pointer"
            @click="$router.push(`/imports/${item.id}`)"
          >
            <td class="px-4 py-2">{{ item.file_name }}</td>
            <td class="px-4 py-2">{{ item.total_records }}</td>
            <td class="px-4 py-2">{{ item.successful_records }}</td>
            <td class="px-4 py-2">{{ item.failed_records }}</td>
            <td class="px-4 py-2">
              <AppStatusBadge :status="item.status" />
            </td>
            <td class="px-4 py-2">{{ new Date(item.created_at).toLocaleString() }}</td>
          </tr>
        </tbody>
      </table>
    </AppStateDisplay>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue';
import { useImports } from '@/composables/useImports';
import AppStatusBadge from '@/components/ui/AppStatusBadge.vue';
import AppStateDisplay from '@/components/ui/AppStateDisplay.vue';

const { imports, loading, error, fetchImports } = useImports();

onMounted(fetchImports);
</script>
