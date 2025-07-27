<template>
    <div>
        <h3>{{ title }}</h3>
        <DataTable
            :value="files"
            showGridlines
            stripedRows
            selectionMode="multiple"
            v-model:selection="selectedFiles"
            size="small"
        >
            <Column selectionMode="multiple" headerStyle="width: 3rem"></Column>
            <Column field="name" header="Name"></Column>
            <Column field="pivot.order" header="Order">
                <template #body="{ data }">
                    <InputNumber v-model="data.pivot.order" />
                </template>
            </Column>
            <Column field="pivot.key" header="Key">
                <template #body="{ data }">
                    <InputText v-model="data.pivot.key" />
                </template>
            </Column>

            <template #footer>
                <div class="flex">
                    <div class="flex-1"></div>
                    <Button
                        type="button"
                        icon="pi pi-trash"
                        text
                        :disabled="selectedFiles.length === 0"
                        @click="deleteSelectedFiles"
                    />
                    <Button
                        type="button"
                        icon="pi pi-plus"
                        text
                        @click="showDialog = true"
                    />
                </div>
            </template>
        </DataTable>
    </div>

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
    title,
} = defineProps<{
    baseUrl: string;
    client: AxiosInstance;
    type: "file" | "image";
    invalid?: boolean;
    initialItems?: FileModel[];
    title: string;
}>();

const getBasePivot = () => {
    return {
        key: "",
        order: 0,
        type,
    };
};

const selectedFiles = ref<FileModel[]>([]);

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

const deleteSelectedFiles = async () => {
    files.value = files.value.filter(
        (file) => !selectedFiles.value.includes(file),
    );
    selectedFiles.value = [];
};
</script>
