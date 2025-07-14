<template>
    <Card class="stat-card">
        <template #content>
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="text-sm font-medium text-gray-500 uppercase tracking-wide">
                        {{ title }}
                    </div>
                    <div class="mt-1 text-3xl font-bold text-gray-900">
                        {{ value }}
                    </div>
                    <div v-if="description" class="mt-1 text-sm text-gray-600">
                        {{ description }}
                    </div>
                </div>
                <div v-if="icon" class="flex-shrink-0">
                    <div 
                        class="w-12 h-12 rounded-lg flex items-center justify-center"
                        :class="iconBackgroundClass"
                    >
                        <Icon 
                            :name="icon" 
                            size="24px" 
                            :color="iconColor" 
                        />
                    </div>
                </div>
            </div>
            
            <div v-if="trend" class="mt-3 flex items-center">
                <div class="flex items-center">
                    <Icon 
                        :name="trendIconName" 
                        size="14px" 
                        :color="trendColor"
                        class="mr-1"
                    />
                    <span 
                        class="text-sm font-medium"
                        :style="{ color: trendColor }"
                    >
                        {{ Math.abs(trend.value) }}{{ trend.unit }}
                    </span>
                </div>
                <span class="text-sm text-gray-500 ml-2">
                    {{ trend.period }}
                </span>
            </div>
        </template>
    </Card>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import Card from 'primevue/card';
import Icon from '@shared/ui/Icon.vue';

interface TrendData {
    value: number;
    unit: string;
    period: string;
}

interface Props {
    title: string;
    value: string | number;
    description?: string;
    icon?: string;
    iconColor?: string;
    trend?: TrendData;
}

const props = withDefaults(defineProps<Props>(), {
    iconColor: '#6B7280'
});

const iconBackgroundClass = computed(() => {
    return 'bg-gray-100';
});

const trendIconName = computed(() => {
    if (!props.trend) return '';
    return props.trend.value >= 0 ? 'mdi:arrow-up' : 'mdi:arrow-down';
});

const trendColor = computed(() => {
    if (!props.trend) return '';
    return props.trend.value >= 0 ? '#10B981' : '#EF4444';
});
</script>

<style scoped>
.stat-card :deep(.p-card-body) {
    padding: 1rem;
}

.stat-card :deep(.p-card-content) {
    padding: 0;
}
</style> 