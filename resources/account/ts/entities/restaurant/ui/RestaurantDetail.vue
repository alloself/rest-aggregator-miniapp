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

<script setup lang="ts" generic="T extends Restaurant & IBaseEntity">
import { client } from "../../../shared/api/axios";
import { RESTAURANT_BASE_URL } from "../const";
import { useRestaurantDetailFormFields } from "../forms/detail";
import { FormContext } from "vee-validate";
import { computed, ref } from "vue";
import BaseDetail from "../../../../../shared/components/BaseDetail.vue";
import { Restaurant, IBaseEntity } from "../../../../../shared/types";
import { useAuthStore } from "@/shared";
import { storeToRefs } from "pinia";

const { id } = defineProps<{
  id?: string;
}>();

const { fields } = useRestaurantDetailFormFields({id});

const form = ref<FormContext<T>>();

const authStore = useAuthStore();

const { user } = storeToRefs(authStore);

const initialValues = computed<Partial<T>>(() => {
  return {
    user_id: user.value?.id,
    working_hours: {},
  } as Partial<T>;
});

const relations = ["files", "images", "news", "events"];
</script>
