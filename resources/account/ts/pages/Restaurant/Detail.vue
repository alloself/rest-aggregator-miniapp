<template>
    <BaseDetail
        :baseUrl="RESTAURANT_BASE_URL"
        :client="client"
        entity="restaurant"
        :fields="fields"
        v-model:form="form"
        :initialValues="initialValues"
        :id="id"
    />
</template>

<script setup lang="ts" generic="T extends Restaurant & IBaseEntity">
import { client } from "../../shared/api/axios";
import { RESTAURANT_BASE_URL } from "../../entities/restaurant/const";
import { useRestaurantDetailFormFields } from "../../entities/restaurant/forms/detail";
import { FormContext } from "vee-validate";
import { computed, ref } from "vue";
import BaseDetail from "../../../../shared/components/BaseDetail.vue";
import { Restaurant, IBaseEntity } from "../../../../shared/types";
import { useAuthStore } from "@/shared";
import { storeToRefs } from "pinia";
import { useRoute } from "vue-router";

const { fields } = useRestaurantDetailFormFields();

const form = ref<FormContext<T>>();

const route = useRoute();

const authStore = useAuthStore();

const { user } = storeToRefs(authStore);

const initialValues = computed<Partial<T>>(() => {
    return {
        user_id: user.value?.id,
    } as Partial<T>;
});

const id = computed(() => route.params.id?.toString() || undefined);
</script>
