<template>
    <BaseList
        :columns="columns"
        :baseUrl="RESTAURANT_BASE_URL"
        :client="client"
        entity="restaurant"
    />
</template>

<script setup lang="ts">
import BaseList from "@/shared/components/BaseList.vue";
import { client } from "../../shared/api/axios";
import { RESTAURANT_BASE_URL } from "../../entities/restaurant/const";

const columns = [
    { field: "name", header: "Название" },
    { field: "slug", header: "Slug" },
];
</script>
