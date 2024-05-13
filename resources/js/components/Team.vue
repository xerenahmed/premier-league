<script setup>
defineProps(['team', 'imgSize', 'textProps']);
</script>

<template>
    <div class="flex items-center gap-3">
        <slot />
        <img :src="team.flag_url" :height="imgSize ?? 40" :width="imgSize ?? 40" />
        <span v-bind="textProps">{{ team.name }}</span>
    </div>
</template>

<style scoped>

</style>
