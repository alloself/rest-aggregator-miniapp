<template>
  <BaseDetail
    :baseUrl="DISHES_BASE_URL"
    :client="client"
    entity="dishes"
    :fields="fields"
    v-model:form="form"
    :relations="relations"
    :id="id"
  />
</template>

<script setup lang="ts">
import { client } from '../../../shared/api/axios';
import { DISHES_BASE_URL } from '../const';
import { FormContext } from 'vee-validate';
import { ref } from 'vue';
import BaseDetail from '../../../../../shared/components/BaseDetail.vue';
import { Dish } from '../../../../../shared/types';
import { useDishesDetailFormFields } from '../forms/detail';

const { id } = defineProps<{
  id?: string;
}>();

const { fields } = useDishesDetailFormFields();

const form = ref<FormContext<Dish>>();

const relations = ['files', 'images'];
</script>


