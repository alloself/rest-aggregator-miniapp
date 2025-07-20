<template>
    <DataTable :value="files" tableStyle="min-width: 50rem">
        <Column field="name" header="Name"></Column>
        <Column field="extension" header="Extension"></Column>
        <Column field="preview" header="Preview"></Column>
        <Column field="actions" header="Actions">
            <template #body="{ data }">
                <Button label="View" icon="pi pi-eye" />
            </template>
        </Column>
        <template #footer>
            <div class="flex">
                <div class="flex-1"></div>
                <Button type="button" icon="pi pi-plus" text />
            </div>
        </template>
    </DataTable>
</template>

<script setup lang="ts" generic="T extends FileModel">
import { File as FileModel } from "../types";

const files = defineModel<FileModel[]>("files", {
    default: () => [],
});
</script>
