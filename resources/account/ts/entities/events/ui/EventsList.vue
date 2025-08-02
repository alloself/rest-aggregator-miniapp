<template>
    <BaseList
        :columns="columns"
        :baseUrl="EVENTS_BASE_URL"
        :client="client"
        entity="events"
    />
</template>

<script setup lang="ts">
import BaseList from "@/shared/components/BaseList.vue";
import { client } from "../../../shared/api/axios";
import { EVENTS_BASE_URL } from "../const";

const columns = [
    { field: "title", header: "Заголовок" },
    { field: "subtitle", header: "Подзаголовок" },
    { field: "start_at", header: "Дата начала" },
    { field: "price_display", header: "Цена" },
    { field: "is_free", header: "Бесплатно" },
    { field: "slug", header: "Slug" },
];
</script>