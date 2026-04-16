import type { Import, ImportDetail } from '@/types';

const BASE_URL = '/api/imports';

export async function getImports(): Promise<Import[]> {
  const response = await fetch(BASE_URL);

  if (!response.ok) {
    throw new Error('Failed to fetch imports.');
  }

  const json = await response.json();
  return json.data as Import[];
}

export async function getImport(id: number): Promise<ImportDetail> {
  const response = await fetch(`${BASE_URL}/${id}`);

  if (response.status === 404) {
    throw new Error('Import not found.');
  }

  if (!response.ok) {
    throw new Error('Failed to fetch import.');
  }

  const json = await response.json();
  return json.data as ImportDetail;
}

export async function uploadImport(file: File): Promise<Import> {
  const formData = new FormData();
  formData.append('file', file);

  const response = await fetch(BASE_URL, {
    method: 'POST',
    body: formData,
  });

  if (!response.ok) {
    const json = await response.json().catch(() => ({}));
    throw new Error(json.message ?? 'Failed to upload file.');
  }

  const json = await response.json();
  return json.data as Import;
}
