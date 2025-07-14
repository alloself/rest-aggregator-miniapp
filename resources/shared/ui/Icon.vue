<template>
    <component
        v-if="customIconComponent"
        :is="customIconComponent"
        :class="computedClass"
        :style="computedStyle"
        v-bind="$attrs"
    />
    <Icon
        v-else
        :icon="props.name"
        :class="computedClass"
        :style="computedStyle"
        v-bind="$attrs"
    />
</template>

<script setup lang="ts">
import { computed, onErrorCaptured, ref, watch } from "vue";
import { Icon } from "@iconify/vue";
import { customIconMap, type CustomIconName } from "./customIcons";

interface Props {
    name: string | CustomIconName;
    size?: number | string;
    color?: string;
    class?: any;
    style?: any;
    fallback?: boolean; // Показывать fallback при ошибке
}

const props = withDefaults(defineProps<Props>(), {
    size: "1em",
    color: "currentColor",
    fallback: true,
});

const customIconComponent = ref<any>(null);

// Загружаем кастомную иконку если она есть
const loadCustomIcon = async () => {
    try {
        customIconComponent.value = null;

        // Проверяем, есть ли иконка в кастомной карте
        if (props.name in customIconMap) {
            const loader = customIconMap[props.name as CustomIconName];
            const module = await loader();
            customIconComponent.value = module.default;
        }
    } catch (error) {
        console.error(
            `[Icon] Failed to load custom icon: ${props.name}`,
            error,
        );
    }
};

watch(() => props.name, loadCustomIcon, { immediate: true });

const computedClass = computed(() => {
    const classes = [];

    if (props.class) {
        if (typeof props.class === "string") {
            classes.push(props.class);
        } else if (Array.isArray(props.class)) {
            classes.push(...props.class);
        } else {
            classes.push(props.class);
        }
    }

    return classes;
});

const computedStyle = computed(() => {
    const styles: Record<string, any> = {
        fontSize:
            typeof props.size === "number" ? `${props.size}px` : props.size,
        color: props.color,
    };

    if (props.style) {
        if (typeof props.style === "string") {
            // Парсим CSS строку (простой случай)
            const cssRules = props.style
                .split(";")
                .filter((rule) => rule.trim());
            cssRules.forEach((rule) => {
                const [property, value] = rule.split(":").map((s) => s.trim());
                if (property && value) {
                    styles[property] = value;
                }
            });
        } else if (typeof props.style === "object") {
            Object.assign(styles, props.style);
        }
    }

    return styles;
});

// Обработка ошибок загрузки иконок
onErrorCaptured((error) => {
    console.error(`[Icon] Failed to load icon: ${props.name}`, error);

    return false; // Предотвращаем всплытие ошибки
});
</script>

<style scoped>
/* Базовые стили иконки */
.icon {
    display: inline-block;
    line-height: 1;
    vertical-align: middle;
}
</style>
