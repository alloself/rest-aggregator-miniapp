<template>
    <BaseDetail
        :baseUrl="CATEGORY_BASE_URL"
        :client="client"
        entity="category"
        :fields="fields"
        v-model:form="form"
        :id="id"
    />
</template>

<script setup lang="ts" generic="T extends Category & IBaseEntity">
import { client } from "../../../shared/api/axios";
import { CATEGORY_BASE_URL } from "../const";
import { useCategoryDetailFormFields } from "../forms/detail";
import { FormContext } from "vee-validate";
import { computed, ref } from "vue";
import { useRoute } from "vue-router";
import BaseDetail from "../../../../../shared/components/BaseDetail.vue";
import { Category, IBaseEntity } from "../../../../../shared/types";

interface Props {
  id?: string;
}

const props = defineProps<Props>();

const route = useRoute();

const id = computed(() => props.id || route.params.id?.toString() || undefined);

const { fields } = useCategoryDetailFormFields();

const form = ref<FormContext<T>>();
</script>