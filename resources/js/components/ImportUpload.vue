<template>
  <div class="max-w-xl mx-auto mt-10 p-6 bg-white rounded shadow">
    <h1 class="text-2xl font-bold mb-6">Upload Transaction File</h1>

    <form @submit.prevent="onSubmit" class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          File (CSV, JSON, XML)
        </label>
        <input
          type="file"
          accept=".csv,.json,.xml,text/csv,application/json,text/xml,application/xml"
          @change="onFileChange"
          class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
        />
      </div>

      <div v-if="error" class="text-red-600 text-sm">{{ error }}</div>

      <div class="flex gap-3">
        <button
          type="submit"
          :disabled="!file || loading"
          class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          {{ loading ? 'Uploading...' : 'Upload' }}
        </button>
        <router-link
          to="/"
          class="px-4 py-2 border border-gray-300 text-gray-700 rounded hover:bg-gray-50"
        >
          Cancel
        </router-link>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useToast } from 'vue-toastification';
import { uploadImport } from '@/api/imports';

const router = useRouter();
const toast = useToast();
const file = ref<File | null>(null);
const loading = ref(false);
const error = ref<string | null>(null);

function onFileChange(event: Event) {
  const input = event.target as HTMLInputElement;
  file.value = input.files?.[0] ?? null;
}

async function onSubmit() {
  if (!file.value) {
    return;
  }

  loading.value = true;
  error.value = null;

  try {
    const result = await uploadImport(file.value);

    if (result.status === 'success') {
      toast.success('All records imported successfully.');
    } else if (result.status === 'partial') {
      toast.warning(`Import completed with errors. ${result.failed_records} record(s) failed.`);
    } else {
      toast.error(`Import failed. All ${result.failed_records} record(s) were invalid.`);
    }

    router.push('/');
  } catch (e) {
    error.value = e instanceof Error ? e.message : 'Upload failed.';
  } finally {
    loading.value = false;
  }
}
</script>
