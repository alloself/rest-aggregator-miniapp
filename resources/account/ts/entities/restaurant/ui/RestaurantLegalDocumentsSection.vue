<template>
  <section class="restaurant-legal-documents">
    <div class="restaurant-legal-documents__header">
      <h3 class="restaurant-legal-documents__title">
        Юридические документы
      </h3>
      <p class="restaurant-legal-documents__subtitle">
        Только PDF. Эти файлы будут показаны на публичных legal-страницах
        ресторана.
      </p>
    </div>

    <div class="restaurant-legal-documents__grid">
      <article
        v-for="document in LEGAL_DOCUMENT_FILE_DEFINITIONS"
        :key="document.key"
        class="restaurant-legal-documents__card"
      >
        <div class="restaurant-legal-documents__card-main">
          <div>
            <h4 class="restaurant-legal-documents__card-title">
              {{ document.title }}
            </h4>
            <p class="restaurant-legal-documents__card-description">
              {{ document.description }}
            </p>
          </div>

          <div class="restaurant-legal-documents__meta">
            <template v-if="getDocumentFile(document.key)">
              <a
                :href="getDocumentFile(document.key)?.url"
                class="restaurant-legal-documents__link"
                rel="noopener noreferrer"
                target="_blank"
              >
                {{ getDocumentFile(document.key)?.name }}
              </a>
              <span class="restaurant-legal-documents__status">
                Загружен PDF
              </span>
            </template>

            <span
              v-else
              class="restaurant-legal-documents__status restaurant-legal-documents__status--muted"
            >
              Используется текст по умолчанию
            </span>
          </div>
        </div>

        <div class="restaurant-legal-documents__actions">
          <label class="restaurant-legal-documents__upload">
            <span>
              {{
                loadingByKey[document.key]
                  ? 'Загрузка...'
                  : 'Загрузить PDF'
              }}
            </span>
            <input
              accept="application/pdf"
              class="restaurant-legal-documents__input"
              :disabled="loadingByKey[document.key]"
              type="file"
              @change="onFileSelected(document.key, $event)"
            />
          </label>

          <button
            v-if="getDocumentFile(document.key)"
            class="restaurant-legal-documents__remove"
            type="button"
            @click="removeDocument(document.key)"
          >
            Удалить
          </button>
        </div>

        <p
          v-if="errorByKey[document.key]"
          class="restaurant-legal-documents__error"
        >
          {{ errorByKey[document.key] }}
        </p>
      </article>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import type { AxiosInstance } from 'axios';
import type { FileResourceData } from '@/shared/types/resources';
import {
  LEGAL_DOCUMENT_FILE_DEFINITIONS,
  type LegalDocumentFileKey,
} from '@/shared/constants/legalDocuments';

const props = defineProps<{
  baseUrl: string;
  client: AxiosInstance;
}>();

const files = defineModel<FileResourceData[]>('modelValue', {
  default: () => [],
});

const loadingByKey = ref<Record<LegalDocumentFileKey, boolean>>({
  legal_privacy_pdf: false,
  legal_personal_data_pdf: false,
});

const errorByKey = ref<Record<LegalDocumentFileKey, string | null>>({
  legal_privacy_pdf: null,
  legal_personal_data_pdf: null,
});

function getDocumentFile(key: LegalDocumentFileKey): FileResourceData | undefined {
  return files.value.find((file) => file.pivot?.key === key);
}

function setDocumentFile(
  key: LegalDocumentFileKey,
  file: FileResourceData | null,
): void {
  const otherFiles = files.value.filter((item) => item.pivot?.key !== key);

  if (!file) {
    files.value = otherFiles;

    return;
  }

  files.value = [
    ...otherFiles,
    {
      ...file,
      pivot: {
          id: file.pivot?.id ?? null,
        key,
        order: file.pivot?.order ?? 0,
          type: file.pivot?.type ?? 'file',
      },
    },
  ];
}

function removeDocument(key: LegalDocumentFileKey): void {
  errorByKey.value[key] = null;
  setDocumentFile(key, null);
}

