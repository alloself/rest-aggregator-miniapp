<template>
    <component
        :is="tag"
        :to="to"
        class="flex items-center space-x-3 px-4 py-2 text-sm font-medium transition-colors cursor-pointer"
        :class="computedClass"
        @click="handleClick"
    >
        <Icon 
            v-if="icon" 
            :name="icon" 
            size="16px" 
            class="flex-shrink-0" 
        />
        <span class="flex-1">{{ label }}</span>
        <Icon 
            v-if="showArrow" 
            name="mdi:chevron-right" 
            size="12px" 
            color="#9CA3AF" 
            class="flex-shrink-0" 
        />
    </component>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import Icon from '@shared/ui/Icon.vue';

interface Props {
    icon?: string;
    label: string;
    to?: string;
    class?: string;
    showArrow?: boolean;
}

interface Emits {
    (e: 'click', event: Event): void;
}

const props = withDefaults(defineProps<Props>(), {
    showArrow: false
});

const emit = defineEmits<Emits>();

const tag = computed(() => {
    return props.to ? 'RouterLink' : 'div';
});

const computedClass = computed(() => {
    const baseClasses = 'hover:bg-gray-100';
    return props.class ? `${baseClasses} ${props.class}` : baseClasses;
});

const handleClick = (event: Event) => {
    emit('click', event);
};
</script>

<style scoped>
/* Additional styles if needed */
</style> 