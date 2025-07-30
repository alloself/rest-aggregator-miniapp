<template>
    <div>
        <h3>{{ title }}</h3>
        <TreeTable :value="treeNodes" size="small">
            <Column
                v-for="col of columns"
                :key="col.field"
                :field="col.field"
                :header="col.header"
            />
            <template #footer>
                <div class="flex ">
                    <div class="flex-1"/>
                    <Button type="button" icon="pi pi-plus" text @click="onCreate" />
                </div>
            </template>
        </TreeTable>
    </div>
</template>

<script setup lang="ts" generic="T extends IBaseTreeEntity<T> & IBaseEntity">
import { computed } from "vue";
import TreeTable from "primevue/treetable";
import Column from "primevue/column";
import type { TreeNode } from "primevue/treenode";
import type { IBaseEntity, IBaseTreeEntity, IBaseColumn } from "../types";
import type { Component } from "vue";
import { useRightDrawer } from "../composables/useRightDrawer";
import type { RightDrawerData, RightDrawerProps } from "../types/rightDrawer";

const {
    columns = [],
    title = "",
    createComponent,
    createTitle = "Создание элемента",
    createProps,
} = defineProps<{
    columns?: IBaseColumn[];
    title?: string;
    createComponent?: Component;
    createTitle?: string;
    createProps?: RightDrawerProps;
}>();

const emit = defineEmits<{
    create: [data: RightDrawerData];
}>();

const items = defineModel<T[]>("items", {
    default: () => [],
});

const { push } = useRightDrawer();

const treeNodes = computed<TreeNode[]>(() => {
    return items.value.map((item) => transformToTreeNode(item));
});

function transformToTreeNode(item: T): TreeNode {
    return {
        key: item.id,
        data: item,
        ...item,
        children: item.children
            ? item.children.map((child) => transformToTreeNode(child))
            : undefined,
    };
}

const onCreate = (): void => {
    if (createComponent) {
        push(createComponent, {
            title: createTitle,
            props: createProps,
            onSave: (data: RightDrawerData) => {
                emit("create", data);
            },
            onClose: () => {
                console.log("Создание отменено");
            },
        });
    } else {
        console.log("onCreate: компонент создания не задан");
    }
};
</script>
