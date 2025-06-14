<template>
  <form @submit="onSubmit" class="smart-form">
    <div
      v-for="field in fields"
      :key="field.name"
      class="form-field"
      :class="`form-field--${field.type}`"
    >
      <!-- Text/Email/Password Fields -->
      <template v-if="['text', 'email', 'password'].includes(field.type)">
        <FloatLabel>
          <component
            :is="getFieldComponent(field.type)"
            :id="field.name"
            v-model="formValues[field.name]"
            :type="field.type"
            :invalid="!!errors[field.name]"
            :readonly="field.readonly"
            :placeholder="field.placeholder"
            v-bind="field.props"
            fluid
          />
          <label :for="field.name">{{ field.label }}</label>
        </FloatLabel>
      </template>

      <!-- Textarea -->
      <template v-else-if="field.type === 'textarea'">
        <FloatLabel>
          <Textarea
            :id="field.name"
            v-model="formValues[field.name]"
            :invalid="!!errors[field.name]"
            :readonly="field.readonly"
            :rows="field.rows || 3"
            :placeholder="field.placeholder"
            v-bind="field.props"
            fluid
          />
          <label :for="field.name">{{ field.label }}</label>
        </FloatLabel>
      </template>

      <!-- Select/Dropdown -->
      <template v-else-if="field.type === 'select'">
        <FloatLabel>
          <Select
            :id="field.name"
            v-model="formValues[field.name]"
            :options="field.options"
            :option-label="field.optionLabel || 'label'"
            :option-value="field.optionValue || 'value'"
            :invalid="!!errors[field.name]"
            :readonly="field.readonly"
            :placeholder="field.placeholder"
            v-bind="field.props"
            fluid
          />
          <label :for="field.name">{{ field.label }}</label>
        </FloatLabel>
      </template>

      <!-- Checkbox -->
      <template v-else-if="field.type === 'checkbox'">
        <div class="flex align-items-center">
          <Checkbox
            :id="field.name"
            v-model="formValues[field.name]"
            :invalid="!!errors[field.name]"
            :readonly="field.readonly"
            :binary="true"
            v-bind="field.props"
          />
          <label :for="field.name" class="ml-2">{{ field.label }}</label>
        </div>
      </template>

      <!-- Number -->
      <template v-else-if="field.type === 'number'">
        <FloatLabel>
          <InputNumber
            :id="field.name"
            v-model="formValues[field.name]"
            :invalid="!!errors[field.name]"
            :readonly="field.readonly"
            :min="field.min"
            :max="field.max"
            :step="field.step"
            :currency="field.currency"
            :mode="field.mode || 'decimal'"
            v-bind="field.props"
            fluid
          />
          <label :for="field.name">{{ field.label }}</label>
        </FloatLabel>
      </template>

      <!-- File Upload -->
      <template v-else-if="field.type === 'file'">
        <div class="file-upload-field">
          <label class="form-label">{{ field.label }}</label>
          <FileUpload
            :id="field.name"
            :multiple="field.multiple"
            :accept="field.accept"
            :max-file-size="field.maxFileSize"
            :invalid="!!errors[field.name]"
            @upload="handleFileUpload($event, field.name)"
            v-bind="field.props"
          />
        </div>
      </template>

      <!-- Error Message -->
      <small v-if="errors[field.name]" class="p-error">
        {{ errors[field.name] }}
      </small>

      <!-- Help Text -->
      <small v-if="field.help && !errors[field.name]" class="form-help">
        {{ field.help }}
      </small>
    </div>

    <!-- Form Actions -->
    <div class="form-actions" v-if="!hideActions">
      <slot name="actions" :isSubmitting="isSubmitting" :isValid="!Object.keys(errors).length">
        <Button
          type="submit"
          :label="submitLabel"
          :loading="isSubmitting"
          :disabled="isSubmitting"
          icon="pi pi-check"
        />
        
        <Button
          v-if="showCancelButton"
          type="button"
          :label="cancelLabel"
          severity="secondary"
          outlined
          @click="$emit('cancel')"
        />
      </slot>
    </div>
  </form>
</template>

<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { useForm } from 'vee-validate'
import { toTypedSchema } from '@vee-validate/zod'
import type { ZodSchema } from 'zod'

// PrimeVue компоненты
import InputText from 'primevue/inputtext'
import Password from 'primevue/password'
import Textarea from 'primevue/textarea'
import Select from 'primevue/select'
import Checkbox from 'primevue/checkbox'
import InputNumber from 'primevue/inputnumber'
import FileUpload from 'primevue/fileupload'
import Button from 'primevue/button'
import FloatLabel from 'primevue/floatlabel'

export interface SmartFormField {
  name: string
  type: 'text' | 'email' | 'password' | 'textarea' | 'select' | 'checkbox' | 'number' | 'file'
  label: string
  placeholder?: string
  help?: string
  readonly?: boolean
  required?: boolean
  
  // Select specific
  options?: Array<{ label: string; value: any }>
  optionLabel?: string
  optionValue?: string
  
  // Number specific
  min?: number
  max?: number
  step?: number
  currency?: string
  mode?: 'decimal' | 'currency'
  
  // Textarea specific
  rows?: number
  
  // File specific
  multiple?: boolean
  accept?: string
  maxFileSize?: number
  
  // Additional props
  props?: Record<string, any>
}

interface Props {
  fields: SmartFormField[]
  validationSchema: ZodSchema
  initialValues?: Record<string, any>
  submitLabel?: string
  cancelLabel?: string
  showCancelButton?: boolean
  hideActions?: boolean
}

interface Emits {
  (e: 'submit', values: Record<string, any>): void
  (e: 'cancel'): void
  (e: 'change', values: Record<string, any>): void
}

const props = withDefaults(defineProps<Props>(), {
  submitLabel: 'Сохранить',
  cancelLabel: 'Отмена',
  showCancelButton: false,
  hideActions: false
})

const emit = defineEmits<Emits>()

// VeeValidate форма с Zod схемой
const { handleSubmit, defineField, errors, values } = useForm({
  validationSchema: toTypedSchema(props.validationSchema),
  initialValues: props.initialValues
})

const isSubmitting = ref(false)

// Реактивные поля формы
const formValues = ref<Record<string, any>>({})

// Создаем поля динамически
props.fields.forEach(field => {
  const [fieldValue] = defineField(field.name)
  formValues.value[field.name] = fieldValue
})

// Компонент для каждого типа поля
const getFieldComponent = (type: string) => {
  switch (type) {
    case 'password':
      return Password
    case 'email':
    case 'text':
    default:
      return InputText
  }
}

// Обработка загрузки файлов
const handleFileUpload = (event: any, fieldName: string) => {
  formValues.value[fieldName] = event.files
  emit('change', values)
}

// Отправка формы
const onSubmit = handleSubmit(async (formData) => {
  isSubmitting.value = true
  
  try {
    emit('submit', formData)
  } finally {
    isSubmitting.value = false
  }
})

// Отслеживание изменений
watch(values, (newValues) => {
  emit('change', newValues)
}, { deep: true })
</script>

<style scoped>
.smart-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.form-field {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-label {
  font-weight: 500;
  color: var(--p-text-color);
  margin-bottom: 0.25rem;
}

.form-help {
  color: var(--p-text-muted-color);
}

.file-upload-field {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  margin-top: 1rem;
  padding-top: 1rem;
  border-top: 1px solid var(--p-content-border-color);
}
</style> 