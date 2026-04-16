import { ref } from 'vue';
import type { Import } from '@/types';
import { getImports } from '@/api/imports';

export function useImports() {
  const imports = ref<Import[]>([]);
  const loading = ref(false);
  const error = ref<string | null>(null);

  async function fetchImports() {
    loading.value = true;
    error.value = null;

    try {
      imports.value = await getImports();
    } catch (e) {
      error.value = e instanceof Error ? e.message : 'Unknown error.';
    } finally {
      loading.value = false;
    }
  }

  return { imports, loading, error, fetchImports };
}
