<template>
    <button
        @click="$emit('click')"
        class="w-full flex items-center space-x-3 px-3 py-2 text-left rounded-lg hover:bg-gray-50 transition-colors group cursor-pointer"
    >
        <div class="w-8 h-8 rounded-lg bg-gray-100 group-hover:bg-gray-200 flex items-center justify-center transition-colors">
            <span class="text-sm">{{ icon }}</span>
        </div>
        <span class="font-medium text-gray-700 group-hover:text-gray-900">{{ label }}</span>
    </button>
</template>

<script setup lang="ts">
interface Props {
    icon: string
    label: string
}

defineProps<Props>()
defineEmits<{
    click: []
}>()
</script> 