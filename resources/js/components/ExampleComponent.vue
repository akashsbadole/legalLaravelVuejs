<template>
    <div class="p-6 bg-white rounded-lg shadow">
        <h2 class="text-xl font-bold mb-4">{{ title }}</h2>
        <p class="text-gray-600">Count: {{ count }}</p>
        <button @click="increment" class="mt-2 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
            Increment
        </button>
    </div>
</template>

<script setup>
import { ref } from 'vue';

defineProps({
    title: {
        type: String,
        default: 'Vue Counter'
    }
});

const count = ref(0);

function increment() {
    count.value++;
}
</script>
