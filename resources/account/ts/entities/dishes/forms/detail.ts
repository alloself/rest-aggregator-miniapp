import type { ISmartFormField } from '@/shared/types';
import { computed } from 'vue';
import BaseInput from '@/shared/components/BaseInput.vue';
import BaseNumberInput from '@/shared/components/BaseNumberInput.vue';
import BaseFileList from '@/shared/components/BaseFileList.vue';
import { FILE_BASE_URL } from '../../category/const';
import { client } from '@/account/ts/shared/api/axios';
import { z } from 'zod';

export const useDishesDetailFormFields = (props?: { category_id?: string }) => {
  const fields = computed<ISmartFormField[]>(() => [
    {
      component: BaseInput,
      key: 'name',
      props: {
        type: 'name',
        fluid: true,
        autocomplete: 'dish_name',
        label: 'Название',
        name: 'name',
      },
      rule: z
        .string({ required_error: 'Название обязательно' })
        .min(1, 'Название обязательно'),
    },
    {
      component: BaseInput,
      key: 'slug',
      props: {
        type: 'slug',
        label: 'Slug',
        fluid: true,
        autocomplete: 'dish_slug',
        name: 'slug',
      },
    },
    {
      component: BaseNumberInput,
      key: 'order',
      props: {
        type: 'order',
        label: 'Порядок',
        fluid: true,
        autocomplete: 'dish_order',
        name: 'order',
      },
    },
    {
      component: BaseNumberInput,
      key: 'price',
      props: {
        type: 'number',
        label: 'Цена',
        fluid: true,
        autocomplete: 'dish_price',
        name: 'price',
      },
    },
    {
      component: BaseInput,
      key: 'description',
      props: {
        type: 'description',
        label: 'Описание',
        fluid: true,
        autocomplete: 'dish_description',
        name: 'description',
      },
    },
    {
      component: BaseFileList,
      props: {
        type: 'file',
        baseUrl: FILE_BASE_URL,
        client: client,
        title: 'Файлы',
      },
      key: 'files',
    },
    {
      component: BaseFileList,
      props: {
        type: 'image',
        baseUrl: FILE_BASE_URL,
        client: client,
        title: 'Изображения',
      },
      key: 'images',
    },
  ]);

  return { fields };
};


