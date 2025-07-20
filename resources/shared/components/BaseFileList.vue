<template>
    <DataTable :value="files" showGridlines stripedRows>
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
                <Button
                    type="button"
                    icon="pi pi-plus"
                    text
                    @click="showDialog = true"
                />
            </div>
        </template>
    </DataTable>

    <Dialog v-model:visible="showDialog" modal header="Создать файл">
        <FileUpload
            name="files[]"
            :url="`${baseUrl}`"
            @upload="onUpload"
            :multiple="true"
            :accept="`${type}/*`"
            :maxFileSize="1000000"
        >
            <template #empty>
                <span>Drag and drop files to here to upload.</span>
            </template>
        </FileUpload>
    </Dialog>
</template>

<script setup lang="ts" generic="T extends FileModel">
import { File as FileModel } from "../types";
import { ref } from "vue";
import { AxiosInstance } from "axios";
import { FileUploadEvent } from "primevue/fileupload";

const {
    baseUrl,
    client,
    type = "file",
} = defineProps<{
    baseUrl: string;
    client: AxiosInstance;
    type: "file" | "image";
}>();

const files = defineModel<FileModel[]>("files", {
    default: () => [],
});

const showDialog = ref(false);

const onUpload = (event: FileUploadEvent) => {
    console.log(event);
};
</script>
