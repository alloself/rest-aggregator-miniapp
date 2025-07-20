<template>
    <BaseList :columns="columns" :baseUrl="CATEGORY_BASE_URL" :client="client" entity="category" />
</template>

<script setup lang="ts">
import BaseList from "@/shared/components/BaseList.vue";
import { client } from "../../shared/api/axios";
import { CATEGORY_BASE_URL } from "../../entities/category/const";

const columns = [
    { field: "id", header: "ID" },
    { field: "name", header: "Название" },
    { field: "description", header: "Описание" },
];

</script>
