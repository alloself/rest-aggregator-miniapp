import type { ISmartFormField } from "@/shared/types";
import { computed } from "vue";
import BaseInput from "@/shared/components/BaseInput.vue";
import BaseWorkingHoursEditor from "@/shared/components/BaseWorkingHoursEditor.vue";
import { z } from "zod";
import BaseFileList from "@/shared/components/BaseFileList.vue";
import { client } from "@/account/ts/shared/api/axios";
import { FILE_BASE_URL } from "../../category/const";
import BaseTree from "@/shared/components/BaseTree.vue";
import BaseEditor from "@/shared/components/BaseEditor.vue";
import BaseRelationTable from "@/shared/components/BaseRelationTable.vue";
import { NewsDetail } from "../../news";

export const useRestaurantDetailFormFields = (props: { id?: string }) => {
  const fields = computed<ISmartFormField[]>(() => [
    {
      component: BaseInput,
      key: "name",
      props: {
        type: "name",
        fluid: true,
        autocomplete: "restaurant_name",
        label: "Название",
        name: "name",
      },
      rule: z
        .string({
          required_error: "Название обязательно",
        })
        .min(1, "Название обязательно"),
    },
    {
      component: BaseInput,
      key: "subtitle",
      props: {
        type: "subtitle",
        fluid: true,
        autocomplete: "restaurant_name",
        label: "Подзаголовок",
        name: "subtitle",
      },
    },
    {
      component: BaseInput,
      key: "slug",
      props: {
        type: "slug",
        label: "Slug",
        fluid: true,
        autocomplete: "restaurant_slug",
        name: "slug",
      },
    },
    {
      component: BaseInput,
      key: "yandex_metrica_code",
      props: {
        type: "yandex_metrica_code",
        label: "Код метрики",
        fluid: true,
        autocomplete: "restaurant_yandex_metrica_code",
        name: "yandex_metrica_code",
      },
    },
    {
      component: BaseInput,
      key: "average_receipt",
      props: {
        type: "average_receipt",
        label: "Средний чек",
        fluid: true,
        autocomplete: "restaurant_average_receipt",
        name: "average_receipt",
      },
    },
    {
      component: BaseInput,
      key: "address",
      props: {
        type: "address",
        label: "Адрес",
        fluid: true,
        autocomplete: "restaurant_address",
        name: "address",
      },
    },
    {
      component: BaseEditor,
      key: "description",
      props: {
        type: "description",
        title: "Описание",
        fluid: true,
        autocomplete: "restaurant_description",
        name: "description",
      },
    },
    {
      component: BaseWorkingHoursEditor,
      key: "working_hours",
    },
    {
      component: BaseFileList,
      props: {
        type: "file",
        baseUrl: FILE_BASE_URL,
        client: client,
        title: "Файлы",
      },
      key: "files",
    },
    {
      component: BaseFileList,
      props: {
        type: "image",
        baseUrl: FILE_BASE_URL,
        client: client,
        title: "Изображения",
      },
      key: "images",
    },
    {
      component: BaseRelationTable,
      props: {
        title: "Новости",
        entity: "news",
        columns: [
          {
            field: "title",
            header: "Название",
          },
          {
            field: "order",
            header: "Порядок",
          },
        ],
        detailComponent: NewsDetail,
        initialValues: {
          restaurant_id: props.id,
        },
      },
      key: "news",
    },
    {
      component: BaseTree,
      props: {
        title: "Категории",
        columns: [
          {
            field: "name",
            header: "Название",
          },
        ],
      },
      key: "categories",
    },
  ]);

  return {
    fields,
  };
};
