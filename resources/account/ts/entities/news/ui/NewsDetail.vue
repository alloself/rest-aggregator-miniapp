<template>
  <BaseDetail
    :baseUrl="NEWS_BASE_URL"
    :client="client"
    entity="news"
    :fields="fields"
    v-model:form="form"
    :relations="relations"
    v-bind="$attrs"
    :id="id"
  />
</template>

<script setup lang="ts" generic="T extends News & IBaseEntity">
import { client } from '../../../shared/api/axios';
import { NEWS_BASE_URL } from '../const';
import { FormContext } from 'vee-validate';
import { ref } from 'vue';
import BaseDetail from '../../../../../shared/components/BaseDetail.vue';
import { News, IBaseEntity } from '../../../../../shared/types';
import { useNewsDetailFormFields } from '../forms/detail';

const { id } = defineProps<{
  id?: string;
}>();

const { fields } = useNewsDetailFormFields();

const form = ref<FormContext<T>>();

const relations = ['files', 'images'];
</script>
