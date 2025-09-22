import type { ISmartFormField } from "@/shared/types";
import { computed } from "vue";
import InputText from "primevue/inputtext";
import { z } from "zod";
import BaseTree from "@/shared/components/BaseTree.vue";
import CategoryDetail from "../ui/CategoryDetail.vue";
import { Category } from "@/shared/types";
import BaseRelationTable from "@/shared/components/BaseRelationTable.vue";
import { DishesDetail } from "../../dishes/index";

export const useCategoryDetailFormFields = (props: { id?: string }) => {
    const fields = computed<ISmartFormField[]>(() => [
        {
            component: InputText,
            key: "name",
            props: {
                type: "name",
                placeholder: "Название",
                fluid: true,
                autocomplete: "category_name",
                name: "name",
            },
            rule: z
                .string({
                    required_error: "Название обязательно",
                })
                .min(1, "Название обязательно"),
        },
        {
            component: BaseTree,
            props: {
              title: 'Категории',
              columns: [
                {
                  field: 'name',
                  header: 'Название',
                },
              ],
              detailComponent: {
                component: CategoryDetail,
                props: {
                  onSave: (data: Category) => {
                    console.log(data);
                  },
                },
              },
              initialValues: {
                parent_id: props.id,
              },
            },
            key: 'children',
          },
        {
            component: BaseRelationTable,
            props: {
              title: 'Блюда',
              entity: 'dishes',
              columns: [
                { field: 'name', header: 'Название' },
                { field: 'order', header: 'Порядок' },
              ],
              detailComponent: {
                component: DishesDetail,
                props: {},
              },
              initialValues: {
                category_id: props.id,
              },
            },
            key: 'dishes',
          },
    ]);

    return {
        fields,
    };
};
