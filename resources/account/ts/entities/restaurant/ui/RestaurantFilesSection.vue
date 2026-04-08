<template>
  <BaseFileList
    v-model:modelValue="generalFiles"
    :type="type"
    :base-url="baseUrl"
    :client="client"
    :title="title"
  />
</template>

<script setup lang="ts">
import { computed } from 'vue';
import type { AxiosInstance } from 'axios';
import BaseFileList from '@/shared/components/BaseFileList.vue';
import type { FileResourceData } from '@/shared/types/resources';
import {
  isLegalDocumentFileKey,
} from '@/shared/constants/legalDocuments';

const props = withDefaults(
  defineProps<{
    baseUrl: string;
    client: AxiosInstance;
    title?: string;
    type?: 'file' | 'image';
  }>(),
  {
    title: 'Файлы',
    type: 'file',
  },
);

const files = defineModel<FileResourceData[]>('modelValue', {
  default: () => [],
});

const generalFiles = computed<FileResourceData[]>({
  get: () => (
    files.value.filter(
      (file) => !isLegalDocumentFileKey(file.pivot?.key),
    )
  ),
  set: (updatedFiles) => {
    const protectedLegalFiles = files.value.filter((file) =>
      isLegalDocumentFileKey(file.pivot?.key),
    );

    files.value = [...updatedFiles, ...protectedLegalFiles];
  },
});

const { baseUrl, client, title, type } = props;
</script>
