<template>
  <div>
    <h3>{{ title }}</h3>
    <TreeTable
      :value="treeNodes"
      size="small"
      showGridlines
      stripedRows
      selectionMode="checkbox"
      v-model:selectionKeys="selectedKeys"
      :metaKeySelection="false"
    >
      <Column expander style="width: 3rem" />
      <Column v-for="col of columns" :key="col.field" :field="col.field" :header="col.header" />
      <Column header="Ключ" style="min-width: 10rem">
        <template #body="{ node }">
          <InputText v-if="hasPivot(node.data)" v-model="pivotOf(node.data).key" placeholder="key" size="small" />
          <span v-else>&nbsp;</span>
        </template>
      </Column>
      <Column header="Порядок" style="width: 8rem">
        <template #body="{ node }">
          <InputNumber
            v-if="hasPivot(node.data)"
            v-model="pivotOf(node.data).order"
            :min="0"
            :step="1"
            showButtons
            buttonLayout="horizontal"
            incrementButtonClass="p-button-text"
            decrementButtonClass="p-button-text"
          />
          <InputNumber
            v-else
            :modelValue="orderOf(node.data)"
            @update:modelValue="handleUpdateChildOrder(node.data, $event)"
            :min="0"
            :step="1"
            showButtons
            buttonLayout="horizontal"
            incrementButtonClass="p-button-text"
            decrementButtonClass="p-button-text"
          />
        </template>
      </Column>
      <Column header="Действия" style="width: 4rem">
        <template #body="{ node }">
          <Button icon="pi pi-pencil" text @click="toEdit({ data: node.data })" />
        </template>
      </Column>
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
import { Component, computed, ref, onBeforeUnmount } from 'vue';
import TreeTable from 'primevue/treetable';
import Column from 'primevue/column';
import type { TreeNode } from 'primevue/treenode';
import type { IBaseEntity, IBaseTreeEntity, IBaseColumn, PivotData } from '../types';
import { useRightDrawerStore } from '@/account/ts/features/rightDrawer/store';
import type { AxiosInstance } from 'axios';
import { debounce } from 'lodash';
import type { DebouncedFunc } from 'lodash';

const {
  columns = [],
  title = '',
  detailComponent,
  initialValues,
  baseUrl,
  client,
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
  baseUrl?: string;
  client?: AxiosInstance;
}>();

const { pushComponent } = useRightDrawerStore();

const items = defineModel<T[]>('modelValue', {
  default: () => [],
});

function compareRootByPivotOrder(a: T, b: T): number {
  const ao = pivotOf(a).order ?? 0;
  const bo = pivotOf(b).order ?? 0;
  return ao - bo;
}

const treeNodes = computed<TreeNode[]>(() => {
  const roots = [...items.value];
  roots.sort(compareRootByPivotOrder);
  return roots.map((item) => transformToTreeNode(item, 0));
});

const selectedKeys = ref<Record<string, boolean>>({});
const lastSelected = ref<T | null>(null);
const orderUpdateDebouncers = ref<Record<string, DebouncedFunc<(latest_item: T) => Promise<void>>>>({});
const previousChildOrder = ref<Record<string, number | undefined>>({});
const pendingOrderUpdate = ref<Record<string, boolean>>({});

function hasPivot(item: T): boolean {
  return !!item.pivot && typeof item.pivot === 'object';
}

function pivotOf(item: T): PivotData {
  return item.pivot ?? {};
}

function ensureRootPivot(item: T): void {
  if (!item.pivot) {
    item.pivot = { order: 0, key: '' };
  } else if (typeof item.pivot.order !== 'number') {
    item.pivot.order = 0;
  }
}

function orderOf(item: T): number {
  return typeof item.order === 'number' ? item.order : 0;
}

function setOrder(item: T, value: number): void {
  item.order = Number(value ?? 0);
}

function compareChildByOrder(a: T, b: T): number {
  const ao = orderOf(a);
  const bo = orderOf(b);
  return ao - bo;
}

function transformToTreeNode(item: T, depth = 0): TreeNode {
  if (depth === 0 && !hasPivot(item)) {
    ensureRootPivot(item);
  }
  const childrenRaw: T[] = Array.isArray(item.children) ? (item.children as T[]) : [];
  const childrenSorted = [...childrenRaw].sort(compareChildByOrder);
  return {
    key: item.id,
    data: item,
    children: childrenSorted.length ? childrenSorted.map((child) => transformToTreeNode(child, depth + 1)) : undefined,
  };
}

