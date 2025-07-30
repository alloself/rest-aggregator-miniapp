<template>
    <BaseDetail
        :baseUrl="CATEGORY_BASE_URL"
        :client="client"
        entity="category"
        :fields="fields"
        v-model:form="form"
        :initialValues="initialValues"
        :relations="relations"
        :id="id"
    />
</template>

<script setup lang="ts" generic="T extends Category & IBaseEntity">
import { client } from "@/account/ts/shared/api/axios";
import { CATEGORY_BASE_URL } from "../const";
import { useCategoryDetailFormFields } from "../forms/detail";
import { FormContext } from "vee-validate";
import { computed, ref } from "vue";
import BaseDetail from "../../../../../shared/components/BaseDetail.vue";
import { Category, IBaseEntity } from "@/shared/types";
import { useRoute } from "vue-router";

const { fields } = useCategoryDetailFormFields();

const form = ref<FormContext<T>>();

const route = useRoute();

const initialValues = computed<Partial<T>>(() => {
    return {
        order: 0,
    } as unknown as Partial<T>;
});

const id = computed(() => route.params.id?.toString() || undefined);

const relations = ["parent", "children"];
</script>