async function onFileSelected(
  key: LegalDocumentFileKey,
  event: Event,
): Promise<void> {
  const input = event.target;
  if (!(input instanceof HTMLInputElement)) {
    return;
  }

  const selectedFile = input.files?.[0];
  input.value = '';

  if (!selectedFile) {
    return;
  }

  const isPdfFile =
    selectedFile.type === 'application/pdf' ||
    selectedFile.name.toLowerCase().endsWith('.pdf');

  if (!isPdfFile) {
    errorByKey.value[key] = 'Можно загрузить только PDF-файл.';

    return;
  }

  errorByKey.value[key] = null;
  loadingByKey.value[key] = true;

  try {
    const formData = new FormData();
    formData.append('file', selectedFile);

    const response = await props.client.post<FileResourceData>(
      props.baseUrl,
      formData,
    );

    setDocumentFile(key, response.data);
  } catch (error) {
    console.error('Failed to upload legal document PDF', error);
    errorByKey.value[key] = 'Не удалось загрузить PDF-файл.';
  } finally {
    loadingByKey.value[key] = false;
  }
}
</script>

<style scoped>
.restaurant-legal-documents {
  margin-top: 1.5rem;
}

.restaurant-legal-documents__header {
  margin-bottom: 1rem;
}

.restaurant-legal-documents__title {
  margin: 0;
  font-size: 1.125rem;
  font-weight: 600;
}

.restaurant-legal-documents__subtitle {
  margin: 0.35rem 0 0;
  color: #6b7280;
  font-size: 0.95rem;
}

.restaurant-legal-documents__grid {
  display: grid;
  gap: 1rem;
}

.restaurant-legal-documents__card {
  border: 1px solid #e5e7eb;
  border-radius: 0.875rem;
  padding: 1rem;
  background: #fff;
}

.restaurant-legal-documents__card-main {
  display: flex;
  gap: 1rem;
  justify-content: space-between;
}

.restaurant-legal-documents__card-title {
  margin: 0;
  font-size: 1rem;
  font-weight: 600;
}

.restaurant-legal-documents__card-description {
  margin: 0.35rem 0 0;
  color: #6b7280;
  font-size: 0.9rem;
  line-height: 1.45;
}

.restaurant-legal-documents__meta {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 0.35rem;
  text-align: right;
}

.restaurant-legal-documents__link {
  color: #2563eb;
  text-decoration: underline;
}

.restaurant-legal-documents__status {
  color: #047857;
  font-size: 0.875rem;
}

.restaurant-legal-documents__status--muted {
  color: #6b7280;
}

.restaurant-legal-documents__actions {
  display: flex;
  gap: 0.75rem;
  margin-top: 1rem;
}

.restaurant-legal-documents__upload,
.restaurant-legal-documents__remove {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-height: 2.5rem;
  padding: 0.625rem 0.9rem;
  border-radius: 999px;
  font-size: 0.9rem;
  font-weight: 500;
  cursor: pointer;
}

.restaurant-legal-documents__upload {
  position: relative;
  overflow: hidden;
  background: #2563eb;
  color: #fff;
}

.restaurant-legal-documents__upload:hover {
  background: #1d4ed8;
}

.restaurant-legal-documents__remove {
  border: 1px solid #d1d5db;
  background: #fff;
  color: #111827;
}

.restaurant-legal-documents__remove:hover {
  background: #f9fafb;
}

.restaurant-legal-documents__input {
  position: absolute;
  inset: 0;
  opacity: 0;
  cursor: pointer;
}

.restaurant-legal-documents__error {
  margin: 0.75rem 0 0;
  color: #dc2626;
  font-size: 0.875rem;
}

@media (max-width: 768px) {
  .restaurant-legal-documents__card-main {
    flex-direction: column;
  }

  .restaurant-legal-documents__meta {
    align-items: flex-start;
    text-align: left;
  }

  .restaurant-legal-documents__actions {
    flex-direction: column;
  }
}
</style>
