<template>
    <TreeTable :value="items" size="small">
        <Column v-for="col of columns"></Column>
    </TreeTable>
</template>

<script setup lang="ts" generic="T extends IBaseTreeEntity & TreeNode">
import { IBaseTreeEntity } from "../types";
import { TreeNode } from "primevue/treenode";

const { items = [], columns = [] } = defineProps<{
    items: T[];
    columns: { field: string; header: string }[];
}>();
</script>
