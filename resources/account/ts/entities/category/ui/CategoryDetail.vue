<template>
  <BaseDetail
    :baseUrl="CATEGORY_BASE_URL"
    :client="client"
    entity="news"
    :fields="fields"
    v-model:form="form"
    v-bind="$attrs"
    :relations="['children']"
    :id="id"
  />
</template>

<script setup lang="ts" generic="T extends Category & IBaseEntity">
import { client } from '../../../shared/api/axios';
import { CATEGORY_BASE_URL } from '../const';
import { FormContext } from 'vee-validate';
import { ref } from 'vue';
import BaseDetail from '../../../../../shared/components/BaseDetail.vue';
import { IBaseEntity, Category } from '../../../../../shared/types';
import { useCategoryDetailFormFields } from '../forms/detail';

const { id } = defineProps<{
  id?: string;
}>();

const { fields } = useCategoryDetailFormFields({ id });

const form = ref<FormContext<T>>();
</script>
