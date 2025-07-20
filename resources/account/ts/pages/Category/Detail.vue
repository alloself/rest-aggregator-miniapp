<template>
    <BaseDetail
        :baseUrl="CATEGORY_BASE_URL"
        :client="client"
        entity="category"
        :fields="fields"
        v-model:form="form"
    />
</template>

<script setup lang="ts" generic="T extends Category & IBaseEntity">
import { client } from "../../shared/api/axios";
import { CATEGORY_BASE_URL } from "../../entities/category/const";
import { useCategoryDetailFormFields } from "../../entities/category/forms/detail";
import { FormContext } from "vee-validate";
import { ref } from "vue";
import BaseDetail from "../../../../shared/components/BaseDetail.vue";
import { Category, IBaseEntity } from "../../../../shared/types";

const { fields } = useCategoryDetailFormFields();

const form = ref<FormContext<T>>();
</script>
