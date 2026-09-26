<script setup>
defineProps({
    items: { type: Array, required: true },
    modelValue: { type: String, required: true },
    label: { type: String, required: true },
});

const emit = defineEmits(['update:modelValue']);
</script>

<template>
    <nav :aria-label="label" class="flex gap-1 overflow-x-auto md:flex-col md:overflow-visible">
        <button
            v-for="item in items"
            :key="item.key"
            type="button"
            class="flex h-9 shrink-0 items-center rounded-lg px-3 text-left text-sm font-medium transition-colors"
            :class="
                modelValue === item.key
                    ? 'bg-gray-100 text-primary-600 dark:bg-white/5 dark:text-primary-400'
                    : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-white'
            "
            :aria-current="modelValue === item.key ? 'page' : undefined"
            @click="emit('update:modelValue', item.key)"
        >
            {{ item.label }}
        </button>
    </nav>
</template>
