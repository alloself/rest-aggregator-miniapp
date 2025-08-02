<template>
  <Card class="flex flex-col h-full" :pt="{ body: { class: 'flex-1' }, content: { class: 'flex-1' } }">
    <template #title>
      <slot name="title">
        <h3 class="text-lg font-medium">{{ title }}</h3>
      </slot>
    </template>
    <template #content>
      <SmartForm class="flex-1" :fields="fields" v-model:form="form" :initialValues="initialValues" />
    </template>

    <template #footer>
      <slot name="footer">
        <div class="flex gap-4 mt-1">
          <div class="flex-1" />
          <Button label="Cancel" severity="secondary" outlined @click="onCancel" />
          <Button label="Save" @click="handleSubmit" />
        </div>
      </slot>
    </template>
  </Card>
</template>
<script setup lang="ts" generic="T extends IBaseEntity">
import { FormContext } from "vee-validate";
import { IBaseEntity, ISmartFormField } from "../types";
import SmartForm from "./SmartForm.vue";
import { AxiosInstance } from "axios";
import { useFormSubmit } from "../composables";
import { onMounted, ref } from "vue";
import { useRouter } from "vue-router";
import { capitalize } from "lodash";

const {
  fields = [],
  id,
  title = "Создание",
  client,
  baseUrl,
  initialValues,
  entity,
  relations = [],
} = defineProps<{
  fields: ISmartFormField[];
  title?: string;
  entity: string;
  client: AxiosInstance;
  baseUrl: string;
  initialValues?: Partial<T>;
  id?: string;
  relations?: string[];
}>();

const emit = defineEmits<{ save: [value: FormContext<T>] }>();

const router = useRouter();

const form = defineModel<FormContext<T>>("form");

const isLoading = ref(false);

const onSave = async () => {
  isLoading.value = true;
  const { data } = await client.post(baseUrl, form.value?.values, {
    params: {
      relations,
    },
  });

  isLoading.value = false;
  router.push({
    name: `${capitalize(entity)}Detail`,
    params: {
      id: data.id,
    },
  });
};

const onEdit = async () => {
  isLoading.value = true;
  await client.put(`${baseUrl}/${id}`, form.value?.values, {
    params: {
      relations,
    },
  });
  isLoading.value = false;
};

const { handler: handleSubmit } = useFormSubmit(id ? onEdit : onSave, form);

const getItem = async () => {
  const { data } = await client.get(`${baseUrl}/${id}`, {
    params: {
      relations,
    },
  });
  form.value?.setValues(data);
};

const onCancel = () => {
  router.back();
};

onMounted(() => {
  if (id) {
    getItem();
  }
});
</script>
