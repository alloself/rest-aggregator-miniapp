<template>
  <BaseDetail
    :baseUrl="RESTAURANT_BASE_URL"
    :client="client"
    entity="restaurant"
    :fields="fields"
    v-model:form="form"
    :initialValues="initialValues"
    :relations="relations"
    :id="id"
  >
    <RestaurantFilesSection
      v-model:modelValue="restaurantFiles"
      :base-url="FILE_BASE_URL"
      :client="client"
      title="Файлы"
    />

    <RestaurantLegalDocumentsSection
      v-if="isRoot"
      v-model:modelValue="restaurantFiles"
      :base-url="FILE_BASE_URL"
      :client="client"
    />

    <template #footer-extra>
      <Button
        v-if="id"
        label="Обновить настройки бота"
        severity="secondary"
        outlined
        :loading="syncBotLoading"
        :disabled="syncBotLoading"
        @click="handleSyncBotSettings"
      />
    </template>
  </BaseDetail>
</template>

<script setup lang="ts">
import { client } from '../../../shared/api/axios';
import { RESTAURANT_BASE_URL } from '../const';
import { FILE_BASE_URL } from '../../category/const';
import { useRestaurantDetailFormFields } from '../forms/detail';
import { FormContext } from 'vee-validate';
import { computed, ref } from 'vue';
import { useToast } from 'primevue/usetoast';
import Button from 'primevue/button';
import BaseDetail from '../../../../../shared/components/BaseDetail.vue';
import { isAxiosError } from 'axios';
import { useAuthStore } from '@/shared';
import type { RestaurantResourceData, FileResourceData } from '@/shared/types/resources';
import RestaurantFilesSection from './RestaurantFilesSection.vue';
import RestaurantLegalDocumentsSection from './RestaurantLegalDocumentsSection.vue';

const { id } = defineProps<{
  id?: string;
}>();

const authStore = useAuthStore();
const toast = useToast();
const syncBotLoading = ref(false);

const { fields } = useRestaurantDetailFormFields({ id });

const form = ref<FormContext<RestaurantResourceData, RestaurantResourceData>>();

const initialValues = {
  working_hours: {},
};

const relations = ['files', 'images', 'news', 'events', 'categories.descendants'];

const isRoot = computed(() => authStore.user?.is_root === true);

const restaurantFiles = computed<FileResourceData[]>({
  get: () => form.value?.values.files ?? [],
  set: (value) => {
    form.value?.setFieldValue('files', value);
  },
});

async function handleSyncBotSettings(): Promise<void> {
  if (!id) return;

  syncBotLoading.value = true;
  try {
    await client.post(`${RESTAURANT_BASE_URL}/${id}/sync-bot-settings`);
    toast.add({
      severity: 'success',
      summary: 'Успешно',
      detail: 'Настройки бота успешно обновлены',
    });
  } catch (error) {
    const message =
      isAxiosError(error) && error.response?.data?.message
        ? String(error.response.data.message)
        : 'Ошибка при обновлении настроек бота';
    toast.add({
      severity: 'error',
      summary: 'Ошибка',
      detail: message,
    });
  } finally {
    syncBotLoading.value = false;
  }
}
</script>
