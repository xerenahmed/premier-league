<script setup>
const emit = defineEmits(['week-played'])
const onClick = () => {
    fetch('/api/league/play-next-week', {
        method: 'POST'
    }).then((resp) => resp.json())
        .then((resp) => emit('week-played', resp));
}
</script>

<template>
    <button type="button" @click="onClick">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
            <path fill-rule="evenodd" d="M4.5 5.653c0-1.427 1.529-2.33 2.779-1.643l11.54 6.347c1.295.712 1.295 2.573 0 3.286L7.28 19.99c-1.25.687-2.779-.217-2.779-1.643V5.653Z" clip-rule="evenodd" />
        </svg>

        <span>Play Next Week</span>
    </button>
</template>

<style scoped>

</style>
