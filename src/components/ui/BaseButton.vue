<script setup>
import { computed } from 'vue'

const props = defineProps({
  variant: {
    type: String,
    default: 'light',
    validator: (v) =>
      ['light', 'primary', 'secondary', 'outline-light', 'outline-primary'].includes(v),
  },
  size: {
    type: String,
    default: 'md',
    validator: (v) => ['sm', 'md', 'lg'].includes(v),
  },
  shape: {
    type: String,
    default: 'default',
    validator: (v) => ['default', 'pill', 'circle'].includes(v),
  },
  href: {
    type: String,
    default: null,
  },
  type: {
    type: String,
    default: 'button',
  },
  loading: {
    type: Boolean,
    default: false,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  block: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['click'])

const buttonClasses = computed(() => {
  const classes = ['btn']

  // Variant
  if (props.variant.includes('outline')) {
    classes.push(`btn-${props.variant}`)
  } else if (props.variant === 'light') {
    classes.push('btn-light-custom')
  } else {
    classes.push(`btn-${props.variant}`)
  }

  // Size
  const sizeMap = { sm: 'btn-sm', md: '', lg: 'btn-lg' }
  if (sizeMap[props.size]) classes.push(sizeMap[props.size])

  // Shape
  if (props.shape === 'pill') classes.push('rounded-pill')
  if (props.shape === 'circle') classes.push('rounded-circle')

  // Block
  if (props.block) classes.push('w-100')

  return classes
})

const circleStyle = computed(() => {
  if (props.shape === 'circle') {
    const sizeMap = { sm: '32px', md: '48px', lg: '56px' }
    const size = sizeMap[props.size]
    return {
      width: size,
      height: size,
      display: 'inline-flex',
      alignItems: 'center',
      justifyContent: 'center',
      padding: '0',
    }
  }
  return {}
})

function handleClick(event) {
  if (!props.disabled && !props.loading) {
    emit('click', event)
  }
}
</script>

<template>
  <component
    :is="href ? 'a' : 'button'"
    :class="buttonClasses"
    :style="shape === 'circle' ? circleStyle : {}"
    :href="href"
    :type="href ? null : type"
    :disabled="disabled || loading"
    @click="handleClick"
  >
    <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
    <slot />
  </component>
</template>

<style scoped>
.btn-light-custom {
  background-color: #ffffff;
  color: #1a1a1a;
  font-family: 'Inter', sans-serif;
  border: none;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  text-decoration: none;
}

.btn-light-custom:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
  background-color: #f8f9fa;
  color: #000000;
}

.btn-light-custom:active:not(:disabled) {
  transform: translateY(1px);
}

.btn-light-custom:disabled {
  opacity: 0.65;
  cursor: not-allowed;
}

.btn-outline-light {
  background-color: transparent;
  color: #ffffff;
  border: 2px solid #ffffff;
  transition: all 0.3s ease;
}

.btn-outline-light:hover:not(:disabled) {
  background-color: #ffffff;
  color: #1a1a1a;
  transform: translateY(-2px);
}
</style>
