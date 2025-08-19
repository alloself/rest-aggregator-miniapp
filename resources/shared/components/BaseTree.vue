<template>
  <div>
    <h3>{{ title }}</h3>
    <TreeTable :value="treeNodes" size="small">
      <Column v-for="col of columns" :key="col.field" :field="col.field" :header="col.header" />
      <template #footer>
        <div class="flex">
          <div class="flex-1" />
          <Button type="button" icon="pi pi-plus" text @click="toCreate" />
        </div>
      </template>
    </TreeTable>
  </div>
</template>

<script setup lang="ts" generic="T extends IBaseEntity & IBaseTreeEntity<T>">
import { Component, computed } from 'vue';
import TreeTable from 'primevue/treetable';
import Column from 'primevue/column';
import type { TreeNode } from 'primevue/treenode';
import type { IBaseEntity, IBaseTreeEntity, IBaseColumn } from '../types';
import { useRightDrawerStore } from '@/account/ts/features/rightDrawer/store';

const {
  columns = [],
  title = '',
  detailComponent,
  initialValues,
} = defineProps<{
  columns?: IBaseColumn[];
  title?: string;
  detailComponent: {
    component: Component;
    props: Record<string, unknown> & {
      onSave?: (payload: IBaseEntity) => void;
      onDelete?: (payload: { id: string }) => void;
    };
  };
  initialValues?: Record<string, unknown>;
}>();

const { pushComponent } = useRightDrawerStore();

const items = defineModel<T[]>('modelValue', {
  default: () => [],
});

const treeNodes = computed<TreeNode[]>(() => {
  return items.value.map((item) => transformToTreeNode(item));
});

function transformToTreeNode(item: T): TreeNode {
  return {
    key: item.id,
    data: item,
    ...item,
    children: item.children ? item.children.map((child) => transformToTreeNode(child)) : undefined,
  };
}

const handleSaved = (saved: T) => {
  const index = items.value.findIndex((i) => i.id === saved.id);
  if (index >= 0) {
    const next = [...items.value];
    next.splice(index, 1, saved);
    items.value = next;
  } else {
    items.value = [...items.value, saved];
  }
  const external = detailComponent.props && detailComponent.props.onSave;
  if (external) external(saved);
};

const handleDeleted = (payload: { id: string }) => {
  const next = items.value.filter((i) => i.id !== payload.id);
  items.value = next;
  const external = detailComponent.props && detailComponent.props.onDelete;
  if (external) external(payload);
};

const toEdit = ({ data }: { data: T }) => {
  pushComponent(detailComponent.component, {
    id: data.id,
    initialValues,
    ...detailComponent.props,
    onSave: handleSaved,
    onDelete: handleDeleted,
  });
};
const toCreate = () => {
  pushComponent(detailComponent.component, {
    initialValues,
    ...detailComponent.props,
    onSave: handleSaved,
    onDelete: handleDeleted,
  });
};
</script>
