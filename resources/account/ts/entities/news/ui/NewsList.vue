<template>
  <BaseList :columns="columns" :baseUrl="NEWS_BASE_URL" :client="client" entity="news" />
</template>

<script setup lang="ts">
import BaseList from '@/shared/components/BaseList.vue';
import { client } from '../../../shared/api/axios';
import { NEWS_BASE_URL } from '../const';

const columns = [
  { field: 'title', header: 'Заголовок' },
  { field: 'slug', header: 'Slug' },
  { field: 'description', header: 'Описание' },
];
</script>
