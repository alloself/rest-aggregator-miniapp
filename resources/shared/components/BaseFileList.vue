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
        <div class="flex flex-col gap-4">
            <FileUpload
                customUpload
                mode="basic"
                @select="onSelect"
                :accept="acceptType"
                :maxFileSize="1000000"
            >
            </FileUpload>
        </div>
        <template #footer>
            <Button label="Сохранить" @click="onSave" />
        </template>
    </Dialog>
</template>

<script setup lang="ts" generic="T extends FileModel">
import { File as FileModel } from "../types";
import { ref, computed } from "vue";
import { AxiosInstance } from "axios";

const {
    baseUrl,
    client,
    type = "file",
    invalid = false,
    initialItems = [],
} = defineProps<{
    baseUrl: string;
    client: AxiosInstance;
    type: "file" | "image";
    invalid?: boolean;
    initialItems?: FileModel[];
}>();

const getBasePivot = () => {
    return {
        key: "",
        order: 0,
        type,
    };
};

const files = defineModel<FileModel[]>("modelValue", {
    default: () => [],
});

const acceptType = computed(() => {
    return type === "image" ? "image/*" : undefined;
});

const showDialog = ref(false);

const bufferItems = ref<File[]>([]);

const onSelect = ({ files }: { files: File[] }) => {
    bufferItems.value = files;
};

const onSave = async () => {
    const formData = new FormData();
    formData.append("file", bufferItems.value[0]);

    try {
        const response = await client.post(baseUrl, formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });

        if (response.data) {
            files.value = [
                ...files.value,
                { ...response.data, pivot: getBasePivot() },
            ];
        }

        showDialog.value = false;
    } catch (error) {
        console.error("File upload failed:", error);
    }
};
</script>
