import { ref } from 'vue';
import type { ImportDetail } from '@/types';
import { getImport } from '@/api/imports';

export function useImport() {
  const importDetail = ref<ImportDetail | null>(null);
  const loading = ref(false);
  const error = ref<string | null>(null);

  async function fetchImport(id: number) {
    loading.value = true;
    error.value = null;
    importDetail.value = null;

    try {
      importDetail.value = await getImport(id);
    } catch (e) {
      error.value = e instanceof Error ? e.message : 'Unknown error.';
    } finally {
      loading.value = false;
    }
  }

  return { importDetail, loading, error, fetchImport };
}
