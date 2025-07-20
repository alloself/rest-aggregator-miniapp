<template>
    <form class="smart-form">
        <slot
            v-for="schemeField in normalizedFields"
            :name="schemeField.key"
            :key="schemeField.uniqueKey"
        >
            <Field
                :name="schemeField.key"
                :validate-on-mount="false"
                :validate-on-blur="false"
                :validate-on-input="false"
                v-slot="{ value, handleChange }"
            >
                <div class="mb-1">
                    <component
                        :is="schemeField.component"
                        :model-value="value"
                        @update:modelValue="handleChange"
                        :invalid="!!getFieldErrors(schemeField)"
                        v-bind="schemeField.props"
                        v-on="schemeField.events"
                    ></component>
                    <div class="h-6 mt-1">
                        <Transition name="error-fade">
                            <Message
                                v-if="getFieldErrors(schemeField)"
                                severity="error"
                                size="small"
                                variant="simple"
                                class="mb-0 text-xs"
                                >{{ getFieldErrors(schemeField) }}</Message
                            >
                        </Transition>
                    </div>
                </div>
            </Field>
        </slot>
    </form>
</template>

<script lang="ts" setup>
import { computed, watch, Transition } from "vue";
import { useForm, Field, type FormContext } from "vee-validate";
import { toTypedSchema } from "@vee-validate/zod";
import { z } from "zod";
import Message from "primevue/message";
import type { ISmartFormField, ISmartFormProps } from "@/shared/types";

const {
    fields = [],
    initialValues = {},
    initialItems = {},
} = defineProps<ISmartFormProps>();

const form = defineModel<FormContext>("form");

const validationSchema = computed(() => {
    const schemaFields: Record<string, any> = {};

    fields.forEach((field) => {
        if (field.rule) {
            schemaFields[field.key] = field.rule;
        }
    });

    if (Object.keys(schemaFields).length === 0) {
        return toTypedSchema(z.object({}));
    }

    return toTypedSchema(z.object(schemaFields));
});

const formContext = useForm({
    validationSchema,
    initialValues,
    keepValuesOnUnmount: true,
    validateOnMount: false,
});
form.value = formContext;

const getInitialItems = (field: ISmartFormField) => {
    return field.key in initialItems
        ? initialItems[field.key]
        : field.props?.initialItems || [];
};

const normalizedFields = computed(() => {
    return fields.map((field, index) => {
        return {
            ...field,
            uniqueKey: `${field.key}-${index}`,
            props: {
                ...(field.props || {}),
                initialItems: getInitialItems(field),
            },
            events: {
                ...(field.events || {}),
            },
        };
    });
});

const getFieldErrors = (field: ISmartFormField) => {
    return formContext.errors.value[field.key] || "";
};

watch(
    () => initialValues,
    (newValues) => {
        if (newValues && Object.keys(newValues).length) {
            formContext.setValues(newValues);
        }
    },
    { deep: true },
);
</script>

<style scoped>
.error-fade-enter-active, .error-fade-leave-active {
    transition: opacity 0.2s ease, transform 0.2s ease;
}

.error-fade-enter-from, .error-fade-leave-to {
    opacity: 0;
    transform: translateY(-4px);
}

.error-fade-enter-to, .error-fade-leave-from {
    opacity: 1;
    transform: translateY(0);
}
</style>
