<template>
    <DataTable
        :value="listData.items"
        showGridlines
        stripedRows
        paginator
        :rows="5"
        :rowsPerPageOptions="[5, 10, 20, 50]"
        @row-click="toEdit"
    >
        <slot v-for="col of columns" :name="`column-${col.field}`">
            <Column
                :key="col.field"
                :field="col.field"
                :header="col.header"
                class="cursor-pointer"
            ></Column>
        </slot>
        <template #paginatorend>
            <Button type="button" icon="pi pi-plus" text @click="toCreate" />
        </template>
    </DataTable>
</template>

<script setup lang="ts" generic="T extends IBaseEntity">
import { AxiosInstance } from "axios";
import { IBaseEntity } from "../types";
import { onBeforeMount, reactive, watch } from "vue";
import { parseQueryParams, prepareQueryParams } from "../helpers";
import { useRouter } from "vue-router";
import { capitalize } from "lodash";

const {
    columns = [],
    baseUrl = "",
    client,
    entity,
    relations = [],
} = defineProps<{
    columns: { field: string; header: string }[];
    baseUrl: string;
    client: AxiosInstance;
    entity: string;
    relations?: string[];
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
        const queryParams = {
            ...options,
            relations: relations,
        };
        const params = prepareQueryParams(queryParams);
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

const toCreate = () => {
    router.push({
        name: `${capitalize(entity)}Create`,
    });
};

const toEdit = ({ data }: { data: T }) => {
    router.push({
        name: `${capitalize(entity)}Detail`,
        params: {
            id: data.id,
        },
    });
};
</script>
