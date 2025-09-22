import type { ISmartFormField } from '@/shared/types';
import { computed } from 'vue';
import BaseInput from '@/shared/components/BaseInput.vue';
import BaseEditor from '@/shared/components/BaseEditor.vue';
import { z } from 'zod';
import BaseFileList from '@/shared/components/BaseFileList.vue';
import { FILE_BASE_URL } from '../../category/const';
import { client } from '@/account/ts/shared/api/axios';
import BaseNumberInput from '@/shared/components/BaseNumberInput.vue';
import BaseDatePicker from '@/shared/components/BaseDatePicker.vue';
import BaseCheckbox from '@/shared/components/BaseCheckbox.vue';

export const useEventsDetailFormFields = () => {
  const fields = computed<ISmartFormField[]>(() => [
    {
      component: BaseInput,
      key: 'title',
      props: {
        type: 'title',
        fluid: true,
        autocomplete: 'event_title',
        label: 'Заголовок',
        name: 'title',
      },
      rule: z
        .string({
          required_error: 'Название обязательно',
        })
        .min(1, 'Название обязательно'),
    },
    {
      component: BaseInput,
      key: 'slug',
      props: {
        label: 'Slug',

        autocomplete: 'event_slug',
        name: 'slug',
      },
    },
    {
      component: BaseInput,
      key: 'subtitle',
      props: {
        label: 'Подзаголовок',
        autocomplete: 'event_subtitle',
        name: 'subtitle',
      },
    },
    {
      component: BaseDatePicker,
      key: 'start_at',
      props: {
        label: 'Дата и время начала',
        name: 'start_at',
        showTime: true,
        hourFormat: '24',
        showIcon: true,
      },
      rule: z.date({ required_error: 'Укажите дату и время' }),
    },
    {
      component: BaseCheckbox,
      key: 'is_sold_out',
      props: {
        label: 'Распродано',
        name: 'is_sold_out',
      },
      rule: z.boolean().optional(),
    },
    {
      component: BaseNumberInput,
      key: 'order',
      props: {
        label: 'Порядок',
        autocomplete: 'event_order',
        name: 'order',
      },
    },
    {
      component: BaseEditor,
      key: 'description',
      props: {
        title: 'Описание',
        autocomplete: 'event_description',
        name: 'description',
      },
    },
    {
      component: BaseNumberInput,
      key: 'price ',
      props: {
        type: 'number',
        label: 'Цена',
        autocomplete: 'event_price',
        name: 'price',
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

  return {
    fields,
  };
};
