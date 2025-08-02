<template>
  <BaseDetail
    :baseUrl="EVENTS_BASE_URL"
    :client="client"
    entity="events"
    :fields="fields"
    v-model:form="form"
    :relations="relations"
    :id="id"
  />
</template>

<script setup lang="ts" generic="T extends Event & IBaseEntity">
import { client } from "../../../shared/api/axios";
import { EVENTS_BASE_URL } from "../const";
import { FormContext } from "vee-validate";
import { ref } from "vue";
import BaseDetail from "../../../../../shared/components/BaseDetail.vue";
import { Event, IBaseEntity } from "../../../../../shared/types";
import { useEventsDetailFormFields } from "../forms/detail";

const { id } = defineProps<{
  id?: string;
}>();

const { fields } = useEventsDetailFormFields();

const form = ref<FormContext<T>>();

const relations = ["files", "images"];
</script>