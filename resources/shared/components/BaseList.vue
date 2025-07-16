<template>
    <DataTable
        :value="listData.items"
        showGridlines
        stripedRows
        paginator
        :rows="5"
        :rowsPerPageOptions="[5, 10, 20, 50]"
    >
        <slot v-for="col of columns" :name="`column-${col.field}`">
            <Column
                :key="col.field"
                :field="col.field"
                :header="col.header"
            ></Column>
        </slot>
        <template #paginatorend>
            <Button type="button" icon="pi pi-download" text />
        </template>
    </DataTable>
</template>

<script setup lang="ts" generic="T extends IBaseEntity">
import { AxiosInstance } from "axios";
import { IBaseEntity } from "../types";
import { onBeforeMount, reactive, watch } from "vue";
import { parseQueryParams, prepareQueryParams } from "../helpers";
import { useRouter } from "vue-router";

const {
    columns = [],
    baseUrl = "",
    client,
} = defineProps<{
    columns: { field: string; header: string }[];
    baseUrl: string;
    client: AxiosInstance;
}>();

const listData = reactive<{
    items: T[];
    params: Record<string, unknown>;
}>({
    items: [],
    params: {},
});

const router = useRouter();

const getItems = async (options?: Record<string, unknown>) => {
    try {
        const params = prepareQueryParams(options);
        const { data } = await client.get(baseUrl, {
            params,
        });

        router.replace({
            query: params,
        });

        listData.items = data;
    } catch (e) {
        console.log(e);
    }
};

onBeforeMount(async () => {
    const parsedParams = parseQueryParams(router.currentRoute.value.query);
    await getItems(parsedParams);
});

watch(
    listData.params,
    async (value) => {
        console.log(value);
        await getItems(value);
    },
    {
        deep: true,
    },
);
</script>
