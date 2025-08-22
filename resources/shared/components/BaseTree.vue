<template>
  <div>
    <h3>{{ title }}</h3>
    <TreeTable
      :value="treeNodes"
      size="small"
      selectionMode="single"
      v-model:selectionKeys="selectedKeys"
      :metaKeySelection="false"
      @nodeSelect="onNodeSelect"
    >
      <Column expander style="width: 3rem" />
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
import { Component, computed, ref, watch } from 'vue';
import TreeTable from 'primevue/treetable';
import Column from 'primevue/column';
import type { TreeNode } from 'primevue/treenode';
import type { IBaseEntity, IBaseTreeEntity, IBaseColumn } from '../types';
import { useRightDrawerStore } from '@/account/ts/features/rightDrawer/store';
import { useTreeSyncStore } from '../stores/treeSync';

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
const treeSync = useTreeSyncStore();
const instanceId = Symbol('BaseTreeInstance');
const channel = computed(() => title || 'default-tree');

const items = defineModel<T[]>('modelValue', {
  default: () => [],
});

const treeNodes = computed<TreeNode[]>(() => {
  return items.value.map((item) => transformToTreeNode(item));
});

const selectedKeys = ref<Record<string, boolean>>({});
const lastSelected = ref<T | null>(null);

function transformToTreeNode(item: T): TreeNode {
  return {
    key: item.id,
    data: item,
    ...item,
    children: item.children ? item.children.map((child) => transformToTreeNode(child)) : undefined,
  };
}

const handleSaved = (saved: T, options?: { suppressSync?: boolean }) => {
  const next = [...items.value];

  function hasParentId(value: unknown): value is { parent_id?: string | null } {
    return typeof value === 'object' && value !== null && 'parent_id' in value;
  }

  const fallbackParentId = lastSelected.value ? (lastSelected.value as unknown as { id: string }).id : null;
  const parentId = hasParentId(saved) ? (saved.parent_id ?? fallbackParentId) : fallbackParentId;

  function upsertInPlace(list: T[]): boolean {
    for (let i = 0; i < list.length; i++) {
      const node = list[i];

      // 1) Обновление существующего узла по id
      if (node.id === saved.id) {
        list.splice(i, 1, saved);
        return true;
      }

      // 2) Попытка вставить как ребёнка к найденному родителю
      const children = Array.isArray(node.children) ? node.children : [];
      if (parentId && node.id === parentId) {
        node.children = [...children, saved];
        return true;
      }

      // 3) Рекурсивный проход по дочерним узлам
      if (children.length) {
        const handled = upsertInPlace(children);
        if (handled) {
          node.children = children;
          return true;
        }
      }
    }
    return false;
  }

  const handled = upsertInPlace(next);
  items.value = handled ? next : [...next, saved];

  if (!options?.suppressSync) {
    treeSync.notifyUpsert(channel.value, saved as unknown as Record<string, unknown>, instanceId);
    const external = detailComponent.props && detailComponent.props.onSave;
    if (external) external(saved);
  }
};

const handleDeleted = (payload: { id: string }, options?: { suppressSync?: boolean }) => {
  const next = [...items.value];

  function removeInPlace(list: T[]): boolean {
    for (let i = 0; i < list.length; i++) {
      const node = list[i];
      if (node.id === payload.id) {
        list.splice(i, 1);
        return true;
      }
      const children = Array.isArray(node.children) ? node.children : [];
      if (children.length) {
        const handled = removeInPlace(children);
        if (handled) {
          node.children = children;
          return true;
        }
      }
    }
    return false;
  }

  const handled = removeInPlace(next);
  items.value = handled ? next : next.filter((i) => i.id !== payload.id);

  if (!options?.suppressSync) {
    treeSync.notifyDelete(channel.value, payload.id, instanceId);
    const external = detailComponent.props && detailComponent.props.onDelete;
    if (external) external(payload);
  }
};

const toEdit = ({ data }: { data: T }) => {
  lastSelected.value = data;
  pushComponent(detailComponent.component, {
    id: data.id,
    initialValues,
    ...detailComponent.props,
    onSave: handleSaved,
    onDelete: handleDeleted,
    modal: true,
  });
};
const toCreate = () => {
  const mergedInitialValues = {
    ...(initialValues || {}),
    ...(lastSelected.value ? { parent_id: (lastSelected.value as unknown as { id: string }).id } : {}),
  };

  pushComponent(detailComponent.component, {
    initialValues: mergedInitialValues,
    ...detailComponent.props,
    onSave: handleSaved,
    onDelete: handleDeleted,
    modal: true,
  });
};

function isObject(value: unknown): value is Record<string, unknown> {
  return typeof value === 'object' && value !== null;
}

function isTreeNodeValue(value: unknown): value is TreeNode {
  return isObject(value) && 'data' in value;
}

const onNodeSelect = (payload: unknown) => {
  let selectedData: T | null = null;

  if (isTreeNodeValue(payload)) {
    selectedData = payload.data as T;
  } else if (isObject(payload)) {
    if ('node' in payload) {
      const nodeCandidate = (payload as Record<string, unknown>).node;
      if (isTreeNodeValue(nodeCandidate)) {
        selectedData = nodeCandidate.data as T;
      }
    } else if ('data' in payload) {
      selectedData = (payload as Record<string, unknown>).data as T;
    }
  }

  if (selectedData) {
    lastSelected.value = selectedData;
    toEdit({ data: selectedData });
  }
};

// Listen sync events from other BaseTree instances in same channel
watch(
  () => treeSync.version,
  () => {
    // upsert
    const up = treeSync.lastUpsert;
    if (up && up.channel === channel.value && up.sourceId !== instanceId) {
      handleSaved(up.node as unknown as T, { suppressSync: true });
    }
    // delete
    const del = treeSync.lastDelete;
    if (del && del.channel === channel.value && del.sourceId !== instanceId) {
      handleDeleted({ id: del.id }, { suppressSync: true });
    }
  },
);
</script>
