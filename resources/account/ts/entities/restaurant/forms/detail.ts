import type { Category, ISmartFormField, News } from '@/shared/types';
import { computed } from 'vue';
import BaseInput from '@/shared/components/BaseInput.vue';
import BaseWorkingHoursEditor from '@/shared/components/BaseWorkingHoursEditor.vue';
import { z } from 'zod';
import BaseFileList from '@/shared/components/BaseFileList.vue';
import { client } from '@/account/ts/shared/api/axios';
import { FILE_BASE_URL } from '../../category/const';
import BaseTree from '@/shared/components/BaseTree.vue';
import BaseEditor from '@/shared/components/BaseEditor.vue';
import BaseRelationTable from '@/shared/components/BaseRelationTable.vue';
import { NewsDetail } from '../../news';
import { EventsDetail } from '../../events';
import { CategoryDetail } from '../../category';

export const useRestaurantDetailFormFields = (props: { id?: string }) => {
  const fields = computed<ISmartFormField[]>(() => [
    {
      component: BaseInput,
      key: 'name',
      props: {
        type: 'name',
        fluid: true,
        autocomplete: 'restaurant_name',
        label: 'Название',
        name: 'name',
      },
      rule: z
        .string({
          required_error: 'Название обязательно',
        })
        .min(1, 'Название обязательно'),
    },
    {
      component: BaseInput,
      key: 'subtitle',
      props: {
        type: 'subtitle',
        fluid: true,
        autocomplete: 'restaurant_name',
        label: 'Подзаголовок',
        name: 'subtitle',
      },
    },
    {
      component: BaseInput,
      key: 'phone',
      props: {
        type: 'phone',
        fluid: true,
        autocomplete: 'restaurant_phone',
        label: 'Телефон',
        name: 'phone',
      },
    },
    {
      component: BaseInput,
      key: 'slug',
      props: {
        type: 'slug',
        label: 'Slug',
        fluid: true,
        autocomplete: 'restaurant_slug',
        name: 'slug',
      },
    },
    {
      component: BaseInput,
      key: 'yandex_metrica_code',
      props: {
        type: 'yandex_metrica_code',
        label: 'Код метрики',
        fluid: true,
        autocomplete: 'restaurant_yandex_metrica_code',
        name: 'yandex_metrica_code',
      },
    },
    {
      component: BaseInput,
      key: 'average_receipt',
      props: {
        type: 'average_receipt',
        label: 'Средний чек',
        fluid: true,
        autocomplete: 'restaurant_average_receipt',
        name: 'average_receipt',
      },
    },
    {
      component: BaseInput,
      key: 'address',
      props: {
        type: 'address',
        label: 'Адрес',
        fluid: true,
        autocomplete: 'restaurant_address',
        name: 'address',
      },
    },
    {
      component: BaseInput,
      key: 'telegram_bot_token',
      props: {
        type: 'text',
        label: 'Токен Telegram бота',
        fluid: true,
        autocomplete: 'telegram_bot_token',
        name: 'telegram_bot_token',
        placeholder: 'Введите токен вашего Telegram бота',
      },
    },
    {
      component: BaseEditor,
      key: 'description',
      props: {
        title: 'Описание',
        autocomplete: 'restaurant_description',
        name: 'description',
      },
    },
    {
      component: BaseEditor,
      key: 'welcome_message',
      props: {
        title: 'Приветственное сообщение бота телеграм',
        autocomplete: 'restaurant_welcome_message',
        name: 'welcome_message',
      },
    },
    {
      component: BaseWorkingHoursEditor,
      key: 'working_hours',
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
    {
      component: BaseRelationTable,
      props: {
        title: 'Новости',
        entity: 'news',
        columns: [
          {
            field: 'title',
            header: 'Название',
          },
          {
            field: 'order',
            header: 'Порядок',
          },
        ],
        detailComponent: {
          component: NewsDetail,
          props: {
            onSave: (data: News) => {
              console.log(data);
            },
          },
        },
        initialValues: {
          restaurant_id: props.id,
        },
      },
      key: 'news',
    },
    {
      component: BaseRelationTable,
      props: {
        title: 'События',
        entity: 'events',
        columns: [
          {
            field: 'title',
            header: 'Название',
          },
          {
            field: 'subtitle',
            header: 'Подзаголовок',
          },
          {
            field: 'start_at',
            header: 'Дата начала',
          },
          {
            field: 'price',
            header: 'Цена',
          },

          {
            field: 'order',
            header: 'Порядок',
          },
        ],
        detailComponent: {
          component: EventsDetail,
          props: {
            onSave: (data: Event) => {
              console.log(data);
            },
          },
        },
        initialValues: {
          restaurant_id: props.id,
        },
      },
      key: 'events',
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
          restaurant_id: props.id,
        },
      },
      key: 'categories',
    },
  ]);

  return {
    fields,
  };
};
