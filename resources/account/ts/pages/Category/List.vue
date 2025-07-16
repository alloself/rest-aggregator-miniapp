<template>
    <BaseList :columns="columns" :baseUrl="baseUrl" :client="client" />
</template>

<script setup lang="ts">
import BaseList from "@/shared/components/BaseList.vue";
import { client } from "../../shared/api/axios";

const columns = [
    { field: "id", header: "ID" },
    { field: "name", header: "Название" },
    { field: "description", header: "Описание" },
];

const baseUrl = "/api/account/categories";
</script>
