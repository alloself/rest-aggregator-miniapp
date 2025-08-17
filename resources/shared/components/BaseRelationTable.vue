<template>
  <div>
    <div class="flex justify-between items-center mb-3">
      <h3>{{ title }}</h3>
    </div>
    <DataTable
      :value="items"
      showGridlines
      stripedRows
      @row-click="toEdit"
      v-model:selection="selectedItems"
      size="small"
    >
      <Column selectionMode="multiple"></Column>
      <slot v-for="column of columns" :name="`column-${column.field}`">
        <Column :key="column.field" :field="column.field" :header="column.header" class="cursor-pointer"></Column>
      </slot>
      <template #footer>
        <div class="flex">
          <div class="flex-1"></div>
          <Button type="button" size="small" icon="pi pi-plus" text @click="toCreate" />
        </div>
      </template>
    </DataTable>
  </div>
</template>

<script setup lang="ts" generic="T extends IBaseEntity">
import { type Component, computed, ref } from "vue";
import { IBaseColumn, IBaseEntity } from "../types";
import { useRouter } from "vue-router";
import { useRightDrawerStore } from "@/account/ts/features/rightDrawer/store";

const { title, columns, entity, detailComponent, initialValues } = defineProps<{
  title: string;
  columns: IBaseColumn[];
  entity: string;
  detailComponent: {
    component: Component;
    props: Record<string, unknown> & {
      onSave?: (payload: IBaseEntity) => void;
      onDelete?: (payload: { id: string }) => void;
    };
  };
  initialValues?: Record<string, unknown>;
}>();

const router = useRouter();

const { pushComponent } = useRightDrawerStore();

const items = defineModel<T[]>("modelValue", {
  default: () => [],
});

const selectedItems = ref<T[]>([]);

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
