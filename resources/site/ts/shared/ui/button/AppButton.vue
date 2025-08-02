<template>
  <Button :class="buttonClasses" :severity="variant" :dt="buttonDesignTokens" @click="handleClick">
    <template #icon v-if="$slots.icon">
      <slot name="icon" />
    </template>
    <slot />
  </Button>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import Button from 'primevue/button';
import type { ButtonProps } from 'primevue';
import { designTokens } from '../../../tokens';

interface AppButtonProps extends /* @vue-ignore */ ButtonProps {
  variant?: 'primary' | 'secondary' | 'help';
  size?: 'small' | 'medium' | 'large';
  fullWidth?: boolean;
}

const props = withDefaults(defineProps<AppButtonProps>(), {
  variant: 'primary',
  size: 'medium',
  fullWidth: false,
});

const emit = defineEmits<{
  click: [event: Event];
}>();

const secondaryButtonStyles = {
  background: designTokens.colors.background.secondary,
  hoverBackground: designTokens.colors.background.secondaryHover,
  activeBackground: designTokens.colors.background.secondaryActive,
  borderColor: designTokens.colors.background.secondary,
  hoverBorderColor: designTokens.colors.background.secondaryHover,
  activeBorderColor: designTokens.colors.background.secondaryActive,
  color: designTokens.colors.text.primary,
  hoverColor: designTokens.colors.text.primary,
  activeColor: designTokens.colors.text.primary,
  focusRing: {
    color: designTokens.colors.background.secondary,
    shadow: 'none',
  },
};

const helpButtonStyles = {
  background: designTokens.colors.accent.primary,
  hoverBackground: designTokens.colors.accent.primaryHover,
  activeBackground: designTokens.colors.accent.primaryActive,
  borderColor: designTokens.colors.accent.primary,
  hoverBorderColor: designTokens.colors.accent.primaryHover,
  activeBorderColor: designTokens.colors.accent.primaryActive,
  color: designTokens.colors.text.primary,
  hoverColor: designTokens.colors.text.primary,
  activeColor: designTokens.colors.text.primary,
  focusRing: {
    color: designTokens.colors.accent.primary,
    shadow: 'none',
  },
};

const buttonDesignTokens = computed(() => {
  return {
    borderRadius: '20px',
    iconOnlyWidth: '60px',
    colorScheme: {
      light: {
        root: {
          secondary: secondaryButtonStyles,
          help: helpButtonStyles,
        },
      },
      dark: {
        root: {
          secondary: secondaryButtonStyles,
          help: helpButtonStyles,
        },
      },
    },
  };
});

const buttonClasses = computed(() => {
  const sizeClasses = {
    small: 'h-[40px] px-3 text-sm',
    medium: 'h-12 px-5 text-base',
    large: 'h-14 px-8 text-lg',
  };

  const widthClasses = props.fullWidth ? 'w-full' : '';

  return [sizeClasses[props.size], widthClasses].filter(Boolean).join(' ');
});

const handleClick = (event: Event) => {
  emit('click', event);
};
</script>
