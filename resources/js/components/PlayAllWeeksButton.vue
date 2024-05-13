<script setup>
import {ref, onUnmounted} from "vue";

const emit = defineEmits(['all-weeks-played'])
const timeout = ref();
const submitting = ref(false);
const playNextWeek = async () => {
    return fetch('/api/league/play-next-week', {
            method: 'POST'
        }).then((resp) => resp.json())
            .then((resp) => {
                emit('all-weeks-played', resp);
                return resp
            });
}
const onClick = () => {
    submitting.value = true;
    playNextWeek().then((resp) => {
        if (resp.data.hasAllPlayed) {
            return;
        }

        timeout.value = setTimeout(() => {
            onClick();
        }, 700);
    })
}
onUnmounted(() => {
    if (timeout.value) {
        clearTimeout(timeout.value);
    }
})
</script>

<template>
    <button type="button" @click="onClick" :disabled="submitting">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
            <path
                d="M5.055 7.06C3.805 6.347 2.25 7.25 2.25 8.69v8.122c0 1.44 1.555 2.343 2.805 1.628L12 14.471v2.34c0 1.44 1.555 2.343 2.805 1.628l7.108-4.061c1.26-.72 1.26-2.536 0-3.256l-7.108-4.061C13.555 6.346 12 7.249 12 8.689v2.34L5.055 7.061Z"/>
        </svg>

        <span>Play All Weeks</span>
    </button>
</template>

<style scoped>
button:disabled {
    @apply bg-slate-500;
}
</style>
