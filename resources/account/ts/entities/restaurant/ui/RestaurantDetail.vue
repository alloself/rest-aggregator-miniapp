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
import { useRestaurantDetailFormFields } from '../forms/detail';
import { FormContext } from 'vee-validate';
import { ref } from 'vue';
import { useToast } from 'primevue/usetoast';
import Button from 'primevue/button';
import BaseDetail from '../../../../../shared/components/BaseDetail.vue';
import { Restaurant } from '../../../../../shared/types';
import { isAxiosError } from 'axios';

const { id } = defineProps<{
  id?: string;
}>();

const toast = useToast();
const syncBotLoading = ref(false);

const { fields } = useRestaurantDetailFormFields({ id });

const form = ref<FormContext<Restaurant, Restaurant>>();

const initialValues = {
  working_hours: {},
};

const relations = ['files', 'images', 'news', 'events', 'categories.descendants'];

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
