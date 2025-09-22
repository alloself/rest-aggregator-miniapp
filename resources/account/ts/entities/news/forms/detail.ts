import type { ISmartFormField } from '@/shared/types';
import { computed } from 'vue';
import BaseInput from '@/shared/components/BaseInput.vue';
import BaseEditor from '@/shared/components/BaseEditor.vue';
import { z } from 'zod';
import BaseFileList from '@/shared/components/BaseFileList.vue';
import { FILE_BASE_URL } from '../../category/const';
import { client } from '@/account/ts/shared/api/axios';
import BaseNumberInput from '@/shared/components/BaseNumberInput.vue';
export const useNewsDetailFormFields = () => {
  const fields = computed<ISmartFormField[]>(() => [
    {
      component: BaseInput,
      key: 'title',
      props: {
        type: 'title',
        fluid: true,
        autocomplete: 'news_title',
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
        type: 'slug',
        label: 'Slug',
        fluid: true,
        autocomplete: 'news_slug',
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
        autocomplete: 'news_order',
        name: 'order',
      },
    },
    {
      component: BaseEditor,
      key: 'description',
      props: {
        type: 'description',
        title: 'Описание',
        fluid: true,
        autocomplete: 'news_description',
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

  return {
    fields,
  };
};
