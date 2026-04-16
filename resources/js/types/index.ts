export interface Import {
  id: number;
  file_name: string;
  total_records: number;
  successful_records: number;
  failed_records: number;
  status: 'success' | 'partial' | 'failed';
  created_at: string;
}

export interface ImportLog {
  id: number;
  transaction_id: string | null;
  error_message: string;
}

export interface ImportDetail extends Import {
  logs: ImportLog[];
}
