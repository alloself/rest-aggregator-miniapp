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
  />
</template>

<script setup lang="ts">
import { client } from '../../../shared/api/axios';
import { RESTAURANT_BASE_URL } from '../const';
import { useRestaurantDetailFormFields } from '../forms/detail';
import { FormContext } from 'vee-validate';
import { ref } from 'vue';
import BaseDetail from '../../../../../shared/components/BaseDetail.vue';
import { Restaurant } from '../../../../../shared/types';

const { id } = defineProps<{
  id?: string;
}>();

const { fields } = useRestaurantDetailFormFields({ id });

const form = ref<FormContext<Restaurant>>();

const initialValues = {
  working_hours: {},
};

const relations = ['files', 'images', 'news', 'events', 'categories.descendants'];
</script>
