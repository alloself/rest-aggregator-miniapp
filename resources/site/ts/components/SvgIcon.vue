<template>
  <div 
    class="svg-icon"
    :class="[
      `svg-icon--${size}`,
      colorClass && `svg-icon--${colorClass}`
    ]"
    :style="customStyle"
    v-html="svgContent"
  />
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'

interface Props {
  name: string
  size?: 'sm' | 'md' | 'lg' | 'custom'
  customSize?: number
  color?: 'black' | 'white' | 'primary' | 'telegram' | 'custom'
  customColor?: string
}

const props = withDefaults(defineProps<Props>(), {
  size: 'md',
  color: 'black'
})

// Reactive data
const svgContent = ref('')

// Computed
const colorClass = computed(() => {
  if (props.color === 'custom') return null
  return props.color
})

const customStyle = computed(() => {
  const style: Record<string, string> = {}
  
  if (props.size === 'custom' && props.customSize) {
    style.width = `${props.customSize}px`
    style.height = `${props.customSize}px`
  }
  
  if (props.color === 'custom' && props.customColor) {
    style.color = props.customColor
  }
  
  return style
})

// Methods
const loadSvg = async () => {
  try {
    const response = await fetch(`/resources/site/assets/icons/${props.name}.svg`)
    if (response.ok) {
      svgContent.value = await response.text()
    } else {
    }
  } catch (error) {
  }
}

// Lifecycle
onMounted(() => {
  loadSvg()
})
</script>

<style>
.svg-icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  
  /* Sizes */
  &--sm {
    width: 16px;
    height: 16px;
  }
  
  &--md {
    width: 20px;
    height: 20px;
  }
  
  &--lg {
    width: 24px;
    height: 24px;
  }
  
  /* Colors */
  &--black {
    color: #000000;
  }
  
  &--white {
    color: #ffffff;
  }
  
  &--primary {
    color: #2AABEE;
  }
  
  &--telegram {
    color: #2AABEE;
  }
  
  /* Ensure SVG inherits color */
  & svg {
    width: 100%;
    height: 100%;
  }
  
  & svg path[fill="black"],
  & svg circle[fill="black"],
  & svg rect[fill="black"] {
    fill: currentColor;
  }
  
  & svg path[stroke="black"],
  & svg circle[stroke="black"],
  & svg rect[stroke="black"] {
    stroke: currentColor;
  }
  
  /* Preserve gradient fills like telegram */
  & svg path[fill="white"] {
    fill: white;
  }
}
</style> 