function upsertNodeInPlace(list: T[], saved: T, parentId: string | null): boolean {
  for (let i = 0; i < list.length; i++) {
    const node = list[i];

    // Обновление существующего узла по id
    if (node.id === saved.id) {
      list.splice(i, 1, saved);
      return true;
    }

    // Попытка вставить как ребёнка к найденному родителю
    const children = Array.isArray(node.children) ? node.children : [];
    if (parentId && node.id === parentId) {
      node.children = [...children, saved];
      return true;
    }

    // Рекурсивный проход по дочерним узлам
    if (children.length) {
      const handled = upsertNodeInPlace(children, saved, parentId);
      if (handled) {
        node.children = children;
        return true;
      }
    }
  }
  return false;
}

function removeNodeInPlace(list: T[], id: string): boolean {
  for (let i = 0; i < list.length; i++) {
    const node = list[i];
    if (node.id === id) {
      list.splice(i, 1);
      return true;
    }
    const children = Array.isArray(node.children) ? node.children : [];
    if (children.length) {
      const handled = removeNodeInPlace(children, id);
      if (handled) {
        node.children = children;
        return true;
      }
    }
  }
  return false;
}

const handleSaved = (saved: T) => {
  const next = [...items.value];

  const fallbackParentId = lastSelected.value ? lastSelected.value.id : null;
  const maybeParent = saved.parent_id;
  const parentId = typeof maybeParent === 'string' ? maybeParent : fallbackParentId;

  const handled = upsertNodeInPlace(next, saved, parentId);
  items.value = handled ? next : [...next, saved];
};

const handleDeleted = (payload: { id: string }) => {
  const next = [...items.value];

  const handled = removeNodeInPlace(next, payload.id);
  items.value = handled ? next : next.filter((i) => i.id !== payload.id);
};

async function sendChildOrderUpdate(id: string, item: T): Promise<void> {
  if (!client || !baseUrl) return;
  try {
    await client.put(`${baseUrl}/${id}`, { order: orderOf(item) });
    pendingOrderUpdate.value[id] = false;
    delete previousChildOrder.value[id];
  } catch (e) {
    const prev = previousChildOrder.value[id];
    if (typeof prev === 'number') {
      setOrder(item, prev);
    }
    pendingOrderUpdate.value[id] = false;
    delete previousChildOrder.value[id];
  }
}

function sendChildOrderUpdateWithId(id: string, latest_item: T): Promise<void> {
  return sendChildOrderUpdate(id, latest_item);
}

function createOrderUpdateDebouncer(id: string): DebouncedFunc<(latest_item: T) => Promise<void>> {
  return debounce(sendChildOrderUpdateWithId.bind(null, id), 300);
}

function getOrderUpdateDebouncer(id: string): DebouncedFunc<(latest_item: T) => Promise<void>> {
  const debouncers = orderUpdateDebouncers.value;
  if (!debouncers[id]) {
    debouncers[id] = createOrderUpdateDebouncer(id);
  }
  return debouncers[id];
}

const handleChildOrderChanged = (item: T) => {
  if (!client || !baseUrl) return;
  const id = item.id;
  if (!id) return;
  const debouncer = getOrderUpdateDebouncer(id);
  debouncer(item);
};

const onChildOrderInput = (item: T, value: number) => {
  const id = item.id;
  if (id) {
    const pending = pendingOrderUpdate.value[id] === true;
    if (!pending) {
      previousChildOrder.value[id] = orderOf(item);
      pendingOrderUpdate.value[id] = true;
    }
  }
  setOrder(item, value);
  handleChildOrderChanged(item);
};

function handleUpdateChildOrder(item: T, value: unknown): void {
  const parsed = Number(value);
  onChildOrderInput(item, parsed);
}

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
    ...(lastSelected.value ? { parent_id: lastSelected.value.id } : {}),
  };

  pushComponent(detailComponent.component, {
    initialValues: mergedInitialValues,
    ...detailComponent.props,
    onSave: handleSaved,
    onDelete: handleDeleted,
    modal: true,
  });
};

onBeforeUnmount(() => {
  const debouncers = orderUpdateDebouncers.value;
  Object.keys(debouncers).forEach((key) => {
    debouncers[key].cancel();
  });
});
</script>
