<template>
  <div class="space-y-6">
    <div>
      <h1 class="text-xl sm:text-2xl font-bold text-brand-900 tracking-tight">
        Log Audit & Rekam Jejak Sistem
      </h1>
      <p class="text-xs sm:text-sm text-muted mt-1">
        Pencatatan riwayat setiap aksi penambahan, pembaruan, perubahan status publikasi, dan impor data oleh seluruh pengguna sistem.
      </p>
    </div>

    <!-- Security & Immutability Badge -->
    <div class="p-3 bg-gray-50 border border-gray-200 rounded-xl text-xs text-muted flex items-center justify-between">
      <div class="flex items-center gap-2">
        <ShieldCheck class="w-4 h-4 text-emerald-600" />
        <span>Tabel log audit bersifat <b>Append-Only (Immutability)</b> dan tidak dapat diubah maupun dihapus.</span>
      </div>
      <span class="font-mono text-[11px]">Total: {{ mockAuditLogs.length }} Aktivitas</span>
    </div>

    <DataTable
      :columns="columns"
      :rows="mockAuditLogs"
      :page-size="10"
      :show-state-filter="false"
    >
      <template #action="{ row }">
        <span
          :class="{
            'bg-emerald-50 text-emerald-800 border-emerald-200': row.action === 'CREATE',
            'bg-blue-50 text-blue-800 border-blue-200': row.action === 'UPDATE',
            'bg-purple-50 text-purple-800 border-purple-200': row.action === 'TRANSITION',
            'bg-amber-50 text-amber-800 border-amber-200': row.action === 'RECALCULATE',
            'bg-rose-50 text-rose-800 border-rose-200': row.action === 'DELETE',
            'bg-gray-100 text-gray-800 border-gray-200': row.action === 'IMPORT',
          }"
          class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider border font-mono"
        >
          {{ row.action }}
        </span>
      </template>

      <template #entity="{ row }">
        <span class="font-mono text-xs font-semibold text-brand-900">
          {{ row.entity }} #{{ row.entity_id }}
        </span>
      </template>
    </DataTable>
  </div>
</template>

<script setup>
import { mockAuditLogs } from '@/data/mockData'
import DataTable from '@/components/ui/DataTable.vue'
import { ShieldCheck } from 'lucide-vue-next'

const columns = [
  { key: 'created_at', label: 'Waktu / Timestamp' },
  { key: 'user_name', label: 'Pelaksana (User)' },
  { key: 'action', label: 'Jenis Aksi' },
  { key: 'entity', label: 'Target Entitas' },
  { key: 'details', label: 'Rincian Perubahan' },
]
</script>